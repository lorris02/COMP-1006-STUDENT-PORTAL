-- Back up your database and inspect invalid/duplicate data BEFORE running.
-- Existing numeric IDs cannot recover leading zeros already lost.
ALTER TABLE students
    MODIFY student_id VARCHAR(12) NOT NULL,
    MODIFY name VARCHAR(100) NOT NULL,
    MODIFY age SMALLINT UNSIGNED NOT NULL,
    MODIFY student_grade DECIMAL(5,2) NOT NULL;
-- Keep the old grade column for now; the application uses student_grade.
