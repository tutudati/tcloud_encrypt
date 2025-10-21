<?php

use Tcloud\Exam\SecureCrypto;

require '../../vendor/autoload.php';

$base = new SecureCrypto();
$encrypt = $base->encrypt("www.baidu.com");
var_dump($encrypt);

var_dump($base->decrypt($encrypt));

