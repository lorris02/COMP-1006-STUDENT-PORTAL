<?php
require_once __DIR__ . '/bootstrap.php';
verifyPost();
if (!demoMode()) {
    http_response_code(403);
    exit('Reset is only available in demo mode.');
}
(new crud())->resetDemo();
flash('Demo records restored.');
redirect('view.php');
