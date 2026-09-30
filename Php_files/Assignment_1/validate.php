<?php
class validate
{
    // Validate every field before saving a student.
    public function student(array $data): array {
        $errors = [];
        $name = $data['name'] ?? '';
        if ($name === '' || strlen($name) > 100 || preg_match('/[\x00-\x1F\x7F]/', $name)) {
            $errors['name'] = 'Enter a name between 1 and 100 characters.';
        }
        if (!preg_match('/^[0-9]{1,12}$/D', $data['student_id'] ?? '')) {
            $errors['student_id'] = 'Use 1 to 12 digits for the student ID.';
        }
        $age = $data['age'] ?? '';
        if (!preg_match('/^[0-9]{1,3}$/D', $age) || (int) $age < 1 || (int) $age > 120) {
            $errors['age'] = 'Enter a whole-number age between 1 and 120.';
        }
        if (!in_array($data['gender'] ?? '', ['male', 'female', 'other'], true)) {
            $errors['gender'] = 'Choose a gender from the list.';
        }
        $grade = $data['grade'] ?? '';
        if (!preg_match('/^[0-9]{1,3}(\.[0-9]{1,2})?$/D', $grade) || (float) $grade > 100) {
            $errors['grade'] = 'Enter a grade from 0 to 100, with up to two decimal places.';
        }
        return $errors;
    }
}
