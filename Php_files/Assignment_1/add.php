<?php
require_once __DIR__ . '/bootstrap.php';
verifyPost();
$data = [];
foreach (['name', 'student_id', 'age', 'gender', 'grade'] as $field) {
    $data[$field] = input($_POST, $field);
}
$id = input($_POST, 'original_id');
$errors = (new validate())->student($data);
if (!$errors) {
    try {
        (new crud())->save($data, $id);
        flash($id === '' ? 'Student added successfully.' : 'Student updated successfully.');
        redirect('view.php');
    } catch (DomainException $error) {
        $errors['student_id'] = $error->getMessage();
    } catch (Throwable $error) {
        error_log($error->getMessage());
        $errors['name'] = 'We could not save this record. Please try again later.';
    }
}
$_SESSION['form'] = ['data' => $data, 'errors' => $errors, 'id' => $id];
redirect('index.php' . ($id !== '' ? '?id=' . rawurlencode($id) : ''));

