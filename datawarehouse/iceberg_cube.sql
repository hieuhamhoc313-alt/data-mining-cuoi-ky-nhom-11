-- =====================================================
-- Iceberg Cube Implementation
-- Vietnam Real Estate Data Warehouse
-- =====================================================

-- =====================================================
-- ICEBERG CUBE CONCEPT
-- =====================================================

/*
ICEBERG CUBE EXPLANATION:
=========================

1. GROUP BY CUBE generates all possible combinations of the specified dimensions.
   For n dimensions, it generates 2^n groupings.

2. The "Iceberg" condition (HAVING COUNT(*) > threshold) filters out
   sparse combinations with few records, improving query performance
   and focusing on statistically significant groups.

3. This is particularly useful for:
   - Data exploration and discovery
   - Identifying significant patterns
   - Reducing the result set size while maintaining important insights

Example for 3 dimensions (City, Legal Status, Furniture State):
- FULL CUBE: 2^3 = 8 combinations
- ICEBERG (COUNT > 20): Only significant groups shown
*/

-- =====================================================
-- BASIC ICEBERG CUBE QUERIES
-- =====================================================

-- Query 1: Iceberg Cube with City, Legal Status, Furniture State
-- =====================================================

SELECT 
    l.city,
    lg.legal_status,
    fr.furniture_state,
    COUNT(*) as property_count,
    AVG(f.price) as avg_price,
    MIN(f.price) as min_price,
    MAX(f.price) as max_price,
    SUM(f.price) as total_value,
    AVG(f.area) as avg_area
FROM fact_property f
JOIN dim_location l ON f.location_id = l.location_id
JOIN dim_legal lg ON f.legal_id = lg.legal_id
JOIN dim_furniture fr ON f.furniture_id = fr.furniture_id
GROUP BY CUBE (l.city, lg.legal_status, fr.furniture_state)
HAVING COUNT(*) > 20
ORDER BY l.city, lg.legal_status, fr.furniture_state;

-- =====================================================
-- Query 2: Iceberg Cube with City and Legal Status
-- =====================================================

SELECT 
    l.city,
    lg.legal_status,
    COUNT(*) as property_count,
    AVG(f.price) as avg_price,
    MIN(f.price) as min_price,
    MAX(f.price) as max_price,
    AVG(f.price_per_sqm) as avg_price_per_sqm
FROM fact_property f
JOIN dim_location l ON f.location_id = l.location_id
JOIN dim_legal lg ON f.legal_id = lg.legal_id
GROUP BY CUBE (l.city, lg.legal_status)
HAVING COUNT(*) > 20
ORDER BY l.city, lg.legal_status;

-- =====================================================
-- Query 3: Iceberg Cube with City and Furniture State
-- =====================================================

SELECT 
    l.city,
    fr.furniture_state,
    COUNT(*) as property_count,
    AVG(f.price) as avg_price,
    MIN(f.price) as min_price,
    MAX(f.price) as max_price,
    AVG(f.area) as avg_area
FROM fact_property f
JOIN dim_location l ON f.location_id = l.location_id
JOIN dim_furniture fr ON f.furniture_id = fr.furniture_id
GROUP BY CUBE (l.city, fr.furniture_state)
HAVING COUNT(*) > 20
ORDER BY l.city, fr.furniture_state;

-- =====================================================
-- Query 4: Iceberg Cube with Legal Status and Furniture State
-- =====================================================

SELECT 
    lg.legal_status,
    fr.furniture_state,
    COUNT(*) as property_count,
    AVG(f.price) as avg_price,
    MIN(f.price) as min_price,
    MAX(f.price) as max_price,
    AVG(f.bedrooms) as avg_bedrooms,
    AVG(f.bathrooms) as avg_bathrooms
FROM fact_property f
JOIN dim_legal lg ON f.legal_id = lg.legal_id
JOIN dim_furniture fr ON f.furniture_id = fr.furniture_id
GROUP BY CUBE (lg.legal_status, fr.furniture_state)
HAVING COUNT(*) > 20
ORDER BY lg.legal_status, fr.furniture_state;

-- =====================================================
-- Query 5: Full Iceberg Cube (all 3 dimensions)
-- =====================================================

-- This query shows all meaningful combinations
-- NULL values in the result indicate "ALL" for that dimension

SELECT 
    COALESCE(l.city, 'ALL CITIES') as city,
    COALESCE(lg.legal_status, 'ALL LEGAL STATUS') as legal_status,
    COALESCE(fr.furniture_state, 'ALL FURNITURE') as furniture_state,
    COUNT(*) as property_count,
    AVG(f.price) as avg_price,
    AVG(f.area) as avg_area,
    AVG(f.price_per_sqm) as avg_price_per_sqm
FROM fact_property f
JOIN dim_location l ON f.location_id = l.location_id
JOIN dim_legal lg ON f.legal_id = lg.legal_id
JOIN dim_furniture fr ON f.furniture_id = fr.furniture_id
GROUP BY CUBE (l.city, lg.legal_status, fr.furniture_state)
HAVING COUNT(*) > 20
ORDER BY 
    GROUPING(l.city),
    l.city,
    GROUPING(lg.legal_status),
    lg.legal_status,
    GROUPING(fr.furniture_state),
    fr.furniture_state;

-- =====================================================
-- ROLLUP vs CUBE Comparison
-- =====================================================

/*
ROLLUP: Hierarchical aggregation
- Creates (n+1) levels of aggregation
- For City, Legal, Furniture: City only, City+Legal, City+Legal+Furniture

CUBE: All combinations
- Creates 2^n combinations
- For 3 dimensions: 8 combinations
- More comprehensive but more expensive
*/

-- ROLLUP Example (same dimensions)
SELECT 
    l.city,
    lg.legal_status,
    fr.furniture_state,
    COUNT(*) as property_count,
    AVG(f.price) as avg_price
FROM fact_property f
JOIN dim_location l ON f.location_id = l.location_id
JOIN dim_legal lg ON f.legal_id = lg.legal_id
JOIN dim_furniture fr ON f.furniture_id = fr.furniture_id
GROUP BY ROLLUP (l.city, lg.legal_status, fr.furniture_state)
HAVING COUNT(*) > 20
ORDER BY l.city, lg.legal_status, fr.furniture_state;

-- =====================================================
-- Advanced Iceberg Cube with Price Segments
-- =====================================================

-- Create price segment based on price percentiles
WITH price_segments AS (
    SELECT 
        f.*,
        CASE 
            WHEN f.price < 5.0 THEN 'Budget'
            WHEN f.price < 8.0 THEN 'Mid-Range'
            ELSE 'Premium'
        END as price_segment
    FROM fact_property f
)
SELECT 
    l.city,
    lg.legal_status,
    fr.furniture_state,
    ps.price_segment,
    COUNT(*) as property_count,
    AVG(f.price) as avg_price,
    AVG(f.area) as avg_area
FROM price_segments ps
JOIN dim_location l ON ps.location_id = l.location_id
JOIN dim_legal lg ON ps.legal_id = lg.legal_id
JOIN dim_furniture fr ON ps.furniture_id = fr.furniture_id
GROUP BY CUBE (l.city, lg.legal_status, fr.furniture_state, ps.price_segment)
HAVING COUNT(*) > 10
ORDER BY l.city, lg.legal_status, fr.furniture_state, ps.price_segment;

-- =====================================================
-- Iceberg Cube Statistics Summary
-- =====================================================

-- Get summary statistics for each dimension level
SELECT 
    'City Level' as aggregation_level,
    l.city as dimension_value,
    COUNT(*) as property_count,
    AVG(f.price) as avg_price
FROM fact_property f
JOIN dim_location l ON f.location_id = l.location_id
GROUP BY l.city
HAVING COUNT(*) > 20

UNION ALL

SELECT 
    'Legal Status Level' as aggregation_level,
    lg.legal_status as dimension_value,
    COUNT(*) as property_count,
    AVG(f.price) as avg_price
FROM fact_property f
JOIN dim_legal lg ON f.legal_id = lg.legal_id
GROUP BY lg.legal_status
HAVING COUNT(*) > 20

UNION ALL

SELECT 
    'Furniture State Level' as aggregation_level,
    fr.furniture_state as dimension_value,
    COUNT(*) as property_count,
    AVG(f.price) as avg_price
FROM fact_property f
JOIN dim_furniture fr ON f.furniture_id = fr.furniture_id
GROUP BY fr.furniture_state
HAVING COUNT(*) > 20

ORDER BY aggregation_level, property_count DESC;

-- =====================================================
-- Performance Tips for Iceberg Cubes
-- =====================================================

/*
1. INDEXING: Create indexes on dimension foreign keys
   CREATE INDEX idx_fact_location ON fact_property(location_id);
   CREATE INDEX idx_fact_legal ON fact_property(legal_id);
   CREATE INDEX idx_fact_furniture ON fact_property(furniture_id);

2. MATERIALIZED VIEWS: For frequently queried cubes,
   consider creating materialized views.

3. PARTITIONING: Partition fact table by date or city
   for better query performance.

4. THRESHOLD TUNING: Adjust HAVING threshold based on
   data distribution to balance coverage vs. sparsity.

5. PRE-AGGREGATION: For very large datasets, pre-compute
   common aggregations in separate tables.
*/
