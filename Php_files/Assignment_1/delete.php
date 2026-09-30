<?php
require_once __DIR__ . '/bootstrap.php';
verifyPost();
$id = input($_POST, 'id');
if (!preg_match('/^[0-9]{1,12}$/D', $id)) {
    http_response_code(400);
    exit('Invalid student ID.');
}
try {
    $records = new crud();
    $student = $records->find($id);
    if ($student === null) {
        http_response_code(404);
        exit('Student not found.');
    }
    if (input($_POST, 'confirm') === 'yes') {
        $records->delete($id);
        flash('Student deleted.');
        redirect('view.php');
    }
} catch (Throwable $error) {
    error_log($error->getMessage());
    http_response_code(503);
    exit('The record could not be loaded. Please try again later.');
}
$pageTitle = 'Delete student';
$activePage = 'records';
include __DIR__ . '/header.php';
?>
<main id="main" class="shell form-page"><div class="panel"><p class="eyebrow">CONFIRM DELETION</p><h1>Delete this student?</h1><p>You are deleting <strong><?= escape($student['name']) ?></strong> (ID <?= escape($id) ?>). This cannot be undone.</p><form method="post" action="delete.php"><input type="hidden" name="csrf" value="<?= escape(csrfToken()) ?>"><input type="hidden" name="id" value="<?= escape($id) ?>"><input type="hidden" name="confirm" value="yes"><div class="form-actions"><a class="button secondary" href="view.php">Keep student</a><button class="button danger" type="submit">Delete student</button></div></form></div></main>
<?php include __DIR__ . '/footer.php'; ?>
