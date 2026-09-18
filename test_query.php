<?php
$files = ['application/config/database.php', 'application/modules/pasien/controllers/Content.php', 'public/index.php'];
foreach ($files as $file) {
    $c = file_get_contents($file);
    echo $file . ': ' . bin2hex(substr($c, 0, 3)) . "\n";
}
