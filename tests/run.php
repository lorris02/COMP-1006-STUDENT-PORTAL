<?php
require __DIR__ . '/../Php_files/Assignment_1/validate.php';
require __DIR__ . '/../Php_files/Assignment_1/crud.php';
putenv('APP_MODE=demo');
$_SESSION = [];
$checks = 0;
function check(bool $condition, string $label): void {
    global $checks;
    if (!$condition) {
        throw new RuntimeException('FAIL: ' . $label);
    }
    $checks++;
}
$validation = new validate();
$valid = ['name' => "O'Connor", 'student_id' => '00042', 'age' => '22', 'gender' => 'female', 'grade' => '0'];
check($validation->student($valid) === [], 'zero grade and leading-zero ID are valid');
foreach (['name' => '', 'student_id' => '123x', 'age' => '22.5', 'gender' => 'invalid', 'grade' => '101'] as $field => $value) {
    $invalid = $valid;
    $invalid[$field] = $value;
    check(isset($validation->student($invalid)[$field]), 'reject invalid ' . $field);
}
foreach (['-1', '1e2', '99.999', '8x5'] as $grade) {
    $invalid = $valid;
    $invalid['grade'] = $grade;
    check(isset($validation->student($invalid)['grade']), 'reject malformed grade');
}
$valid['grade'] = '100.00';
check($validation->student($valid) === [], 'upper grade boundary');
$records = new crud();
check(count($records->all()) === 3, 'sample records seeded');
$records->save($valid);
check($records->find('00042')['name'] === "O'Connor", 'create and find');
check(count($records->all()) === 4, 'record added');
try {
    $records->save($valid);
    check(false, 'reject duplicate');
} catch (DomainException $error) {
    check(count($records->all()) === 4, 'duplicate does not add a record');
}
$valid['student_id'] = '00043';
$valid['grade'] = '75.5';
$records->save($valid, '00042');
check($records->find('00042') === null && $records->find('00043')['student_grade'] === 75.5, 'edit and change ID');
$records->delete('00043');
check($records->find('00043') === null, 'delete');
$records->resetDemo();
check(count($records->all()) === 3, 'reset');
$_SESSION = [];
check(count((new crud())->all()) === 3, 'new session receives its own records');
echo "$checks checks passed.\n";
