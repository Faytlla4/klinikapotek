<?php
$dirs = ['application/', 'bonfire/'];
foreach ($dirs as $d) {
    $dir = new RecursiveDirectoryIterator($d);
    $ite = new RecursiveIteratorIterator($dir);
    foreach($ite as $f) {
        if(substr($f, -4) == '.php') {
            $c = file_get_contents($f);
            if(substr($c, 0, 3) == "\xef\xbb\xbf") {
                echo "Found BOM in: " . $f . "\n";
                file_put_contents($f, substr($c, 3));
            }
        }
    }
}
