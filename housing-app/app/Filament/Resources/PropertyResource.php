<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\PropertyManagement;
use App\Filament\Resources\PropertyResource\Pages;
use App\Models\Property;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $cluster = PropertyManagement::class;

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Thông tin địa chỉ')
                            ->schema([
                                Forms\Components\TextInput::make('address')
                                    ->label('Địa chỉ')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('city')
                                    ->label('Thành phố')
                                    ->required()
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('district')
                                    ->label('Quận/Huyện')
                                    ->maxLength(100),
                            ])->columns(3),
                    ]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Thông tin bất động sản')
                            ->schema([
                                Forms\Components\TextInput::make('area')
                                    ->label('Diện tích (m²)')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0),
                                Forms\Components\TextInput::make('frontage')
                                    ->label('Mặt tiền (m)')
                                    ->numeric()
                                    ->minValue(0),
                                Forms\Components\TextInput::make('access_road')
                                    ->label('Đường vào (m)')
                                    ->numeric()
                                    ->minValue(0),
                                Forms\Components\TextInput::make('floors')
                                    ->label('Số tầng')
                                    ->numeric()
                                    ->minValue(1),
                                Forms\Components\TextInput::make('bedrooms')
                                    ->label('Số phòng ngủ')
                                    ->numeric()
                                    ->minValue(0),
                                Forms\Components\TextInput::make('bathrooms')
                                    ->label('Số phòng tắm')
                                    ->numeric()
                                    ->minValue(0),
                            ])->columns(3),
                    ]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Hướng')
                            ->schema([
                                Forms\Components\Select::make('house_direction')
                                    ->label('Hướng nhà')
                                    ->options([
                                        'Đông' => 'Đông',
                                        'Tây' => 'Tây',
                                        'Nam' => 'Nam',
                                        'Bắc' => 'Bắc',
                                        'Đông Bắc' => 'Đông Bắc',
                                        'Đông Nam' => 'Đông Nam',
                                        'Tây Bắc' => 'Tây Bắc',
                                        'Tây Nam' => 'Tây Nam',
                                    ]),
                                Forms\Components\Select::make('balcony_direction')
                                    ->label('Hướng ban công')
                                    ->options([
                                        'Đông' => 'Đông',
                                        'Tây' => 'Tây',
                                        'Nam' => 'Nam',
                                        'Bắc' => 'Bắc',
                                        'Đông Bắc' => 'Đông Bắc',
                                        'Đông Nam' => 'Đông Nam',
                                        'Tây Bắc' => 'Tây Bắc',
                                        'Tây Nam' => 'Tây Nam',
                                    ]),
                            ])->columns(2),
                    ]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Pháp lý & Nội thất')
                            ->schema([
                                Forms\Components\Select::make('legal_status')
                                    ->label('Tình trạng pháp lý')
                                    ->options([
                                        'Đã có sổ' => 'Đã có sổ',
                                        'Đang chờ sổ' => 'Đang chờ sổ',
                                        'Sổ hồng' => 'Sổ hồng',
                                        'Sổ đỏ' => 'Sổ đỏ',
                                        'Giấy tờ khác' => 'Giấy tờ khác',
                                    ]),
                                Forms\Components\Select::make('furniture_state')
                                    ->label('Tình trạng nội thất')
                                    ->options([
                                        'Đầy đủ' => 'Đầy đủ',
                                        'Cơ bản' => 'Cơ bản',
                                        'Cao cấp' => 'Cao cấp',
                                        'Bán hoàn thiện' => 'Bán hoàn thiện',
                                        'Chưa hoàn thiện' => 'Chưa hoàn thiện',
                                    ]),
                            ])->columns(2),
                    ]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Giá & Phân khúc')
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->label('Giá (VNĐ)')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefix('₫'),
                                Forms\Components\Select::make('price_segment')
                                    ->label('Phân khúc giá')
                                    ->options([
                                        'Low Price' => 'Low Price',
                                        'Medium Price' => 'Medium Price',
                                        'High Price' => 'High Price',
                                    ])
                                    ->default('Medium Price'),
                            ])->columns(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Địa chỉ')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('city')
                    ->label('Thành phố')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('district')
                    ->label('Quận/Huyện')
                    ->searchable(),
                Tables\Columns\TextColumn::make('area')
                    ->label('Diện tích')
                    ->numeric()
                    ->suffix(' m²')
                    ->sortable(),
                Tables\Columns\TextColumn::make('floors')
                    ->label('Tầng')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bedrooms')
                    ->label('PN')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bathrooms')
                    ->label('PT')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('legal_status')
                    ->label('Pháp lý')
                    ->sortable(),
                Tables\Columns\TextColumn::make('furniture_state')
                    ->label('Nội thất')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Giá')
                    ->numeric()
                    ->money('VND')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('price_segment')
                    ->label('Phân khúc')
                    ->colors([
                        'success' => 'High Price',
                        'warning' => 'Medium Price',
                        'danger' => 'Low Price',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('city')
                    ->label('Thành phố')
                    ->searchable(),
                Tables\Filters\SelectFilter::make('price_segment')
                    ->label('Phân khúc giá')
                    ->options([
                        'Low Price' => 'Low Price',
                        'Medium Price' => 'Medium Price',
                        'High Price' => 'High Price',
                    ]),
                Tables\Filters\SelectFilter::make('legal_status')
                    ->label('Tình trạng pháp lý'),
                Tables\Filters\SelectFilter::make('furniture_state')
                    ->label('Tình trạng nội thất'),
                Tables\Filters\Filter::make('price_range')
                    ->form([
                        Forms\Components\TextInput::make('min_price')
                            ->label('Giá tối thiểu')
                            ->numeric(),
                        Forms\Components\TextInput::make('max_price')
                            ->label('Giá tối đa')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_price'],
                                fn(Builder $query): Builder => $query->where('price', '>=', $data['min_price']),
                            )
                            ->when(
                                $data['max_price'],
                                fn(Builder $query): Builder => $query->where('price', '<=', $data['max_price']),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'view' => Pages\ViewProperty::route('/{record}'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }
}
