-- ================================================
-- Step 1: Create Feature Group 14 "Community"
-- ================================================
-- Purpose: Create dedicated feature group for forums, discussions, and school memories
-- Execution: Run via DB client or tinker

INSERT INTO feature_groups (id, name, slug, description, icon, position, status, created_at, updated_at)
VALUES (14, 'Community', 'community', 'Forums, discussions, and school memories', 'fas fa-users', 14, 1, NOW(), NOW());

-- Verification
SELECT * FROM feature_groups WHERE id = 14;
-- Expected: 1 row with Community group
