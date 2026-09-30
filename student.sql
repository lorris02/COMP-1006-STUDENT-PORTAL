-- Import into a NEW database. Existing databases should use the migration.
CREATE TABLE students (
    student_id VARCHAR(12) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    age SMALLINT UNSIGNED NOT NULL,
    gender VARCHAR(10) NOT NULL,
    student_grade DECIMAL(5,2) NOT NULL,
    CONSTRAINT valid_age CHECK (age BETWEEN 1 AND 120),
    CONSTRAINT valid_grade CHECK (student_grade BETWEEN 0 AND 100),
    CONSTRAINT valid_gender CHECK (gender IN ('male', 'female', 'other'))
);
