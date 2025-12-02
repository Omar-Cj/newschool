## Issues with Examination Reports

### 1. Student Exam Report - Duplicate Results
- When generating the student exam report, the results for that particular student duplicated.
- This leads to confusion when viewing or exporting the report as the same marks and details are shown multiple times for a single exam/student.

### 2. Student Gradebook - No Data Displayed
- The student gradebook is currently not showing any data.
- Even after entering all required parameters, the gradebook subject sections remains empty or does not fetch/display the relevant student scores.


**Here is the strucure of student Exam Report Procedure**

**

create
    definer = root@localhost procedure GetStudentExamReport(IN p_session_id bigint, IN p_term_id bigint,
                                                            IN p_exam_type_id bigint, IN p_class_id bigint,
                                                            IN p_section_id bigint, IN p_student_id bigint,
                                                            IN p_branch_id int, IN p_school_id int)
BEGIN
                SELECT
                    s.name AS subject_name,
                    eer.obtained_marks AS result,
                    CASE
                        WHEN eer.is_absent = 1 THEN 'Absent'
                        WHEN eer.obtained_marks IS NULL THEN 'Not Graded'
                        ELSE COALESCE(mg.name, 'N/A')
                    END AS grade,
                    COALESCE(ee.total_marks, 100) AS total_marks
                FROM
                    subject_assigns sa
                INNER JOIN
                    subject_assign_childrens sac ON sa.id = sac.subject_assign_id
                INNER JOIN
                    subjects s ON sac.subject_id = s.id
                LEFT JOIN
                    exam_entries ee ON (
                        ee.class_id = p_class_id
                        AND ee.section_id = p_section_id
                        AND ee.exam_type_id = p_exam_type_id
                        AND ee.session_id = p_session_id
                        AND ee.term_id = p_term_id
                        AND ee.status = 'published'
                        AND (
                            ee.subject_id = s.id
                            OR ee.is_all_subjects = 1
                        )
                    )
                LEFT JOIN
                    exam_entry_results eer ON (
                        eer.exam_entry_id = ee.id
                        AND eer.student_id = p_student_id
                        AND eer.subject_id = s.id
                    )
                LEFT JOIN
                    marks_grades mg ON (
                        eer.is_absent = 0
                        AND eer.obtained_marks IS NOT NULL
                        AND ee.total_marks IS NOT NULL
                        AND ee.total_marks > 0
                        AND (eer.obtained_marks / ee.total_marks) * 100 >= mg.percent_from
                        AND (eer.obtained_marks / ee.total_marks) * 100 <= mg.percent_upto
                    )
                INNER JOIN
                    students st ON st.id = p_student_id
                WHERE
                    sa.session_id = p_session_id
                    AND sa.classes_id = p_class_id
                    AND sa.section_id = p_section_id
                    AND (p_branch_id IS NULL OR st.branch_id = p_branch_id)
                    AND (p_school_id IS NULL OR st.school_id = p_school_id)
                ORDER BY
                    s.name;
            END;

**


**Here is the structure of StudentGradebook Report**


**
create
    definer = root@localhost procedure GetStudentGradebook(IN p_session_id bigint, IN p_term_id bigint,
                                                           IN p_class_id bigint, IN p_section_id bigint,
                                                           IN p_student_id bigint, IN p_branch_id int,
                                                           IN p_school_id int)
BEGIN
                DECLARE v_student_exists INT;

                SELECT COUNT(*)
                INTO v_student_exists
                FROM session_class_students scs
                INNER JOIN students s ON scs.student_id = s.id
                WHERE scs.session_id = p_session_id
                  AND scs.student_id = p_student_id
                  AND scs.classes_id = p_class_id
                  AND scs.section_id = p_section_id
                  AND (p_branch_id IS NULL OR s.branch_id = p_branch_id)
                  AND (p_school_id IS NULL OR s.school_id = p_school_id);

                IF v_student_exists = 0 THEN
                    SELECT 'Student not enrolled in the specified class and section for this session' AS error_message;
                ELSE
                    SELECT
                        subj.name AS subject_name,
                        MAX(CASE
                            WHEN et.id = 6 THEN
                                CASE
                                    WHEN eer.is_absent = 1 THEN 'Absent'
                                    WHEN eer.obtained_marks IS NULL THEN '-'
                                    ELSE CAST(COALESCE(eer.obtained_marks, 0) AS CHAR)
                                END
                        END) AS monthly_exam_1,
                        MAX(CASE
                            WHEN et.id = 4 THEN
                                CASE
                                    WHEN eer.is_absent = 1 THEN 'Absent'
                                    WHEN eer.obtained_marks IS NULL THEN '-'
                                    ELSE CAST(COALESCE(eer.obtained_marks, 0) AS CHAR)
                                END
                        END) AS mid_term,
                        MAX(CASE
                            WHEN et.id = 5 THEN
                                CASE
                                    WHEN eer.is_absent = 1 THEN 'Absent'
                                    WHEN eer.obtained_marks IS NULL THEN '-'
                                    ELSE CAST(COALESCE(eer.obtained_marks, 0) AS CHAR)
                                END
                        END) AS monthly_exam_2,
                        MAX(CASE
                            WHEN et.id = 7 THEN
                                CASE
                                    WHEN eer.is_absent = 1 THEN 'Absent'
                                    WHEN eer.obtained_marks IS NULL THEN '-'
                                    ELSE CAST(COALESCE(eer.obtained_marks, 0) AS CHAR)
                                END
                        END) AS final_term
                    FROM
                        students st
                    INNER JOIN
                        session_class_students scs ON st.id = scs.student_id
                            AND scs.session_id = p_session_id
                            AND scs.student_id = p_student_id
                            AND scs.classes_id = p_class_id
                            AND scs.section_id = p_section_id
                    INNER JOIN
                        subject_assigns sa ON sa.session_id = p_session_id
                            AND sa.classes_id = p_class_id
                            AND sa.section_id = p_section_id
                    INNER JOIN
                        subject_assign_childrens sac ON sa.id = sac.subject_assign_id
                    INNER JOIN
                        subjects subj ON sac.subject_id = subj.id
                            AND subj.status = 1
                    LEFT JOIN
                        exam_entries ee ON ee.class_id = p_class_id
                            AND ee.section_id = p_section_id
                            AND ee.session_id = p_session_id
                            AND ee.term_id = p_term_id
                            AND ee.status = 'published'
                            AND (ee.subject_id = subj.id OR ee.is_all_subjects = 1)
                    LEFT JOIN
                        exam_types et ON ee.exam_type_id = et.id
                            AND et.status = 1
                            AND et.id IN (4, 5, 6, 7)
                    LEFT JOIN
                        exam_entry_results eer ON eer.exam_entry_id = ee.id
                            AND eer.student_id = p_student_id
                            AND eer.subject_id = subj.id
                    WHERE
                        (p_branch_id IS NULL OR st.branch_id = p_branch_id)
                        AND (p_school_id IS NULL OR st.school_id = p_school_id)
                    GROUP BY
                        subj.id,
                        subj.name
                    ORDER BY
                        subj.name;
                END IF;
            END;

**


**here is the structure of exam entries table**

**

id,bigint unsigned,NO,PRI,,auto_increment
school_id,bigint unsigned,YES,MUL,,""
session_id,bigint unsigned,NO,MUL,,""
term_id,bigint unsigned,NO,MUL,,""
grade,varchar(255),YES,MUL,,""
class_id,bigint unsigned,NO,MUL,,""
section_id,bigint unsigned,NO,MUL,,""
branch_id,bigint unsigned,YES,MUL,,""
exam_type_id,bigint unsigned,NO,MUL,,""
subject_id,bigint unsigned,YES,MUL,,""
is_all_subjects,tinyint(1),NO,"",0,""
entry_method,"enum('manual','excel')",NO,"",manual,""
upload_file_path,varchar(255),YES,"",,""
total_marks,double,NO,"",100,""
status,"enum('draft','completed','published')",NO,MUL,draft,""
created_by,bigint unsigned,NO,MUL,,""
published_at,timestamp,YES,"",,""
created_at,timestamp,YES,"",,""
updated_at,timestamp,YES,"",,""

**

**Here is the structure of Exam_entry_results table**


**

id,bigint unsigned,NO,PRI,,auto_increment
school_id,bigint unsigned,YES,MUL,,""
session_id,bigint unsigned,NO,MUL,,""
term_id,bigint unsigned,NO,MUL,,""
grade,varchar(255),YES,MUL,,""
class_id,bigint unsigned,NO,MUL,,""
section_id,bigint unsigned,NO,MUL,,""
branch_id,bigint unsigned,YES,MUL,,""
exam_type_id,bigint unsigned,NO,MUL,,""
subject_id,bigint unsigned,YES,MUL,,""
is_all_subjects,tinyint(1),NO,"",0,""
entry_method,"enum('manual','excel')",NO,"",manual,""
upload_file_path,varchar(255),YES,"",,""
total_marks,double,NO,"",100,""
status,"enum('draft','completed','published')",NO,MUL,draft,""
created_by,bigint unsigned,NO,MUL,,""
published_at,timestamp,YES,"",,""
created_at,timestamp,YES,"",,""
updated_at,timestamp,YES,"",,""
**

**Note:-**
- as you can see the exam_entry_results have school_id but does not have branch_id scope we should fix that because the school can have different branches so when fixing the exam entry addition both manually and the excel file should take acount in branch.



**Expected Behavior:**
- Each student/exam result should only show one entry in reports.
- Gradebook should always show the available data for each student.

