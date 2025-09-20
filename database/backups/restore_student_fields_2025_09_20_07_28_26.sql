-- Student Fields Restoration Script
-- Generated: 2025-09-20 07:28:26
-- Backup Table: students_backup_2025_09_20_07_28_26

-- Restore student_ar_name field
ALTER TABLE students ADD COLUMN student_ar_name TEXT;
UPDATE students s SET student_ar_name = (SELECT student_ar_name FROM students_backup_2025_09_20_07_28_26 b WHERE b.id = s.id);

-- Restore nationality field
ALTER TABLE students ADD COLUMN nationality TEXT;
UPDATE students s SET nationality = (SELECT nationality FROM students_backup_2025_09_20_07_28_26 b WHERE b.id = s.id);

-- Restore cpr_no field
ALTER TABLE students ADD COLUMN cpr_no TEXT;
UPDATE students s SET cpr_no = (SELECT cpr_no FROM students_backup_2025_09_20_07_28_26 b WHERE b.id = s.id);

-- Restore spoken_lang_at_home field
ALTER TABLE students ADD COLUMN spoken_lang_at_home TEXT;
UPDATE students s SET spoken_lang_at_home = (SELECT spoken_lang_at_home FROM students_backup_2025_09_20_07_28_26 b WHERE b.id = s.id);

-- Restore student_id_certificate field
ALTER TABLE students ADD COLUMN student_id_certificate TEXT;
UPDATE students s SET student_id_certificate = (SELECT student_id_certificate FROM students_backup_2025_09_20_07_28_26 b WHERE b.id = s.id);

-- Restore emergency_contact field
ALTER TABLE students ADD COLUMN emergency_contact TEXT;
UPDATE students s SET emergency_contact = (SELECT emergency_contact FROM students_backup_2025_09_20_07_28_26 b WHERE b.id = s.id);

-- Restore health_status field
ALTER TABLE students ADD COLUMN health_status TEXT;
UPDATE students s SET health_status = (SELECT health_status FROM students_backup_2025_09_20_07_28_26 b WHERE b.id = s.id);

-- Restore rank_in_family field
ALTER TABLE students ADD COLUMN rank_in_family TEXT;
UPDATE students s SET rank_in_family = (SELECT rank_in_family FROM students_backup_2025_09_20_07_28_26 b WHERE b.id = s.id);

-- Restore siblings field
ALTER TABLE students ADD COLUMN siblings TEXT;
UPDATE students s SET siblings = (SELECT siblings FROM students_backup_2025_09_20_07_28_26 b WHERE b.id = s.id);

