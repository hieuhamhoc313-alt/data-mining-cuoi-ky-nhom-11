# Housing VN - Hệ thống Phân tích và Dự đoán Bất động sản Việt Nam

Đây là đồ án Data Mining kết hợp giữa phân tích dữ liệu, tiền xử lý, huấn luyện mô hình phân loại giá bất động sản và xây dựng web app bằng Laravel.

Project gồm 2 phần chính:

- `housing-app/`: ứng dụng web Laravel
- `notebooks/`, `train_models.py`, `models/`: phần xử lý dữ liệu, EDA, classification và model artifacts

## 1. Chức năng chính

- Phân tích dữ liệu bất động sản Việt Nam
- Dashboard thống kê giá nhà
- Báo cáo Data Warehouse
- Báo cáo Iceberg Cube
- Form dự đoán phân khúc giá bất động sản
- Huấn luyện mô hình Decision Tree và Random Forest

## 2. Công nghệ sử dụng

### Backend
- PHP 8.3+
- Laravel 13
- Filament 5
- MySQL

### Frontend
- Blade
- Bootstrap
- Vite
- Tailwind CSS 4
- Chart.js

### Data Science / Machine Learning
- Python 3.10+
- Pandas
- NumPy
- Matplotlib
- Scikit-learn
- Joblib
- Jupyter Notebook

## 3. Cần cài gì trước khi chạy?

Nếu bạn là người mới, hãy cài lần lượt các phần sau:

### Bắt buộc
1. **Git**
   - Dùng để clone source code
2. **PHP 8.3 hoặc mới hơn**
3. **Composer**
   - Dùng để cài dependency PHP/Laravel
4. **Node.js 20+ và npm**
   - Dùng để build giao diện frontend bằng Vite
5. **MySQL 8+**
   - Dùng để lưu dữ liệu của ứng dụng
6. **Python 3.10+**
   - Dùng để train mô hình ML và tạo file model `.pkl`

### Nên cài thêm
- **phpMyAdmin**, **TablePlus**, hoặc **DBeaver** để quản lý MySQL dễ hơn
- **Jupyter Notebook / JupyterLab** để mở các notebook trong thư mục `notebooks/`
- **Herd**, **XAMPP**, **MAMP**, hoặc **Laragon** nếu bạn muốn có môi trường local PHP/MySQL dễ dùng

## 4. Hướng dẫn setup riêng cho Windows

Nếu bạn dùng **Windows 10/11** và là người mới, đây là cách setup dễ nhất.

### Phương án dễ nhất cho người mới

Nên cài:
- **Git for Windows**
- **Laragon** hoặc **XAMPP**
- **Node.js 20+**
- **Python 3.10+**
- **Composer**

> Gợi ý: nếu bạn chưa quen tự cấu hình PHP/MySQL, hãy dùng **Laragon** vì cài nhanh và dễ bật/tắt dịch vụ.

### Cách cài từng phần trên Windows

#### 1. Cài Git
- Tải và cài **Git for Windows**
- Sau khi cài xong, mở **Git Bash**, **PowerShell**, hoặc **Command Prompt**
- Kiểm tra:

```powershell
git --version
```

#### 2. Cài PHP, MySQL và web stack

Bạn có thể chọn một trong hai cách:

**Cách A - Laragon**
- Cài Laragon
- Bật Laragon để có sẵn PHP, MySQL và môi trường local
- Nếu Laragon chưa có đúng version PHP, bạn có thể thêm PHP 8.3+

**Cách B - XAMPP**
- Cài XAMPP
- Bật **Apache** và **MySQL** trong XAMPP Control Panel
- Đảm bảo PHP trong XAMPP đã được thêm vào `PATH` nếu muốn chạy `php` từ terminal

Kiểm tra:

```powershell
php --version
mysql --version
```

#### 3. Cài Composer
- Tải Composer Installer cho Windows
- Trong lúc cài, trỏ đúng đến file `php.exe`
- Kiểm tra:

```powershell
composer --version
```

#### 4. Cài Node.js
- Tải bản **LTS** từ trang Node.js
- Kiểm tra:

```powershell
node -v
npm -v
```

#### 5. Cài Python
- Tải Python 3.10+ từ python.org
- Khi cài, nhớ tick **Add Python to PATH**
- Kiểm tra:

```powershell
python --version
pip --version
```

### Terminal nên dùng trên Windows

Bạn có thể dùng:
- **PowerShell**
- **Command Prompt**
- **Git Bash**
- Terminal tích hợp trong **VS Code / Cursor**

Nếu bạn dùng PowerShell, phần lớn lệnh trong README đều chạy được.

## 5. Clone project

Từ terminal, chạy:

```bash
git clone <repo-url>
cd housing-vn
```

## 7. Cấu trúc thư mục quan trọng

```text
housing-vn/
├── housing-app/                  # Laravel web application
├── notebooks/                    # Notebook cho EDA, preprocessing, classification, warehouse
├── models/                       # File model sau khi train (.pkl, metrics, thresholds...)
├── train_models.py               # Script train model ML
└── vietnam_housing_dataset.csv   # Dataset chính
```

## 8. Setup Laravel app

Di chuyển vào thư mục app:

```bash
cd housing-app
```

### Bước 1: cài dependency PHP

```bash
composer install
```

### Bước 2: cài dependency frontend

```bash
npm install
```

### Bước 3: tạo file môi trường

Nếu chưa có `.env`:

```bash
cp .env.example .env
```

### Bước 4: tạo application key

```bash
php artisan key:generate
```

## 9. Cấu hình `.env`

Mở file `housing-app/.env` và chỉnh các dòng quan trọng sau:

```env
APP_NAME="Housing VN"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=housing_app
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

### Lưu ý cho người mới
- `DB_DATABASE`: tên database bạn sẽ tạo trong MySQL
- `DB_USERNAME`: thường là `root` nếu chạy local
- `DB_PASSWORD`: để trống nếu MySQL local không có mật khẩu
- `SESSION_DRIVER=file`, `CACHE_STORE=file`, `QUEUE_CONNECTION=sync` phù hợp hơn cho môi trường local đơn giản

## 10. Tạo database MySQL

Đăng nhập MySQL rồi tạo database:

```sql
CREATE DATABASE housing_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Hoặc chạy nhanh bằng terminal:

```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS housing_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

## 11. Chạy migration và seed dữ liệu

Trong thư mục `housing-app/` chạy:

```bash
php artisan migrate
php artisan db:seed
```

Nếu muốn reset toàn bộ database và nạp lại từ đầu:

```bash
php artisan migrate:fresh --seed
```

## 12. Chạy frontend và backend

### Cách 1: chạy tách riêng

Terminal 1:

```bash
php artisan serve
```

Terminal 2:

```bash
npm run dev
```

Sau đó mở trình duyệt tại:

```text
http://127.0.0.1:8000
```

### Cách 2: build frontend trước rồi chạy backend

```bash
npm run build
php artisan serve
```

## 13. Nếu giao diện lỗi hoặc không load CSS/JS

Hãy thử:

```bash
php artisan optimize:clear
npm run build
```

Nếu vẫn đang phát triển giao diện, ưu tiên chạy:

```bash
npm run dev
```

## 14. Setup phần Python để train model

Quay về thư mục gốc project:

```bash
cd ..
```

Tạo virtual environment:

```bash
python3 -m venv .venv
source .venv/bin/activate
```

Trên Windows PowerShell:

```powershell
python -m venv .venv
.venv\Scripts\Activate.ps1
```

Cài các thư viện cần thiết:

```bash
pip install pandas numpy matplotlib scikit-learn joblib jupyter seaborn
```

## 15. Train model ML

Từ thư mục gốc `housing-vn/`, chạy:

```bash
python3 train_models.py
```

Sau khi train xong, thư mục `models/` cần có các file như:

- `random_forest_model.pkl`
- `scaler.pkl`
- `feature_names.pkl`
- `metrics.json`
- `thresholds.json`

### Vì sao bước này quan trọng?
Form dự đoán trong web app sẽ cố dùng model ML thật nếu các file `.pkl` tồn tại. Nếu chưa có, hệ thống có thể phải fallback sang rule-based prediction.

## 16. Chạy Jupyter Notebook

Nếu muốn xem EDA và các bước xử lý dữ liệu:

```bash
jupyter notebook
```

Mở các file trong thư mục `notebooks/`:

- `01_eda.ipynb`
- `02_preprocessing.ipynb`
- `03_classification.ipynb`
- `04_data_warehouse.ipynb`

## 17. Tài khoản đăng nhập mặc định

Sau khi seed, hãy kiểm tra các file trong:

- `housing-app/database/seeders/`

Nếu project có tạo tài khoản mặc định, thông tin đăng nhập sẽ được định nghĩa ở đó. Nếu chưa rõ, bạn có thể mở `UserSeeder.php` để xem email và password mẫu.

## 18. Các lệnh hay dùng

### Laravel

```bash
php artisan serve
php artisan migrate
php artisan db:seed
php artisan migrate:fresh --seed
php artisan optimize:clear
php artisan route:list
```

### Frontend

```bash
npm install
npm run dev
npm run build
```

### Python / ML

```bash
python3 train_models.py
jupyter notebook
```

## 19. Troubleshooting cho người mới

### 1. Lỗi `composer: command not found`
Bạn chưa cài Composer hoặc chưa thêm Composer vào PATH.

### 2. Lỗi `php: command not found`
Bạn chưa cài PHP hoặc terminal chưa nhận PATH của PHP.

### 3. Lỗi `npm: command not found`
Bạn chưa cài Node.js.

### 4. Lỗi không kết nối được MySQL
Kiểm tra lại:
- MySQL đã bật chưa
- đúng `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` chưa

### 5. Lỗi session/cache/queue liên quan database
Nếu bạn chỉ chạy local để demo, nên dùng:

```env
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

### 6. Lỗi predict không chạy
Hãy kiểm tra:
- database đã migrate chưa
- frontend đã build chưa
- đã train model và có file `.pkl` trong thư mục `models/` chưa

### 7. Lỗi giao diện không hiển thị đúng
Chạy lại:

```bash
php artisan optimize:clear
npm run dev
```

## 20. Quy trình setup nhanh cho người mới

Nếu bạn muốn setup nhanh từ đầu, đây là thứ tự nên làm:

1. Cài Git, PHP, Composer, Node.js, MySQL, Python
2. Clone project
3. Vào `housing-app/`
4. Chạy `composer install`
5. Chạy `npm install`
6. Tạo `.env` và cấu hình database
7. Chạy `php artisan key:generate`
8. Tạo database `housing_app`
9. Chạy `php artisan migrate --seed`
10. Quay về thư mục gốc chạy `python3 train_models.py`
11. Quay lại `housing-app/` chạy `php artisan serve`
12. Chạy `npm run dev`
13. Mở web và sử dụng

## 21. Gợi ý môi trường cài đặt dễ nhất

Nếu bạn mới hoàn toàn, cấu hình dễ nhất thường là:

- **macOS**: Herd + Homebrew + Python 3
- **Windows**: Laragon hoặc XAMPP + Python 3 + Node.js
- **Linux**: PHP + Composer + MySQL + Node.js + Python cài thủ công

## 22. Ghi chú

- App web nằm trong thư mục `housing-app/`, không phải thư mục gốc
- Dataset chính là `vietnam_housing_dataset.csv`
- Các notebook dùng cho báo cáo và phân tích học thuật
- Nếu thiếu model `.pkl`, web vẫn có thể dùng fallback prediction, nhưng để đúng bài toán ML thì nên train model đầy đủ

---

Nếu bạn muốn, mình có thể viết tiếp một bản README nâng cấp hơn gồm:
- hướng dẫn riêng cho **macOS / Windows / Linux**
- ảnh chụp giao diện
- sơ đồ kiến trúc project
- mục FAQ cho giảng viên hoặc người chấm bài
