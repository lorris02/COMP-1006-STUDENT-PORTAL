<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="A PHP student record management project by Jonathan Ilori.">
    <title><?= escape($pageTitle) ?> | Student Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>
<header class="site-header">
    <div class="shell header-inner">
        <a class="brand" href="view.php"><span class="brand-mark" aria-hidden="true">SP</span><span>Student Portal<small>Computer Programming · School project</small></span></a>
        <nav aria-label="Main navigation">
            <a href="view.php" <?= $activePage === 'records' ? 'aria-current="page"' : '' ?>>Records</a>
            <a href="index.php" <?= $activePage === 'add' ? 'aria-current="page"' : '' ?>>Add student</a>
        </nav>
    </div>
</header>
<?php if (demoMode()): ?>
<div class="shell demo-banner"><span class="status-dot" aria-hidden="true"></span><strong>Interactive demo</strong><span>Fictional records. Changes stay in your browser session. Please use sample data.</span></div>
<?php endif; ?>
<?php if (isset($_SESSION['message'])): ?>
<div class="shell notice" role="status"><?= escape($_SESSION['message']) ?></div>
<?php unset($_SESSION['message']); endif; ?>
