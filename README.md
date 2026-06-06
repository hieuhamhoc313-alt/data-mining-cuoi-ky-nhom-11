# Vietnam Real Estate Data Mining System

He th thong Data Mining cho bat dong san Viet Nam voi cac chuc nang:

- Phan tich du lieu (EDA)
- Tien xu ly du lieu
- Thiet ke Data Warehouse (Star Schema)
- Iceberg Cube Analysis
- Classification (Decision Tree, Random Forest)
- Laravel 12 Web Application

## Cau truc du an

```
housing-vn/
├── vietnam_housing_dataset.csv    # Du lieu nguon
├── notebooks/                    # Jupyter Notebooks
│   ├── 01_eda.ipynb            # Exploratory Data Analysis
│   ├── 02_preprocessing.ipynb   # Data Preprocessing
│   ├── 03_classification.ipynb  # Classification Models
│   └── 04_data_warehouse.ipynb  # Data Warehouse Design
├── datawarehouse/               # SQL schemas
│   ├── schema.sql              # Star Schema
│   ├── etl_flow.md             # ETL Documentation
│   └── iceberg_cube.sql        # Iceberg Cube Queries
├── models/                      # Trained models (pickle files)
├── laravel-app/                 # Laravel 12 Application
│   ├── app/
│   │   ├── Http/Controllers/  # Controllers
│   │   ├── Models/             # Eloquent Models
│   │   ├── Services/           # Business Logic
│   │   ├── Repositories/       # Repository Pattern
│   │   └── Http/Requests/      # Form Requests
│   ├── database/
│   │   ├── migrations/        # Database Migrations
│   │   └── seeders/           # Data Seeders
│   ├── resources/views/        # Blade Views
│   └── routes/                # Route Definitions
└── requirements.txt            # Python dependencies
```

## Setup

### 1. Python Environment

```bash
pip install -r requirements.txt
```

### 2. Run Notebooks

```bash
cd notebooks
jupyter notebook
```

### 3. Laravel Setup

```bash
cd laravel-app

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Create database
mysql -u root -p
CREATE DATABASE housing_vn;

# Run migrations
php artisan migrate

# Seed data from CSV
php artisan db:seed

# Start server
php artisan serve
```

## Tinh nang

### Dashboard
- Tong so bat dong san
- Gia trung binh, cao nhat, thap nhat
- Bieu do phan phoi gia
- Bieu do theo thanh pho

### Analytics
- Gia theo dien tich
- Gia theo so phong ngu
- Gia theo tinh trang phap ly
- Gia theo tinh trang noi that

### Data Warehouse
- Star Schema voi fact table va dimension tables
- Thong ke theo chieu

### Iceberg Cube
- GROUP BY CUBE voi 3 chieu: City, Legal Status, Furniture State
- HAVING COUNT(*) > 20

### Prediction
- Nhap thong tin BDS
- Du doan phan khuc gia (Low/Medium/High)
- Su dung Random Forest Classifier

## API Endpoints

```
GET  /api/v1/properties      - Danh sach BDS
GET  /api/v1/properties/{id} - Chi tiet BDS
GET  /api/v1/analytics       - Analytics data
GET  /api/v1/statistics      - Thong ke
POST /api/v1/predict         - Du doan gia
```

## Du lieu

Tap du lieu chinh gom 30,230 ban ghi voi cac cot:
- Address, Area, Frontage, Access Road
- House direction, Balcony direction
- Floors, Bedrooms, Bathrooms
- Legal status, Furniture state
- Price

## Giao dien

- Bootstrap 5 + Custom CSS
- Chart.js cho bieu do
- Responsive (Desktop, Tablet, Mobile)
- Sidebar navigation

## Thu tu trien khai

1. Chay EDA notebook de phan tich du lieu
2. Chay preprocessing notebook de xu ly du lieu
3. Chay classification notebook de train model
4. Cai dat Laravel va chay seeder
5. Khoi dong ung dung

## Yêu cau

- PHP 8.2+
- MySQL 8.0+
- Python 3.10+
- Node.js (optional, for asset compilation)
