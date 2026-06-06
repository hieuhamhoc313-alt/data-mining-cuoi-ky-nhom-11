# ETL Flow Documentation
## Vietnam Real Estate Data Warehouse

---

## 1. ETL Overview

ETL (Extract, Transform, Load) is the process of:
- **Extract**: Reading data from source systems (CSV files)
- **Transform**: Cleaning, validating, and transforming data
- **Load**: Inserting data into the data warehouse

---

## 2. ETL Process Steps

### Step 1: EXTRACT

```
Source: vietnam_housing_dataset.csv
```

| Field | Type | Description |
|-------|------|-------------|
| Address | String | Full property address |
| Area | Float | Property area in m² |
| Frontage | Float | Frontage width in m |
| Access Road | Float | Access road width in m |
| House direction | String | House facing direction |
| Balcony direction | String | Balcony facing direction |
| Floors | Integer | Number of floors |
| Bedrooms | Integer | Number of bedrooms |
| Bathrooms | Integer | Number of bathrooms |
| Legal status | String | Legal documentation status |
| Furniture state | String | Furniture condition |
| Price | Float | Property price (Billions VND) |

---

### Step 2: TRANSFORM

#### 2.1 Data Cleaning

```python
# Handle missing values
numerical_cols = ['Area', 'Frontage', 'Access Road', 'Floors', 'Bedrooms', 'Bathrooms']
categorical_cols = ['House direction', 'Balcony direction', 'Legal status', 'Furniture state']

# Fill numerical with median
for col in numerical_cols:
    df[col] = df[col].fillna(df[col].median())

# Fill categorical with mode
for col in categorical_cols:
    df[col] = df[col].fillna(df[col].mode()[0])
```

#### 2.2 Outlier Removal (IQR Method)

```python
def remove_outliers_iqr(df, columns):
    for col in columns:
        Q1 = df[col].quantile(0.25)
        Q3 = df[col].quantile(0.75)
        IQR = Q3 - Q1
        lower = Q1 - 1.5 * IQR
        upper = Q3 + 1.5 * IQR
        df = df[(df[col] >= lower) & (df[col] <= upper)]
    return df
```

#### 2.3 Feature Engineering

```python
# Extract City from Address
def extract_city(address):
    if 'HÀ NỘI' in address: return 'Hà Nội'
    elif 'HỒ CHÍ MINH' in address: return 'Hồ Chí Minh'
    elif 'ĐÀ NẴNG' in address: return 'Đà Nẵng'
    else: return 'Other'

# Extract District from Address
def extract_district(address):
    # Pattern matching for district names
    pass
```

#### 2.4 Surrogate Key Generation

```python
# Generate surrogate keys for dimension tables
dim_location['location_id'] = range(1, len(dim_location) + 1)
dim_legal['legal_id'] = range(1, len(dim_legal) + 1)
dim_furniture['furniture_id'] = range(1, len(dim_furniture) + 1)
dim_direction['direction_id'] = range(1, len(dim_direction) + 1)
```

#### 2.5 Measure Calculations

```python
# Calculate derived measures
fact_property['price_per_sqm'] = fact_property['Price'] / fact_property['Area']
```

---

### Step 3: LOAD

#### 3.1 Load Dimension Tables (SCD Type 1)

```sql
-- Load dim_location
INSERT INTO dim_location (location_id, full_address, city, district, ward)
SELECT DISTINCT location_id, Address, City, District, Ward
FROM staging_property;

-- Load dim_legal
INSERT INTO dim_legal (legal_id, legal_status)
SELECT DISTINCT legal_id, Legal status
FROM staging_property;
```

#### 3.2 Load Fact Table

```sql
-- Load fact_property
INSERT INTO fact_property (
    property_id, location_id, property_type_id, legal_id,
    furniture_id, direction_id, date_id, area, frontage,
    access_road, floors, bedrooms, bathrooms, price, price_per_sqm
)
SELECT
    s.property_id,
    dl.location_id,
    dp.property_type_id,
    dl.legal_id,
    df.furniture_id,
    dd.direction_id,
    dd.date_id,
    s.Area,
    s.Frontage,
    s.Access_Road,
    s.Floors,
    s.Bedrooms,
    s.Bathrooms,
    s.Price,
    s.Price / s.Area
FROM staging_property s
JOIN dim_location dl ON s.Address = dl.full_address
JOIN dim_legal dl2 ON s.Legal_status = dl2.legal_status
JOIN dim_furniture df ON s.Furniture_state = df.furniture_state
JOIN dim_direction dd ON s.House_direction = dd.house_direction;
```

---

## 3. ETL Schedule

| Phase | Frequency | Description |
|-------|-----------|-------------|
| Initial Load | One-time | Load all historical data |
| Incremental | Daily | Load new records only |
| Full Refresh | Monthly | Complete reload for data correction |

---

## 4. Data Quality Checks

### Pre-Load Validation

- [ ] Check for NULL primary keys
- [ ] Verify foreign key references exist
- [ ] Validate numeric ranges (Area > 0, Price > 0)
- [ ] Check for duplicate records

### Post-Load Validation

```sql
-- Record count validation
SELECT 'dim_location' as table_name, COUNT(*) as row_count FROM dim_location
UNION ALL
SELECT 'dim_legal', COUNT(*) FROM dim_legal
UNION ALL
SELECT 'fact_property', COUNT(*) FROM fact_property;

-- Aggregate validation
SELECT
    COUNT(*) as total_properties,
    SUM(price) as total_value,
    AVG(price) as avg_price
FROM fact_property;
```

---

## 5. Error Handling

```python
try:
    # ETL process
    extract_data()
    transform_data()
    load_data()
except Exception as e:
    log_error(e)
    send_alert()
    rollback_transaction()
```

---

## 6. ETL Flow Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                         EXTRACT                                  │
│  ┌──────────────────────────────────────────────────────────┐    │
│  │              vietnam_housing_dataset.csv                  │    │
│  └──────────────────────────────────────────────────────────┘    │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                        TRANSFORM                                 │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────┐     │
│  │   Cleanse   │─▶│   Filter    │─▶│  Feature Engineering│     │
│  └─────────────┘  └─────────────┘  └─────────────────────┘     │
│        │                │                      │                │
│        ▼                ▼                      ▼                │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────┐     │
│  │   Outlier  │  │   Missing   │  │   Surrogate Keys    │     │
│  │  Removal   │  │   Values    │  │    Generation       │     │
│  └─────────────┘  └─────────────┘  └─────────────────────┘     │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                          LOAD                                    │
│  ┌──────────────────────────────────────────────────────────┐    │
│  │                    fact_property                         │    │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐   │    │
│  │  │dim_loc   │ │dim_legal │ │dim_furn  │ │dim_dir   │   │    │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘   │    │
│  └──────────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────────┘
```
