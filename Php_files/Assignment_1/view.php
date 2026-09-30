<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = 'Student records';
$activePage = 'records';
$failed = false;
try {
    $all = (new crud())->all();
} catch (Throwable $error) {
    error_log($error->getMessage());
    http_response_code(503);
    $all = [];
    $failed = true;
}
$count = count($all);
$average = $count ? array_sum(array_column($all, 'student_grade')) / $count : 0;
$passing = count(array_filter($all, fn($student) => (float) $student['student_grade'] >= 50));
$search = substr(input($_GET, 'q'), 0, 100);
$sort = input($_GET, 'sort');
if (!in_array($sort, ['name', 'grade', 'id'], true)) {
    $sort = 'name';
}
$rows = array_values(array_filter($all, fn($student) => $search === '' || stripos($student['name'], $search) !== false || strpos((string) $student['student_id'], $search) !== false));
usort($rows, function($a, $b) use ($sort) {
    if ($sort === 'grade') {
        return $b['student_grade'] <=> $a['student_grade'];
    }
    if ($sort === 'id') {
        return strcmp((string) $a['student_id'], (string) $b['student_id']);
    }
    return strcasecmp($a['name'], $b['name']);
});
$pages = max(1, (int) ceil(count($rows) / 10));
$page = min($pages, max(1, (int) input($_GET, 'page')));
$visible = array_slice($rows, ($page - 1) * 10, 10);
include __DIR__ . '/header.php';
?>
<main id="main" class="shell">
    <div class="page-heading heading-row"><div><p class="eyebrow">STUDENT MANAGEMENT</p><h1>Student records<span class="title-dot">.</span></h1><p>A clear view of your students and their progress.</p></div><a class="button" href="index.php">+ Add student</a></div>
    <?php if ($failed): ?><div class="error-summary" role="alert">The database is unavailable. Please try again later.</div><?php endif; ?>
    <div class="stats"><div class="stat"><span>Total students</span><strong><?= $count ?></strong><small>In your records</small></div><div class="stat"><span>Average grade</span><strong><?= number_format($average, 1) ?><em>%</em></strong><small>Across all students</small></div><div class="stat"><span>Passing students</span><strong><?= $passing ?></strong><small>Grade of 50% or higher</small></div></div>
    <section class="panel records-panel" aria-labelledby="directory-title">
        <div class="directory-heading"><h2 id="directory-title">Student directory</h2><span class="count-badge"><?= count($rows) ?> records</span></div>
        <form method="get" action="view.php" class="search-bar"><div class="field search-field"><label for="q">Search by name or student ID</label><input type="search" id="q" name="q" maxlength="100" value="<?= escape($search) ?>" placeholder="Find a student..."></div><div class="field"><label for="sort">Sort by</label><select id="sort" name="sort"><?php foreach (['name' => 'Name: A–Z', 'grade' => 'Grade: highest first', 'id' => 'Student ID'] as $value => $label): ?><option value="<?= $value ?>" <?= $sort === $value ? 'selected' : '' ?>><?= $label ?></option><?php endforeach; ?></select></div><button class="button secondary" type="submit">Apply</button><?php if ($search !== ''): ?><a class="text-link" href="view.php">Clear</a><?php endif; ?></form>
        <div class="table-wrap"><table><caption class="sr-only">Student records with edit and delete actions</caption><thead><tr><th scope="col">Student</th><th scope="col">Student ID</th><th scope="col">Age</th><th scope="col">Gender</th><th scope="col">Grade</th><th scope="col">Actions</th></tr></thead><tbody>
        <?php foreach ($visible as $student): ?>
        <tr><td><span class="student-name"><?= escape($student['name']) ?></span></td><td class="mono"><?= escape($student['student_id']) ?></td><td><?= escape($student['age']) ?></td><td><?= escape(ucfirst($student['gender'])) ?></td><td><span class="grade <?= $student['student_grade'] >= 50 ? 'passing' : 'low' ?>"><?= escape(rtrim(rtrim(number_format((float) $student['student_grade'], 2, '.', ''), '0'), '.')) ?>%</span></td><td><div class="row-actions"><a class="text-link" href="index.php?id=<?= rawurlencode($student['student_id']) ?>" aria-label="Edit <?= escape($student['name']) ?>">Edit</a><form action="delete.php" method="post"><input type="hidden" name="csrf" value="<?= escape(csrfToken()) ?>"><input type="hidden" name="id" value="<?= escape($student['student_id']) ?>"><button class="delete-link" aria-label="Delete <?= escape($student['name']) ?>">Delete</button></form></div></td></tr>
        <?php endforeach; ?>
        <?php if (!$visible): ?><tr><td colspan="6" class="empty-state"><strong><?= $search === '' ? 'No students yet' : 'No matching students' ?></strong><p><?= $search === '' ? 'Add a student to get started.' : 'Try a different name or student ID.' ?></p></td></tr><?php endif; ?>
        </tbody></table></div>
        <div class="table-footer"><span>Showing <?= count($visible) ?> of <?= count($rows) ?> records</span><nav aria-label="Pagination"><?php if ($page > 1): ?><a href="?<?= escape(http_build_query(['q' => $search, 'sort' => $sort, 'page' => $page - 1])) ?>">Previous</a><?php endif; ?><span>Page <?= $page ?> of <?= $pages ?></span><?php if ($page < $pages): ?><a href="?<?= escape(http_build_query(['q' => $search, 'sort' => $sort, 'page' => $page + 1])) ?>">Next</a><?php endif; ?></nav></div>
    </section>
    <?php if (demoMode()): ?><form method="post" action="reset.php" class="reset-form"><input type="hidden" name="csrf" value="<?= escape(csrfToken()) ?>"><span>Want a fresh start?</span><button class="text-link">Restore sample records</button></form><?php endif; ?>
</main>
<?php include __DIR__ . '/footer.php'; ?>
