<?php
require_once __DIR__ . '/bootstrap.php';
$id = input($_GET, 'id');
$errors = [];
$data = ['name' => '', 'student_id' => '', 'age' => '', 'gender' => '', 'grade' => ''];
$unavailable = false;
if ($id !== '') {
    try {
        $student = (new crud())->find($id);
        if ($student === null) {
            http_response_code(404);
            $unavailable = true;
        } else {
            $data = $student;
            $data['grade'] = (string) $student['student_grade'];
        }
    } catch (Throwable $error) {
        error_log($error->getMessage());
        http_response_code(503);
        $unavailable = true;
    }
}
if (isset($_SESSION['form'])) {
    $form = $_SESSION['form'];
    unset($_SESSION['form']);
    $data = $form['data'];
    $errors = $form['errors'];
    $id = $form['id'];
}
$isEditing = $id !== '';
$pageTitle = $isEditing ? 'Edit student' : 'Add student';
$activePage = 'add';
include __DIR__ . '/header.php';
?>
<main id="main" class="shell form-page">
    <a class="back-link" href="view.php">&larr; Back to records</a>
    <div class="page-heading"><p class="eyebrow">STUDENT MANAGEMENT</p><h1><?= escape($pageTitle) ?></h1><p><?= $isEditing ? 'Update a record and save your changes.' : 'Keep student information organised in one place.' ?></p></div>
    <?php if ($unavailable): ?>
    <div class="panel"><h2>Record unavailable</h2><p>The student was not found, or the database is unavailable. Return to records and try again.</p></div>
    <?php else: ?>
    <form action="add.php" method="post" class="panel student-form">
        <input type="hidden" name="csrf" value="<?= escape(csrfToken()) ?>">
        <input type="hidden" name="original_id" value="<?= escape($id) ?>">
        <div class="panel-heading"><h2>Student details</h2><p>All fields are required.</p></div>
        <?php if ($errors): ?><div class="error-summary" role="alert"><strong>Check your entries</strong><ul><?php foreach ($errors as $field => $message): ?><li><a href="#<?= escape($field) ?>"><?= escape($message) ?></a></li><?php endforeach; ?></ul></div><?php endif; ?>
        <div class="form-grid">
        <?php foreach (['name' => ['Full name', 'text', 'e.g. Alex Morgan'], 'student_id' => ['Student ID', 'text', 'e.g. 100004'], 'age' => ['Age', 'number', 'e.g. 21'], 'grade' => ['Grade (%)', 'number', 'e.g. 85.5']] as $field => $settings): ?>
            <div class="field <?= $field === 'name' ? 'wide' : '' ?>">
                <label for="<?= $field ?>"><?= $settings[0] ?></label>
                <input id="<?= $field ?>" name="<?= $field ?>" type="<?= $settings[1] ?>" value="<?= escape($data[$field]) ?>" placeholder="<?= $settings[2] ?>" required
                    <?php if ($field === 'name'): ?>maxlength="100" autocomplete="name"<?php endif; ?>
                    <?php if ($field === 'student_id'): ?>maxlength="12" pattern="[0-9]{1,12}" inputmode="numeric"<?php endif; ?>
                    <?php if ($field === 'age'): ?>min="1" max="120" step="1"<?php endif; ?>
                    <?php if ($field === 'grade'): ?>min="0" max="100" step="0.01"<?php endif; ?>
                    <?= isset($errors[$field]) ? 'aria-invalid="true" aria-describedby="' . $field . '-error"' : '' ?>>
                <?php if (isset($errors[$field])): ?><small class="field-error" id="<?= $field ?>-error"><?= escape($errors[$field]) ?></small><?php endif; ?>
            </div>
        <?php endforeach; ?>
            <div class="field"><label for="gender">Gender</label><select id="gender" name="gender" required <?= isset($errors['gender']) ? 'aria-invalid="true" aria-describedby="gender-error"' : '' ?>><option value="">Choose an option</option><?php foreach (['male', 'female', 'other'] as $gender): ?><option value="<?= $gender ?>" <?= $data['gender'] === $gender ? 'selected' : '' ?>><?= ucfirst($gender) ?></option><?php endforeach; ?></select><?php if (isset($errors['gender'])): ?><small class="field-error" id="gender-error"><?= escape($errors['gender']) ?></small><?php endif; ?></div>
        </div>
        <div class="form-actions"><a class="button secondary" href="view.php">Cancel</a><button class="button" type="submit"><?= $isEditing ? 'Save changes' : 'Add student' ?></button></div>
    </form>
    <?php endif; ?>
</main>
<?php include __DIR__ . '/footer.php'; ?>
