<?php
require_once __DIR__ . '/database.php';

class crud extends database
{
    public function __construct() {
        parent::__construct();
        if ($this->conn === null && !isset($_SESSION['students'])) {
            $this->resetDemo();
        }
    }
    public function resetDemo(): void {
        $_SESSION['students'] = [
            '100001' => ['student_id' => '100001', 'name' => 'Alex Morgan', 'age' => 21, 'gender' => 'other', 'student_grade' => 88.5],
            '100002' => ['student_id' => '100002', 'name' => 'Maya Chen', 'age' => 24, 'gender' => 'female', 'student_grade' => 94],
            '100003' => ['student_id' => '100003', 'name' => 'Jordan Taylor', 'age' => 19, 'gender' => 'male', 'student_grade' => 72],
        ];
    }
    public function all(): array {
        if ($this->conn === null) {
            return array_values($_SESSION['students']);
        }
        return $this->conn->query('SELECT student_id, name, age, gender, student_grade FROM students')->fetchAll();
    }
    public function find(string $id): ?array {
        if ($this->conn === null) {
            return $_SESSION['students'][$id] ?? null;
        }
        $statement = $this->conn->prepare('SELECT student_id, name, age, gender, student_grade FROM students WHERE student_id = ?');
        $statement->execute([$id]);
        return $statement->fetch() ?: null;
    }
    public function save(array $data, string $originalId = ''): void {
        $existing = $this->find($data['student_id']);
        if ($existing !== null && $data['student_id'] !== $originalId) {
            throw new DomainException('This student ID is already in use.');
        }
        if ($originalId !== '' && $this->find($originalId) === null) {
            throw new DomainException('This student no longer exists.');
        }
        $record = ['student_id' => $data['student_id'], 'name' => $data['name'], 'age' => (int) $data['age'], 'gender' => $data['gender'], 'student_grade' => (float) $data['grade']];
        if ($this->conn === null) {
            if ($originalId !== '') {
                unset($_SESSION['students'][$originalId]);
            }
            $_SESSION['students'][$data['student_id']] = $record;
            return;
        }
        $values = array_values($record);
        if ($originalId === '') {
            $statement = $this->conn->prepare('INSERT INTO students (student_id, name, age, gender, student_grade) VALUES (?, ?, ?, ?, ?)');
        } else {
            $statement = $this->conn->prepare('UPDATE students SET student_id = ?, name = ?, age = ?, gender = ?, student_grade = ? WHERE student_id = ?');
            $values[] = $originalId;
        }
        try {
            $statement->execute($values);
        } catch (PDOException $error) {
            if (($error->errorInfo[1] ?? null) === 1062) {
                throw new DomainException('This student ID is already in use.');
            }
            throw $error;
        }
    }
    public function delete(string $id): void {
        if ($this->conn === null) {
            unset($_SESSION['students'][$id]);
            return;
        }
        $statement = $this->conn->prepare('DELETE FROM students WHERE student_id = ?');
        $statement->execute([$id]);
    }
}
