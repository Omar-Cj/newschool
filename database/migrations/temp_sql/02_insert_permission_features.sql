-- ================================================================
-- Step 2: Insert All 95 New Permission Features
-- ================================================================
-- Purpose: Map all 102 permissions to permission_features table
-- Note: 7 records already exist (IDs: 24, 25, 26, 34, 39, 48, 49)
-- Total after migration: 102 permission_features records

-- ================================================================
-- FEATURE GROUP 1: Dashboard (1 feature)
-- ================================================================
INSERT INTO permission_features (permission_id, feature_group_id, name, description, is_premium, position, status, created_at, updated_at) VALUES
(1, 1, 'Dashboard Access', 'Access to main dashboard and calendar view', 0, 1, 1, NOW(), NOW());

-- ================================================================
-- FEATURE GROUP 2: Student Information (7 features)
-- ================================================================
INSERT INTO permission_features (permission_id, feature_group_id, name, description, is_premium, position, status, created_at, updated_at) VALUES
(2, 2, 'Student Management', 'Create, view, update, and delete student records', 0, 1, 1, NOW(), NOW()),
(3, 2, 'Student Categories', 'Organize students by categories for reporting', 0, 2, 1, NOW(), NOW()),
(4, 2, 'Student Promotion', 'Promote students to next grade level', 0, 3, 1, NOW(), NOW()),
(5, 2, 'Disabled Students', 'Manage records for students with disabilities', 0, 4, 1, NOW(), NOW()),
(6, 2, 'Parent Management', 'Manage parent and guardian information', 0, 5, 1, NOW(), NOW()),
(7, 2, 'Admissions', 'Handle new student admission process', 0, 6, 1, NOW(), NOW());
-- Note: permission_id 110 (Student Reports) already exists as feature_id 48

-- ================================================================
-- FEATURE GROUP 3: Academic Management (9 features)
-- ================================================================
INSERT INTO permission_features (permission_id, feature_group_id, name, description, is_premium, position, status, created_at, updated_at) VALUES
(8, 3, 'Class Management', 'Manage academic classes and grade levels', 0, 1, 1, NOW(), NOW()),
(9, 3, 'Section Management', 'Organize classes into sections', 0, 2, 1, NOW(), NOW()),
(10, 3, 'Shift Management', 'Configure morning/afternoon/evening shifts', 0, 3, 1, NOW(), NOW()),
(11, 3, 'Class Setup', 'Configure class structures and relationships', 0, 4, 1, NOW(), NOW()),
(12, 3, 'Subject Management', 'Manage academic subjects and curricula', 0, 5, 1, NOW(), NOW()),
(13, 3, 'Subject Assignment', 'Assign subjects to classes and teachers', 0, 6, 1, NOW(), NOW()),
(14, 3, 'Class Routine', 'View and manage weekly class schedules', 0, 7, 1, NOW(), NOW()),
(15, 3, 'Time Schedule', 'Configure time slots for periods', 0, 8, 1, NOW(), NOW()),
(16, 3, 'Classroom Management', 'Manage physical classroom assignments', 0, 9, 1, NOW(), NOW());

-- ================================================================
-- FEATURE GROUP 4: Fees Management (9 features)
-- ================================================================
-- Note: Features 206 (cash_transfer), 98 (parent_deposit), 99 (parent_statement) already exist
INSERT INTO permission_features (permission_id, feature_group_id, name, description, is_premium, position, status, created_at, updated_at) VALUES
(17, 4, 'Fee Groups', 'Organize fees into logical groups', 0, 1, 1, NOW(), NOW()),
(18, 4, 'Fee Types', 'Define types of fees (tuition, transport, etc)', 0, 2, 1, NOW(), NOW()),
(19, 4, 'Fee Master', 'Manage fee structures and amounts', 0, 3, 1, NOW(), NOW()),
(20, 4, 'Fee Assignment', 'Assign fees to students and classes', 0, 4, 1, NOW(), NOW()),
(21, 4, 'Fee Collection', 'Collect and record fee payments', 0, 5, 1, NOW(), NOW()),
(22, 4, 'Discount Setup', 'Configure sibling and early payment discounts', 0, 6, 1, NOW(), NOW()),
(70, 4, 'Tax Setup', 'Configure tax rates for fee calculations', 0, 7, 1, NOW(), NOW()),
(91, 4, 'Fee Generation', 'Automatically generate fees for students', 0, 8, 1, NOW(), NOW());
-- Note: permission_id 206, 98, 99 already exist at positions 9, 10, 11

-- ================================================================
-- FEATURE GROUP 5: Examination (12 features)
-- ================================================================
-- Note: Feature 109 (exam_entry) already exists as feature_id 34
INSERT INTO permission_features (permission_id, feature_group_id, name, description, is_premium, position, status, created_at, updated_at) VALUES
(23, 5, 'Exam Types', 'Define examination types (midterm, final, etc)', 0, 1, 1, NOW(), NOW()),
(24, 5, 'Marks Grading', 'Configure grading scales and letter grades', 0, 2, 1, NOW(), NOW()),
(25, 5, 'Exam Assignment', 'Assign exams to classes and subjects', 0, 3, 1, NOW(), NOW()),
(26, 5, 'Exam Routine', 'View and manage exam schedules', 0, 4, 1, NOW(), NOW()),
(27, 5, 'Marks Register', 'Record and update exam marks', 0, 5, 1, NOW(), NOW()),
(28, 5, 'Homework', 'Assign and track homework assignments', 0, 6, 1, NOW(), NOW()),
(29, 5, 'Exam Settings', 'Configure examination system settings', 0, 7, 1, NOW(), NOW()),
(105, 5, 'Academic Terms (View)', 'View academic term information', 0, 9, 1, NOW(), NOW()),
(106, 5, 'Academic Terms (Create)', 'Create new academic terms', 0, 10, 1, NOW(), NOW()),
(107, 5, 'Academic Terms (Update)', 'Update academic term details', 0, 11, 1, NOW(), NOW()),
(108, 5, 'Academic Terms (Delete)', 'Delete academic terms', 0, 12, 1, NOW(), NOW());
-- Note: permission_id 109 (exam_entry) already exists at position 8

-- ================================================================
-- FEATURE GROUP 6: Accounts (4 features)
-- ================================================================
-- Note: Feature 112 (expense_category) already exists as feature_id 39
INSERT INTO permission_features (permission_id, feature_group_id, name, description, is_premium, position, status, created_at, updated_at) VALUES
(30, 6, 'Account Heads', 'Manage chart of accounts structure', 0, 1, 1, NOW(), NOW()),
(31, 6, 'Income Management', 'Record income transactions', 0, 2, 1, NOW(), NOW()),
(32, 6, 'Expense Management', 'Record and track expenses', 0, 3, 1, NOW(), NOW());
-- Note: permission_id 112 (expense_category) already exists at position 4

-- ================================================================
-- FEATURE GROUP 7: Attendance (2 features)
-- ================================================================
INSERT INTO permission_features (permission_id, feature_group_id, name, description, is_premium, position, status, created_at, updated_at) VALUES
(33, 7, 'Attendance Marking', 'Mark daily student attendance', 0, 1, 1, NOW(), NOW()),
(34, 7, 'Attendance Reports', 'View attendance statistics and reports', 0, 2, 1, NOW(), NOW());

-- ================================================================
-- FEATURE GROUP 8: Reports (7 features)
-- ================================================================
-- Note: Features 110 (student_reports) and 111 (report_center) already exist as feature_ids 48, 49
INSERT INTO permission_features (permission_id, feature_group_id, name, description, is_premium, position, status, created_at, updated_at) VALUES
(35, 8, 'Marksheet', 'Generate student exam marksheets', 0, 1, 1, NOW(), NOW()),
(36, 8, 'Merit List', 'Generate class merit/rank lists', 0, 2, 1, NOW(), NOW()),
(37, 8, 'Progress Card', 'Generate progress report cards', 0, 3, 1, NOW(), NOW()),
(38, 8, 'Due Fees Report', 'View outstanding fee reports', 0, 4, 1, NOW(), NOW()),
(39, 8, 'Fee Collection Report', 'View fee collection statistics', 0, 5, 1, NOW(), NOW()),
(40, 8, 'Account Reports', 'View financial accounting reports', 0, 6, 1, NOW(), NOW());
-- Note: permission_id 110 and 111 already exist at positions 9, 10

-- ================================================================
-- FEATURE GROUP 9: Library (5 features)
-- ================================================================
INSERT INTO permission_features (permission_id, feature_group_id, name, description, is_premium, position, status, created_at, updated_at) VALUES
(71, 9, 'Book Categories', 'Categorize library books by genre/subject', 0, 1, 1, NOW(), NOW()),
(72, 9, 'Book Management', 'Manage library book inventory', 0, 2, 1, NOW(), NOW()),
(73, 9, 'Library Members', 'Manage library membership records', 0, 3, 1, NOW(), NOW()),
(74, 9, 'Member Categories', 'Define member types (student, teacher, etc)', 0, 4, 1, NOW(), NOW()),
(75, 9, 'Book Issuance', 'Issue and return library books', 0, 5, 1, NOW(), NOW());

-- ================================================================
-- FEATURE GROUP 10: Online Examination (4 features - ALL PREMIUM)
-- ================================================================
INSERT INTO permission_features (permission_id, feature_group_id, name, description, is_premium, position, status, created_at, updated_at) VALUES
(76, 10, 'Online Exam Types', 'Configure online examination types', 1, 1, 1, NOW(), NOW()),
(77, 10, 'Question Groups', 'Organize questions into topic groups', 1, 2, 1, NOW(), NOW()),
(78, 10, 'Question Bank', 'Manage question repository', 1, 3, 1, NOW(), NOW()),
(79, 10, 'Online Exams', 'Conduct online examinations', 1, 4, 1, NOW(), NOW());

-- ================================================================
-- FEATURE GROUP 11: Staff Management (5 features)
-- ================================================================
INSERT INTO permission_features (permission_id, feature_group_id, name, description, is_premium, position, status, created_at, updated_at) VALUES
(41, 11, 'Language Management', 'Manage system language translations', 0, 1, 1, NOW(), NOW()),
(42, 11, 'Role Management', 'Configure user roles and permissions', 0, 2, 1, NOW(), NOW()),
(43, 11, 'User Management', 'Manage system user accounts', 0, 3, 1, NOW(), NOW()),
(44, 11, 'Department Management', 'Organize staff into departments', 0, 4, 1, NOW(), NOW()),
(45, 11, 'Designation Management', 'Define staff positions and titles', 0, 5, 1, NOW(), NOW());

-- ================================================================
-- FEATURE GROUP 12: Website (16 features - MIXED)
-- ================================================================
-- Premium: CMS features (sections, slider, about, counter, contact_info, dep_contact, news, event, gallery_category, gallery, subscribe, contact_message)
-- Basic: ID cards and certificates
INSERT INTO permission_features (permission_id, feature_group_id, name, description, is_premium, position, status, created_at, updated_at) VALUES
(46, 12, 'Page Sections', 'Manage website page sections', 1, 1, 1, NOW(), NOW()),
(47, 12, 'Slider Management', 'Manage homepage image sliders', 1, 2, 1, NOW(), NOW()),
(48, 12, 'About Page', 'Manage about page content', 1, 3, 1, NOW(), NOW()),
(49, 12, 'Counters', 'Manage achievement counter widgets', 1, 4, 1, NOW(), NOW()),
(50, 12, 'Contact Information', 'Manage school contact details', 1, 5, 1, NOW(), NOW()),
(51, 12, 'Department Contacts', 'Manage department contact information', 1, 6, 1, NOW(), NOW()),
(52, 12, 'News Management', 'Publish school news and updates', 1, 7, 1, NOW(), NOW()),
(53, 12, 'Event Management', 'Manage school events calendar', 1, 8, 1, NOW(), NOW()),
(54, 12, 'Gallery Categories', 'Organize photo gallery categories', 1, 9, 1, NOW(), NOW()),
(55, 12, 'Gallery Management', 'Manage photo galleries', 1, 10, 1, NOW(), NOW()),
(56, 12, 'Newsletter Subscriptions', 'View newsletter subscriber list', 1, 11, 1, NOW(), NOW()),
(57, 12, 'Contact Messages', 'View contact form submissions', 1, 12, 1, NOW(), NOW()),
(80, 12, 'ID Card Templates', 'Design student/staff ID cards', 0, 13, 1, NOW(), NOW()),
(81, 12, 'ID Card Generation', 'Generate and print ID cards', 0, 14, 1, NOW(), NOW()),
(82, 12, 'Certificate Templates', 'Design certificate templates', 0, 15, 1, NOW(), NOW()),
(83, 12, 'Certificate Generation', 'Generate and print certificates', 0, 16, 1, NOW(), NOW());

-- ================================================================
-- FEATURE GROUP 13: Settings (16 features)
-- ================================================================
INSERT INTO permission_features (permission_id, feature_group_id, name, description, is_premium, position, status, created_at, updated_at) VALUES
(58, 13, 'General Settings', 'Configure general system settings', 0, 1, 1, NOW(), NOW()),
(59, 13, 'Storage Settings', 'Configure file storage (local/S3)', 0, 2, 1, NOW(), NOW()),
(60, 13, 'Task Scheduler', 'Manage automated scheduled tasks', 0, 3, 1, NOW(), NOW()),
(61, 13, 'Software Updates', 'Manage system version updates', 0, 4, 1, NOW(), NOW()),
(62, 13, 'reCAPTCHA Settings', 'Configure spam protection', 0, 5, 1, NOW(), NOW()),
(63, 13, 'Payment Gateway', 'Configure payment methods (Stripe, PayPal)', 0, 6, 1, NOW(), NOW()),
(64, 13, 'Email Configuration', 'Configure SMTP email settings', 0, 7, 1, NOW(), NOW()),
(65, 13, 'SMS Configuration', 'Configure SMS gateway settings', 0, 8, 1, NOW(), NOW()),
(66, 13, 'Gender Options', 'Manage gender options for forms', 0, 9, 1, NOW(), NOW()),
(67, 13, 'Religion Options', 'Manage religion options for forms', 0, 10, 1, NOW(), NOW()),
(68, 13, 'Blood Group Options', 'Manage blood group types', 0, 11, 1, NOW(), NOW()),
(69, 13, 'Academic Sessions', 'Manage academic year sessions', 0, 12, 1, NOW(), NOW()),
(84, 13, 'Google Meet Integration', 'Configure Google Meet for virtual classes', 0, 13, 1, NOW(), NOW()),
(85, 13, 'Notice Board', 'Manage school announcements', 0, 14, 1, NOW(), NOW()),
(86, 13, 'Message Templates', 'Manage SMS and email templates', 0, 15, 1, NOW(), NOW()),
(87, 13, 'Send Messages', 'Send SMS and email to users', 0, 16, 1, NOW(), NOW());

-- ================================================================
-- FEATURE GROUP 14: Community (3 features - ALL PREMIUM)
-- ================================================================
INSERT INTO permission_features (permission_id, feature_group_id, name, description, is_premium, position, status, created_at, updated_at) VALUES
(88, 14, 'Forum Management', 'Manage discussion forums', 1, 1, 1, NOW(), NOW()),
(89, 14, 'Forum Comments', 'Moderate forum comments', 1, 2, 1, NOW(), NOW()),
(90, 14, 'School Memories', 'Share and manage school memory posts', 1, 3, 1, NOW(), NOW());

-- ================================================================
-- VERIFICATION QUERIES
-- ================================================================
-- Check total count
SELECT COUNT(*) as total_permission_features FROM permission_features;
-- Expected: 102

-- Check premium count
SELECT COUNT(*) as premium_features FROM permission_features WHERE is_premium = 1;
-- Expected: 27

-- Check for missing permissions
SELECT p.id, p.attribute
FROM permissions p
LEFT JOIN permission_features pf ON p.id = pf.permission_id
WHERE pf.id IS NULL
ORDER BY p.id;
-- Expected: 0 rows (all mapped)

-- Feature distribution by group
SELECT
    fg.id,
    fg.name,
    COUNT(pf.id) as feature_count,
    SUM(CASE WHEN pf.is_premium = 1 THEN 1 ELSE 0 END) as premium_count,
    SUM(CASE WHEN pf.is_premium = 0 THEN 1 ELSE 0 END) as basic_count
FROM feature_groups fg
LEFT JOIN permission_features pf ON fg.id = pf.feature_group_id
GROUP BY fg.id, fg.name
ORDER BY fg.position;
