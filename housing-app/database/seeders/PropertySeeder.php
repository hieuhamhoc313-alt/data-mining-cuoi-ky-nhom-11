<?php

namespace Database\Seeders;

use App\Models\Property;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    protected array $provinces = [];
    protected array $provinceLookup = [];
    protected array $wardLookup = [];

    public function run(): void
    {
        $csvPath = base_path('vietnam_housing_dataset.csv');

        if (!file_exists($csvPath)) {
            $this->command->error('CSV file not found: ' . $csvPath);
            return;
        }

        $this->loadLocationReferenceData();
        $this->command->info('Loading data from CSV...');

        $handle = fopen($csvPath, 'r');

        $header = fgetcsv($handle);

        $header = array_map(function ($col) {
            return trim(preg_replace('/^[\x{feff}]/u', '', $col));
        }, $header);

        $batchSize = 100;
        $batch = [];
        $totalRecords = 0;
        $skippedRecords = 0;
        $rowNumber = 1;

        while (($data = fgetcsv($handle)) !== false) {
            $rowNumber++;

            if (count($data) !== count($header)) {
                $skippedRecords++;
                continue;
            }

            $record = array_combine($header, $data);

            if (!$this->isValidRecord($record)) {
                $skippedRecords++;
                continue;
            }

            $property = $this->preparePropertyData($record);
            $batch[] = $property;

            if (count($batch) >= $batchSize) {
                Property::insert($batch);
                $totalRecords += count($batch);
                $batch = [];

                $this->command->info("Processed {$totalRecords} records...");
            }
        }

        if (count($batch) > 0) {
            Property::insert($batch);
            $totalRecords += count($batch);
        }

        fclose($handle);

        $this->command->info('Import completed!');
        $this->command->info("Total records imported: {$totalRecords}");
        $this->command->info("Records skipped: {$skippedRecords}");

        if ($totalRecords > 0) {
            $this->calculatePriceSegments();
        }
    }

    protected function loadLocationReferenceData(): void
    {
        $provincesPath = base_path('../provinces.json');
        $wardsPath = base_path('../wards.json');

        $this->provinces = $this->decodeJsonFile($provincesPath);
        $this->provinceLookup = [];
        $this->wardLookup = [];

        foreach ($this->provinces as $province) {
            $canonicalName = $province['name'] ?? null;

            if (!$canonicalName) {
                continue;
            }

            $variants = array_filter([
                $province['name'] ?? null,
                $province['fullName'] ?? null,
                $province['slug'] ?? null,
            ]);

            foreach ($variants as $variant) {
                $this->provinceLookup[$this->normalizeText($variant)] = $canonicalName;
            }

            if ($canonicalName === 'Hồ Chí Minh') {
                foreach (['TPHCM', 'TP HCM', 'TP. HCM', 'TP HO CHI MINH', 'SAI GON', 'SÀI GÒN', 'HO CHI MINH', 'HCM'] as $alias) {
                    $this->provinceLookup[$this->normalizeText($alias)] = $canonicalName;
                }
            }

            if ($canonicalName === 'Hà Nội') {
                foreach (['TP HANOI', 'TP. HANOI', 'HA NOI', 'HANOI'] as $alias) {
                    $this->provinceLookup[$this->normalizeText($alias)] = $canonicalName;
                }
            }

            if ($canonicalName === 'Đà Nẵng') {
                foreach (['DA NANG', 'TP DA NANG', 'TP. DA NANG'] as $alias) {
                    $this->provinceLookup[$this->normalizeText($alias)] = $canonicalName;
                }
            }
        }

        foreach ($this->decodeJsonFile($wardsPath) as $ward) {
            $provinceCode = $ward['provinceCode'] ?? null;
            $province = $this->findProvinceByCode($provinceCode);

            if (!$province || empty($ward['name'])) {
                continue;
            }

            $provinceName = $province['name'];
            $wardName = trim($ward['name']);
            $fullName = trim($ward['fullName'] ?? '');
            $segments = array_map('trim', array_filter(explode(',', $fullName)));

            $districtName = null;
            if (count($segments) >= 3) {
                $districtName = $segments[count($segments) - 2];
            }

            if (!$districtName && count($segments) >= 2) {
                $districtName = $segments[count($segments) - 1];
            }

            $entry = [
                'province' => $provinceName,
                'district' => $districtName,
                'ward' => $wardName,
                'type' => $ward['type'] ?? null,
                'full_name' => $fullName,
            ];

            $keys = array_filter([
                $wardName,
                $fullName,
                $ward['slug'] ?? null,
                $districtName ? $wardName . '|' . $districtName : null,
                $districtName ? $fullName . '|' . $districtName : null,
            ]);

            foreach ($keys as $key) {
                $normalizedKey = $this->normalizeText($key);
                $this->wardLookup[$normalizedKey] ??= [];
                $this->wardLookup[$normalizedKey][] = $entry;
            }
        }
    }

    protected function decodeJsonFile(string $path): array
    {
        if (!file_exists($path)) {
            return [];
        }

        $content = file_get_contents($path);
        $decoded = json_decode($content, true);

        return is_array($decoded) ? $decoded : [];
    }

    protected function isValidRecord(array $record): bool
    {
        $address = trim($record['Address'] ?? '');
        $area = trim($record['Area'] ?? '');
        $price = trim($record['Price'] ?? '');

        if (empty($address) || empty($area) || empty($price)) {
            return false;
        }

        $areaValue = floatval($area);
        $priceValue = floatval($price);

        if ($areaValue <= 0 || $priceValue <= 0) {
            return false;
        }

        return true;
    }

    protected function preparePropertyData(array $record): array
    {
        $location = $this->extractLocationParts($record['Address'] ?? '');

        return [
            'address' => trim($record['Address'] ?? ''),
            'city' => $location['city'],
            'district' => $location['district'],
            'ward' => $location['ward'],
            'area' => floatval($record['Area'] ?? 0),
            'frontage' => $this->parseFloat($record['Frontage'] ?? null),
            'access_road' => $this->parseFloat($record['Access Road'] ?? null),
            'house_direction' => $this->normalizeDirection($record['House direction'] ?? null),
            'balcony_direction' => $this->normalizeDirection($record['Balcony direction'] ?? null),
            'floors' => $this->parseInt($record['Floors'] ?? null),
            'bedrooms' => $this->parseInt($record['Bedrooms'] ?? null),
            'bathrooms' => $this->parseInt($record['Bathrooms'] ?? null),
            'legal_status' => $this->normalizeLegalStatus($record['Legal status'] ?? 'Other'),
            'furniture_state' => $this->normalizeFurniture($record['Furniture state'] ?? null),
            'price' => floatval($record['Price'] ?? 0),
            'price_segment' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    protected function normalizeDirection(?string $direction): ?string
    {
        if (empty($direction)) {
            return null;
        }

        $direction = trim($direction);

        $validDirections = [
            'Đông', 'Tây', 'Nam', 'Bắc',
            'Đông - Bắc', 'Đông - Nam', 'Tây - Bắc', 'Tây - Nam',
            'Đông-Bắc', 'Đông-Nam', 'Tây-Bắc', 'Tây-Nam',
        ];

        foreach ($validDirections as $valid) {
            if (strcasecmp($direction, $valid) === 0) {
                return $valid;
            }
        }

        return null;
    }

    protected function normalizeLegalStatus(string $status): string
    {
        $status = trim($status);

        $validStatuses = ['Have certificate', 'Sale contract', 'Pending', 'Other'];

        foreach ($validStatuses as $valid) {
            if (strcasecmp($status, $valid) === 0) {
                return $valid;
            }
        }

        return 'Other';
    }

    protected function normalizeFurniture(?string $furniture): ?string
    {
        if (empty($furniture)) {
            return null;
        }

        $furniture = trim($furniture);

        $validStates = ['Full', 'Basic', 'Empty'];

        foreach ($validStates as $valid) {
            if (strcasecmp($furniture, $valid) === 0) {
                return $valid;
            }
        }

        return null;
    }

    protected function extractLocationParts(string $address): array
    {
        $segments = $this->extractAddressSegments($address);
        $city = $this->extractCityFromSegments($segments) ?? 'Khác';
        $district = $this->extractDistrictFromSegments($segments, $city) ?? 'Chưa xác định';
        $ward = $this->extractWardFromSegments($segments, $city, $district) ?? 'Chưa xác định';

        return [
            'city' => $city,
            'district' => $district,
            'ward' => $ward,
        ];
    }

    protected function extractAddressSegments(string $address): array
    {
        $address = trim(preg_replace('/\s+/', ' ', $address));

        if ($address === '') {
            return [];
        }

        return array_values(array_filter(array_map(function ($segment) {
            $segment = trim($segment, " \t\n\r\0\x0B.");
            return $segment === '' ? null : $segment;
        }, explode(',', $address))));
    }

    protected function extractCityFromSegments(array $segments): ?string
    {
        for ($i = count($segments) - 1; $i >= 0; $i--) {
            $normalized = $this->normalizeText($segments[$i]);
            if (isset($this->provinceLookup[$normalized])) {
                return $this->provinceLookup[$normalized];
            }
        }

        foreach ($segments as $segment) {
            $normalizedSegment = $this->normalizeText($segment);
            foreach ($this->provinceLookup as $normalizedProvince => $canonicalName) {
                if ($normalizedProvince !== '' && str_contains($normalizedSegment, $normalizedProvince)) {
                    return $canonicalName;
                }
            }
        }

        return null;
    }

    protected function extractDistrictFromSegments(array $segments, ?string $city): ?string
    {
        if (count($segments) < 2) {
            return null;
        }

        $cityNormalized = $city ? $this->normalizeText($city) : null;

        for ($i = count($segments) - 1; $i >= 0; $i--) {
            $segment = $segments[$i];
            $normalized = $this->normalizeText($segment);

            if ($cityNormalized && isset($this->provinceLookup[$normalized])) {
                continue;
            }

            if ($this->looksLikeWardSegment($segment)) {
                continue;
            }

            if ($this->looksLikeDistrictSegment($segment)) {
                return $this->stripLocationPrefix($segment);
            }
        }

        for ($i = count($segments) - 2; $i >= 0; $i--) {
            $segment = $segments[$i];

            if ($this->looksLikeStreetSegment($segment) || $this->looksLikeWardSegment($segment)) {
                continue;
            }

            $candidate = $this->stripLocationPrefix($segment);
            if ($candidate !== '') {
                return $candidate;
            }
        }

        return null;
    }

    protected function extractWardFromSegments(array $segments, ?string $city, ?string $district): ?string
    {
        foreach ($segments as $segment) {
            if ($this->looksLikeWardSegment($segment)) {
                return $this->stripLocationPrefix($segment);
            }
        }

        $districtNormalized = $district ? $this->normalizeText($district) : null;
        $cityNormalized = $city ? $this->normalizeText($city) : null;

        foreach ($segments as $segment) {
            $normalized = $this->normalizeText($segment);
            $candidates = $this->wardLookup[$normalized] ?? [];

            foreach ($candidates as $candidate) {
                if ($cityNormalized && $this->normalizeText($candidate['province']) !== $cityNormalized) {
                    continue;
                }

                if ($districtNormalized && !empty($candidate['district']) && $this->normalizeText($candidate['district']) !== $districtNormalized) {
                    continue;
                }

                return $candidate['ward'];
            }
        }

        return null;
    }

    protected function looksLikeWardSegment(string $segment): bool
    {
        return preg_match('/^(phuong|xa|thi tran|tt\.?|p\.?|x\.?)/iu', $this->normalizeText($segment)) === 1;
    }

    protected function looksLikeDistrictSegment(string $segment): bool
    {
        return preg_match('/^(quan|huyen|thi xa|thanh pho|tp\.?|q\.?)/iu', $this->normalizeText($segment)) === 1;
    }

    protected function looksLikeStreetSegment(string $segment): bool
    {
        return preg_match('/^(duong|pho|ngo|hem|hxh|ap|du an|khu|ql|quoc lo|tinh lo|dt\.?)/iu', $this->normalizeText($segment)) === 1;
    }

    protected function stripLocationPrefix(string $value): string
    {
        $value = trim($value);
        $value = preg_replace('/^(Phường|Xã|Thị trấn|Thị xã|Quận|Huyện|Thành phố|TP\.?|P\.?|Q\.?|TT\.?)\s+/iu', '', $value);

        return trim($value ?? '');
    }

    protected function normalizeText(?string $value): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }

        $value = mb_strtolower($value, 'UTF-8');
        $transliterationMap = [
            'à' => 'a', 'á' => 'a', 'ạ' => 'a', 'ả' => 'a', 'ã' => 'a',
            'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ậ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a',
            'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ặ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a',
            'è' => 'e', 'é' => 'e', 'ẹ' => 'e', 'ẻ' => 'e', 'ẽ' => 'e',
            'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ệ' => 'e', 'ể' => 'e', 'ễ' => 'e',
            'ì' => 'i', 'í' => 'i', 'ị' => 'i', 'ỉ' => 'i', 'ĩ' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ọ' => 'o', 'ỏ' => 'o', 'õ' => 'o',
            'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ộ' => 'o', 'ổ' => 'o', 'ỗ' => 'o',
            'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ợ' => 'o', 'ở' => 'o', 'ỡ' => 'o',
            'ù' => 'u', 'ú' => 'u', 'ụ' => 'u', 'ủ' => 'u', 'ũ' => 'u',
            'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ự' => 'u', 'ử' => 'u', 'ữ' => 'u',
            'ỳ' => 'y', 'ý' => 'y', 'ỵ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y',
            'đ' => 'd',
        ];

        $value = strtr($value, $transliterationMap);
        $value = preg_replace('/[^a-z0-9]+/u', ' ', $value);

        return trim($value ?? '');
    }

    protected function findProvinceByCode(?string $code): ?array
    {
        if (!$code) {
            return null;
        }

        foreach ($this->provinces as $province) {
            if (($province['code'] ?? null) === $code) {
                return $province;
            }
        }

        return null;
    }

    protected function parseFloat($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return floatval($value);
    }

    protected function parseInt($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return intval($value);
    }

    protected function calculatePriceSegments(): void
    {
        $this->command->info('Calculating price segments...');

        $prices = Property::pluck('price')->toArray();

        if (count($prices) === 0) {
            $this->command->warn('No properties found to calculate segments.');
            return;
        }

        sort($prices);
        $count = count($prices);

        $lowThreshold = $prices[(int) floor($count * 0.33)];
        $highThreshold = $prices[(int) floor($count * 0.67)];

        Property::chunk(500, function ($properties) use ($lowThreshold, $highThreshold) {
            foreach ($properties as $property) {
                if ($property->price < $lowThreshold) {
                    $segment = 'Low';
                } elseif ($property->price < $highThreshold) {
                    $segment = 'Medium';
                } else {
                    $segment = 'High';
                }
                $property->update(['price_segment' => $segment]);
            }
        });

        $this->command->info('Price segments calculated successfully!');
    }
}
