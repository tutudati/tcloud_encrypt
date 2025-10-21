<?php
declare(strict_types=1);

namespace Qlcloud\Cloud;

use Exception;

class Base
{
    public function __construct()
    {
        $this->checkDomain();
    }

    private function checkDomain(): void
    {
        try {
            $configObject = new Config();
            $config = $configObject->config;
            $crypto = new SecureCrypto();
            $decryptString = $crypto->decrypt($config["license"]);
            if ($config["current_domain"] !== $decryptString) {
                die("");
            }
        }catch (Exception) {
            die("");
        }
    }
}