<?php
// Reset PHP opcache to clear stale bytecode
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache has been reset successfully!<br>";
} else {
    echo "OPcache is not enabled.<br>";
}

// Also test if Google Client can be loaded
require __DIR__ . '/../vendor/autoload.php';
echo "Google\\Client exists: " . (class_exists('Google\\Client') ? 'Yes' : 'No') . "<br>";
echo "Google\\Service\\Drive exists: " . (class_exists('Google\\Service\\Drive') ? 'Yes' : 'No') . "<br>";
echo "Masbug\\Flysystem\\GoogleDriveAdapter exists: " . (class_exists('Masbug\\Flysystem\\GoogleDriveAdapter') ? 'Yes' : 'No') . "<br>";

// Delete this file after use for security
// unlink(__FILE__);
