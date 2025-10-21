#!/usr/bin/env php

<?php

use Qlcloud\Cloud\SecureCrypto;

require './vendor/autoload.php';

$argv = $_SERVER['argv'];
$argc = $_SERVER['argc'];

if ($argc < 2) {
    echo "Usage: php encrypt.php <encrypt_string>\n";
    exit(1);
}
$arg = $argv[1];

$base = new SecureCrypto();
$encrypt = $base->encrypt($arg);
$result = file_put_contents(__DIR__ . '/encrypt.txt', $encrypt);
if ($result) echo "success";

