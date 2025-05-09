<?php
$title = $title ?? 'Sales';
ob_start();
?>

<h2 class="text-2xl font-semibold mb-6">Sales</h2>
<p>This is a placeholder page for Sales management.</p>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>
