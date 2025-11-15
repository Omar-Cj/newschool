-- ================================================================
-- Step 3: Assign Features to Package 1 (Basic Package)
-- ================================================================
-- Purpose: Assign all 75 non-premium features to Package 1
-- Premium features (27) are excluded from Basic Package

-- Insert all non-premium features into Package 1
INSERT INTO package_permission_features (package_id, permission_feature_id, created_at, updated_at)
SELECT 1, id, NOW(), NOW()
FROM permission_features
WHERE is_premium = 0
  AND status = 1
  AND id NOT IN (
    SELECT permission_feature_id
    FROM package_permission_features
    WHERE package_id = 1
  );

-- ================================================================
-- VERIFICATION QUERIES
-- ================================================================

-- 1. Check total features in Package 1
SELECT COUNT(*) as total_features
FROM package_permission_features
WHERE package_id = 1;
-- Expected: ~75 features

-- 2. Verify no premium features in Package 1
SELECT pf.id, pf.name, pf.is_premium
FROM package_permission_features ppf
JOIN permission_features pf ON ppf.permission_feature_id = pf.id
WHERE ppf.package_id = 1 AND pf.is_premium = 1;
-- Expected: 0 rows (no premium features in Basic Package)

-- 3. Show Package 1 feature breakdown by group
SELECT
    fg.name as feature_group,
    COUNT(ppf.id) as feature_count
FROM package_permission_features ppf
JOIN permission_features pf ON ppf.permission_feature_id = pf.id
JOIN feature_groups fg ON pf.feature_group_id = fg.id
WHERE ppf.package_id = 1
GROUP BY fg.id, fg.name
ORDER BY fg.position;

-- 4. Show excluded premium features
SELECT
    fg.name as feature_group,
    pf.name as feature_name,
    'PREMIUM - Not in Basic Package' as status
FROM permission_features pf
JOIN feature_groups fg ON pf.feature_group_id = fg.id
WHERE pf.is_premium = 1
ORDER BY fg.position, pf.position;
-- Expected: 27 rows (all premium features)

-- 5. Detailed Package 1 feature list
SELECT
    fg.name as feature_group,
    pf.name as feature_name,
    pf.description,
    p.attribute as permission_attribute
FROM package_permission_features ppf
JOIN permission_features pf ON ppf.permission_feature_id = pf.id
JOIN feature_groups fg ON pf.feature_group_id = fg.id
JOIN permissions p ON pf.permission_id = p.id
WHERE ppf.package_id = 1
ORDER BY fg.position, pf.position;
