<?php
$hash = '0192023a7bbd73250516f069df18b500';
$words = ['admin', 'admin123', 'password', '123456'];
foreach($words as $w) {
    if(md5($w) === $hash) { echo "MD5 match: " . $w . "\n"; }
}
echo md5('admin123');
