@extends('layouts.app')

@section('title', 'Prediction - Vietnam Real Estate Analytics')
@section('page-title', 'Price Prediction')

@section('content')
<div class="container-fluid">
    <div class="row g-4">
        {{-- Prediction Form --}}
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-cpu me-2"></i>Nhập thông tin BĐS
                    </h5>
                    <small class="text-muted">Diện tích, số tầng, số phòng ngủ...</small>
                </div>
                <div class="card-body">
                    <form id="predictionForm">
                        @csrf

                        <div class="mb-3">
                            <label for="area" class="form-label">
                                Diện tích <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="area" name="area" 
                                       placeholder="VD: 100" min="1" max="10000" required>
                                <span class="input-group-text">m²</span>
                            </div>
                            <div class="form-text">Diện tích bất động sản (1 - 10000 m²)</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="frontage" class="form-label">Mặt tiền</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="frontage" name="frontage" 
                                           placeholder="VD: 5" step="0.1" min="0">
                                    <span class="input-group-text">m</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="access_road" class="form-label">Đường truy cập</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="access_road" name="access_road" 
                                           placeholder="VD: 10" step="0.1" min="0">
                                    <span class="input-group-text">m</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="floors" class="form-label">Số tầng</label>
                                <input type="number" class="form-control" id="floors" name="floors" 
                                       placeholder="VD: 3" min="1" max="100">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="bedrooms" class="form-label">Số phòng ngủ</label>
                                <input type="number" class="form-control" id="bedrooms" name="bedrooms" 
                                       placeholder="VD: 4" min="0" max="50">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="bathrooms" class="form-label">Số phòng tắm</label>
                                <input type="number" class="form-control" id="bathrooms" name="bathrooms" 
                                       placeholder="VD: 3" min="0" max="50">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label">Thành phố</label>
                                <select class="form-select" id="city" name="city">
                                    <option value="Ha Noi">Hà Nội</option>
                                    <option value="Ho Chi Minh">Hồ Chí Minh</option>
                                    <option value="Da Nang">Đà Nẵng</option>
                                    <option value="Hai Phong">Hải Phòng</option>
                                    <option value="Can Tho">Cần Thơ</option>
                                    <option value="Hung Yen">Hưng Yên</option>
                                    <option value="Binh Duong">Bình Dương</option>
                                    <option value="Other">Khác</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="legal_status" class="form-label">
                                Tình trạng pháp lý <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="legal_status" name="legal_status" required>
                                <option value="Have certificate">Có sổ đỏ</option>
                                <option value="Sale contract">Hợp đồng mua bán</option>
                                <option value="Pending">Đang chờ</option>
                                <option value="Other">Khác</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="furniture_state" class="form-label">Tình trạng nội thất</label>
                            <select class="form-select" id="furniture_state" name="furniture_state">
                                <option value="Full">Đầy đủ (Full)</option>
                                <option value="Basic">Cơ bản (Basic)</option>
                                <option value="Empty">Trống (Empty)</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100" id="predictBtn">
                            <i class="bi bi-search me-2"></i>Dự đoán giá
                        </button>
                    </form>
                </div>
            </div>

            {{-- Model Info --}}
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="mb-2">
                        <i class="bi bi-info-circle me-1"></i>Thông tin mô hình
                    </h6>
                    <ul class="list-unstyled mb-0 small text-muted">
                        <li><i class="bi bi-check2 me-2 text-success"></i>Random Forest Classifier</li>
                        <li><i class="bi bi-check2 me-2 text-success"></i>3 phan khuc: Low, Medium, High</li>
                        <li><i class="bi bi-check2 me-2 text-success"></i>Features: Area, Floors, Bedrooms...</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Prediction Result --}}
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-clipboard-check me-2"></i>Kết quả dự đoán
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Initial State --}}
                    <div id="initialState" class="text-center py-5">
                        <div class="display-1 text-muted mb-3">
                            <i class="bi bi-question-circle"></i>
                        </div>
                        <h5 class="text-muted">Nhập thông tin bất động sản và nhấn "Dự đoán giá"</h5>
                        <p class="text-muted small">Kết quả sẽ hiển thị tại đây</p>
                    </div>

                    {{-- Loading State --}}
                    <div id="loadingState" class="text-center py-5 d-none">
                        <div class="spinner-border text-primary mb-3" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <h5 class="text-muted">Đang xử lý dự đoán...</h5>
                    </div>

                    {{-- Result State --}}
                    <div id="resultState" class="d-none">
                        <div class="text-center mb-4">
                            <div id="segmentIcon" class="display-1 mb-3"></div>
                            <h3 id="segmentLabel" class="mb-2"></h3>
                            <p id="segmentDescription" class="text-muted"></p>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="text-muted mb-1">Confidence</h6>
                                        <h4 id="confidenceValue" class="mb-0"></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="text-muted mb-1">Phương pháp</h6>
                                        <h4 id="methodValue" class="mb-0"></h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="bi bi-lightbulb me-2"></i>Phân tích
                                </h6>
                                <p id="analysisText" class="card-text mb-0"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Error State --}}
                    <div id="errorState" class="text-center py-5 d-none">
                        <div class="display-1 text-danger mb-3">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <h5 class="text-danger">Đã xảy ra lỗi</h5>
                        <p id="errorMessage" class="text-muted"></p>
                        <button class="btn btn-outline-primary" onclick="resetForm()">
                            Thử lại
                        </button>
                    </div>
                </div>
            </div>

            {{-- Segment Descriptions --}}
            <div class="card mt-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-collection me-2"></i>Giải thích phân khúc giá
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="p-3 bg-success bg-opacity-10 rounded">
                                <h6 class="text-success">
                                    <i class="bi bi-arrow-down-circle me-1"></i>Low (Thấp)
                                </h6>
                                <p class="small text-muted mb-0">
                                    Bất động sản giá rẻ, phù hợp với người có ngân sách hạn chế
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-warning bg-opacity-10 rounded">
                                <h6 class="text-warning">
                                    <i class="bi bi-dash-circle me-1"></i>Medium (Trung bình)
                                </h6>
                                <p class="small text-muted mb-0">
                                    Bất động sản trung bình, cân bằng giữa giá và chất lượng
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-danger bg-opacity-10 rounded">
                                <h6 class="text-danger">
                                    <i class="bi bi-arrow-up-circle me-1"></i>High (Cao)
                                </h6>
                                <p class="small text-muted mb-0">
                                    Bất động sản cao cấp, vị trí đắc địa, nội thất đầy đủ
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const segmentConfig = {
        'Low': {
            icon: '<i class="bi bi-arrow-down-circle-fill text-success"></i>',
            label: 'Low Price',
            description: 'Phân khúc giá thấp',
            color: 'success',
            analysis: 'Bất động sản này có giá thuộc phân khúc thấp. Phù hợp với ngân sách hạn chế hoặc đầu tư cho thuê.'
        },
        'Medium': {
            icon: '<i class="bi bi-dash-circle-fill text-warning"></i>',
            label: 'Medium Price',
            description: 'Phân khúc giá trung bình',
            color: 'warning',
            analysis: 'Bất động sản này có giá trung bình. Đây là lựa chọn cân bằng giữa giá cao và chất lượng.'
        },
        'High': {
            icon: '<i class="bi bi-arrow-up-circle-fill text-danger"></i>',
            label: 'High Price',
            description: 'Phân khúc giá cao',
            color: 'danger',
            analysis: 'Bất động sản này thuộc phân khúc cao cấp. Có thể có vị trí đắc địa, nội thất đầy đủ hoặc diện tích lớn.'
        }
    };

    function showState(state) {
        document.getElementById('initialState').classList.add('d-none');
        document.getElementById('loadingState').classList.add('d-none');
        document.getElementById('resultState').classList.add('d-none');
        document.getElementById('errorState').classList.add('d-none');
        
        if (state === 'initial') {
            document.getElementById('initialState').classList.remove('d-none');
        } else if (state === 'loading') {
            document.getElementById('loadingState').classList.remove('d-none');
        } else if (state === 'result') {
            document.getElementById('resultState').classList.remove('d-none');
        } else if (state === 'error') {
            document.getElementById('errorState').classList.remove('d-none');
        }
    }

    function resetForm() {
        document.getElementById('predictionForm').reset();
        showState('initial');
    }

    document.getElementById('predictionForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        
        // Convert numeric fields
        data.area = parseFloat(data.area);
        data.frontage = data.frontage ? parseFloat(data.frontage) : 0;
        data.access_road = data.access_road ? parseFloat(data.access_road) : 0;
        data.floors = data.floors ? parseInt(data.floors) : 1;
        data.bedrooms = data.bedrooms ? parseInt(data.bedrooms) : 1;
        data.bathrooms = data.bathrooms ? parseInt(data.bathrooms) : 1;
        
        showState('loading');
        
        try {
            const response = await fetch('{{ route("api.predict") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                const prediction = result.prediction;
                const config = segmentConfig[prediction.segment] || segmentConfig['Medium'];
                
                document.getElementById('segmentIcon').innerHTML = config.icon;
                document.getElementById('segmentLabel').textContent = config.label;
                document.getElementById('segmentLabel').className = `text-${config.color}`;
                document.getElementById('segmentDescription').textContent = config.description;
                document.getElementById('confidenceValue').textContent = `${(prediction.confidence * 100).toFixed(1)}%`;
                document.getElementById('methodValue').textContent = prediction.method === 'python' ? 'ML Model' : 'Rule-based';
                document.getElementById('analysisText').textContent = config.analysis;
                
                showState('result');
            } else {
                document.getElementById('errorMessage').textContent = result.message || 'Unknown error';
                showState('error');
            }
        } catch (error) {
            console.error('Prediction error:', error);
            document.getElementById('errorMessage').textContent = 'Máy chủ dự đoán hiện chưa phản hồi. Hãy kiểm tra database, model hoặc route `/api/predict` rồi thử lại.';
            showState('error');
        }
    });
</script>
@endpush
