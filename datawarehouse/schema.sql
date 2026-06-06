-- =====================================================
-- Vietnam Real Estate Data Warehouse
-- Star Schema Design
-- =====================================================

-- Drop existing tables if they exist
DROP TABLE IF EXISTS fact_property;
DROP TABLE IF EXISTS dim_location;
DROP TABLE IF EXISTS dim_legal;
DROP TABLE IF EXISTS dim_furniture;
DROP TABLE IF EXISTS dim_direction;
DROP TABLE IF EXISTS dim_property;
DROP TABLE IF EXISTS dim_date;

-- =====================================================
-- DIMENSION TABLES
-- =====================================================

-- Dimension: Location
CREATE TABLE dim_location (
    location_id INT PRIMARY KEY,
    full_address VARCHAR(500),
    city VARCHAR(100),
    district VARCHAR(100),
    ward VARCHAR(100)
);

-- Dimension: Legal Status
CREATE TABLE dim_legal (
    legal_id INT PRIMARY KEY,
    legal_status VARCHAR(50)
);

-- Dimension: Furniture State
CREATE TABLE dim_furniture (
    furniture_id INT PRIMARY KEY,
    furniture_state VARCHAR(50)
);

-- Dimension: Direction
CREATE TABLE dim_direction (
    direction_id INT PRIMARY KEY,
    house_direction VARCHAR(50),
    balcony_direction VARCHAR(50)
);

-- Dimension: Property Type
CREATE TABLE dim_property (
    property_type_id INT PRIMARY KEY,
    property_type_name VARCHAR(100)
);

-- Dimension: Date
CREATE TABLE dim_date (
    date_id INT PRIMARY KEY,
    year INT,
    quarter INT,
    month INT,
    month_name VARCHAR(20)
);

-- =====================================================
-- FACT TABLE
-- =====================================================

CREATE TABLE fact_property (
    property_id INT PRIMARY KEY,
    location_id INT,
    property_type_id INT,
    legal_id INT,
    furniture_id INT,
    direction_id INT,
    date_id INT,
    
    -- Measures
    area DECIMAL(10, 2),
    frontage DECIMAL(8, 2),
    access_road DECIMAL(8, 2),
    floors INT,
    bedrooms INT,
    bathrooms INT,
    price DECIMAL(15, 2),
    price_per_sqm DECIMAL(12, 2),
    
    -- Foreign Keys
    FOREIGN KEY (location_id) REFERENCES dim_location(location_id),
    FOREIGN KEY (property_type_id) REFERENCES dim_property(property_type_id),
    FOREIGN KEY (legal_id) REFERENCES dim_legal(legal_id),
    FOREIGN KEY (furniture_id) REFERENCES dim_furniture(furniture_id),
    FOREIGN KEY (direction_id) REFERENCES dim_direction(direction_id),
    FOREIGN KEY (date_id) REFERENCES dim_date(date_id)
);

-- =====================================================
-- INDEXES FOR PERFORMANCE
-- =====================================================

CREATE INDEX idx_fact_location ON fact_property(location_id);
CREATE INDEX idx_fact_legal ON fact_property(legal_id);
CREATE INDEX idx_fact_furniture ON fact_property(furniture_id);
CREATE INDEX idx_fact_direction ON fact_property(direction_id);
CREATE INDEX idx_fact_date ON fact_property(date_id);
CREATE INDEX idx_fact_price ON fact_property(price);

-- =====================================================
-- SAMPLE DATA: dim_location
-- =====================================================

INSERT INTO dim_location (location_id, full_address, city, district, ward) VALUES
(1, 'Phường 11, Gò Vấp, Hồ Chí Minh', 'Hồ Chí Minh', 'Gò Vấp', 'Phường 11'),
(2, 'Phường 8, Gò Vấp, Hồ Chí Minh', 'Hồ Chí Minh', 'Gò Vấp', 'Phường 8'),
(3, 'Phường 12, Gò Vấp, Hồ Chí Minh', 'Hồ Chí Minh', 'Gò Vấp', 'Phường 12'),
(4, 'Phường 22, Bình Thạnh, Hồ Chí Minh', 'Hồ Chí Minh', 'Bình Thạnh', 'Phường 22'),
(5, 'Phường 6, Bình Thạnh, Hồ Chí Minh', 'Hồ Chí Minh', 'Bình Thạnh', 'Phường 6'),
(6, 'Cầu Giấy, Hà Nội', 'Hà Nội', 'Cầu Giấy', 'Quan Hoa'),
(7, 'Nam Từ Liêm, Hà Nội', 'Hà Nội', 'Nam Từ Liêm', 'Phường Mỹ Đình 1'),
(8, 'Tây Hồ, Hà Nội', 'Hà Nội', 'Tây Hồ', 'Phú Thượng'),
(9, 'Hà Đông, Hà Nội', 'Hà Nội', 'Hà Đông', 'Yên Nghĩa'),
(10, 'Văn Giang, Hưng Yên', 'Hưng Yên', 'Văn Giang', 'Xã Long Hưng'),
(11, 'Thuận An, Bình Dương', 'Bình Dương', 'Thuận An', 'Phường An Phú'),
(12, 'Quảng Ninh', 'Quảng Ninh', 'Vân Đồn', 'Xã Đông Xá'),
(13, 'Phú Thọ', 'Phú Thọ', 'Thanh Thủy', 'Xã Đồng Trung'),
(14, 'Hải Dương', 'Hải Dương', 'Kinh Môn', 'Phường Minh Tân'),
(15, 'Tân Uyên, Bình Dương', 'Bình Dương', 'Tân Uyên', 'Xã Vĩnh Tân');

-- =====================================================
-- SAMPLE DATA: dim_legal
-- =====================================================

INSERT INTO dim_legal (legal_id, legal_status) VALUES
(1, 'Have certificate'),
(2, 'Sale contract'),
(3, 'Pending'),
(4, 'Other');

-- =====================================================
-- SAMPLE DATA: dim_furniture
-- =====================================================

INSERT INTO dim_furniture (furniture_id, furniture_state) VALUES
(1, 'Full'),
(2, 'Basic'),
(3, 'Empty');

-- =====================================================
-- SAMPLE DATA: dim_direction
-- =====================================================

INSERT INTO dim_direction (direction_id, house_direction, balcony_direction) VALUES
(1, 'Đông', 'Đông'),
(2, 'Tây', 'Tây'),
(3, 'Nam', 'Nam'),
(4, 'Bắc', 'Bắc'),
(5, 'Đông - Bắc', 'Đông - Bắc'),
(6, 'Đông - Nam', 'Đông - Nam'),
(7, 'Tây - Bắc', 'Tây - Bắc'),
(8, 'Tây - Nam', 'Tây - Nam'),
(9, 'Đông', 'Nam'),
(10, 'Tây', 'Bắc');

-- =====================================================
-- SAMPLE DATA: dim_property
-- =====================================================

INSERT INTO dim_property (property_type_id, property_type_name) VALUES
(1, 'Residential'),
(2, 'Commercial'),
(3, 'Industrial'),
(4, 'Agricultural');

-- =====================================================
-- SAMPLE DATA: dim_date
-- =====================================================

INSERT INTO dim_date (date_id, year, quarter, month, month_name) VALUES
(1, 2024, 1, 1, 'January'),
(2, 2024, 1, 2, 'February'),
(3, 2024, 1, 3, 'March'),
(4, 2024, 2, 4, 'April'),
(5, 2024, 2, 5, 'May'),
(6, 2024, 2, 6, 'June'),
(7, 2024, 3, 7, 'July'),
(8, 2024, 3, 8, 'August'),
(9, 2024, 3, 9, 'September'),
(10, 2024, 4, 10, 'October'),
(11, 2024, 4, 11, 'November'),
(12, 2024, 4, 12, 'December');

-- =====================================================
-- SAMPLE DATA: fact_property (first 20 records)
-- =====================================================

INSERT INTO fact_property (property_id, location_id, property_type_id, legal_id, furniture_id, direction_id, date_id, area, frontage, access_road, floors, bedrooms, bathrooms, price, price_per_sqm) VALUES
(1, 1, 1, 1, 1, 5, 1, 54.00, 3.50, 3.50, 2, 2, 3, 5.35, 0.10),
(2, 2, 1, 1, 1, 6, 1, 92.00, 4.00, 6.00, 2, 4, 4, 6.90, 0.08),
(3, 3, 1, 1, 1, 6, 2, 46.00, 4.60, 6.00, 4, 4, 5, 7.99, 0.17),
(4, 4, 1, 1, 2, 6, 2, 60.00, 3.50, 5.00, 2, 6, 5, 5.60, 0.09),
(5, 5, 1, 1, 1, 5, 3, 32.80, 4.50, 6.00, 5, 4, 4, 7.50, 0.23),
(6, 6, 1, 1, 2, 5, 3, 72.00, 5.60, 3.50, 1, 4, 3, 10.00, 0.14),
(7, 7, 1, 1, 1, 7, 4, 54.00, 5.80, 3.00, 4, 6, 6, 8.80, 0.16),
(8, 8, 1, 1, 2, 7, 4, 36.00, 3.80, 2.20, 5, 3, 3, 3.85, 0.11),
(9, 6, 1, 1, 1, 6, 5, 41.00, 3.50, 3.50, 5, 4, 4, 10.00, 0.24),
(10, 9, 1, 1, 2, 6, 5, 35.00, 4.00, 4.00, 4, 3, 3, 2.90, 0.08),
(11, 10, 1, 2, 3, 5, 6, 84.00, 6.00, 13.00, 5, 5, 4, 8.60, 0.10),
(12, 10, 1, 2, 3, 5, 6, 60.00, 5.00, 13.00, 5, 4, 4, 7.50, 0.13),
(13, 10, 1, 2, 1, 6, 7, 90.00, 6.00, 13.00, 5, 4, 4, 8.90, 0.10),
(14, 10, 1, 1, 3, 5, 7, 70.00, 6.00, 13.00, 5, 6, 6, 9.80, 0.14),
(15, 10, 1, 1, 3, 6, 7, 91.00, 7.00, 17.00, 5, 6, 6, 9.80, 0.11),
(16, 11, 1, 1, 1, 3, 8, 150.00, 10.00, 20.00, 2, 7, 4, 6.70, 0.04),
(17, 10, 1, 2, 3, 6, 8, 91.00, 5.00, 13.00, 5, 6, 4, 8.10, 0.09),
(18, 10, 1, 1, 2, 6, 8, 78.00, 6.00, 13.00, 4, 6, 6, 8.63, 0.11),
(19, 12, 1, 1, 1, 1, 9, 300.00, 15.00, 32.00, 3, 6, 3, 9.40, 0.03),
(20, 10, 1, 1, 2, 6, 9, 48.00, 5.00, 13.00, 5, 5, 4, 5.70, 0.12);

-- =====================================================
-- ANALYTICAL QUERIES
-- =====================================================

-- Query 1: Average price by city
-- SELECT l.city, 
--        COUNT(*) as property_count,
--        AVG(f.price) as avg_price,
--        MIN(f.price) as min_price,
--        MAX(f.price) as max_price
-- FROM fact_property f
-- JOIN dim_location l ON f.location_id = l.location_id
-- GROUP BY l.city
-- ORDER BY avg_price DESC;

-- Query 2: Average price by legal status
-- SELECT lg.legal_status,
--        COUNT(*) as property_count,
--        AVG(f.price) as avg_price
-- FROM fact_property f
-- JOIN dim_legal lg ON f.legal_id = lg.legal_id
-- GROUP BY lg.legal_status
-- ORDER BY avg_price DESC;

-- Query 3: Average price by furniture state
-- SELECT fr.furniture_state,
--        COUNT(*) as property_count,
--        AVG(f.price) as avg_price
-- FROM fact_property f
-- JOIN dim_furniture fr ON f.furniture_id = fr.furniture_id
-- GROUP BY fr.furniture_state
-- ORDER BY avg_price DESC;

-- Query 4: Multi-dimensional analysis (City x Legal Status x Furniture)
-- SELECT l.city, lg.legal_status, fr.furniture_state,
--        COUNT(*) as property_count,
--        AVG(f.price) as avg_price
-- FROM fact_property f
-- JOIN dim_location l ON f.location_id = l.location_id
-- JOIN dim_legal lg ON f.legal_id = lg.legal_id
-- JOIN dim_furniture fr ON f.furniture_id = fr.furniture_id
-- GROUP BY l.city, lg.legal_status, fr.furniture_state
-- HAVING COUNT(*) > 1
-- ORDER BY avg_price DESC;
