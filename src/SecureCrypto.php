<?php
declare(strict_types=1);

namespace Qlcloud\Cloud;

use Exception;

class SecureCrypto
{
    private string $publicKeyPath;

    private string $privateKeyPath;

    private string $passphrase = "";

    private string $baseDir = __DIR__ . "/ssl_key/";

    public function __construct()
    {
        $this->publicKeyPath = $this->baseDir . "public_key.pem";
        $this->privateKeyPath = $this->baseDir . "private_key.pem";
    }

    public function encrypt(string $data): bool|string
    {
        try {
            $aesKey = random_bytes(32);
            $iv = random_bytes(16);
            $encryptedData = openssl_encrypt(
                $data,
                "AES-256-CBC",
                $aesKey,
                OPENSSL_RAW_DATA,
                $iv,
            );
            if ($encryptedData === false) {
                throw new Exception("AES加密失败: " . openssl_error_string());
            }
            $publicKey = file_get_contents($this->publicKeyPath);
            $res = openssl_pkey_get_public($publicKey);
            if (!$res) {
                throw new Exception("加载公钥失败");
            }

            $encryptedAesKey = "";
            $result = openssl_public_encrypt(
                $aesKey,
                $encryptedAesKey,
                $res,
                OPENSSL_PKCS1_OAEP_PADDING,
            );
            openssl_free_key($res);

            if (!$result) {
                throw new Exception("RSA加密密钥失败: " . openssl_error_string());
            }

            $signature = "";
            $privateKey = file_get_contents($this->privateKeyPath);
            $res = openssl_pkey_get_private($privateKey, $this->passphrase);
            if (!$res) {
                throw new Exception("加载私钥失败");
            }

            openssl_sign($data, $signature, $res, OPENSSL_ALGO_SHA256);
            openssl_free_key($res);

            $package = [
                "data" => base64_encode($encryptedData),
                "key" => base64_encode($encryptedAesKey),
                "iv" => base64_encode($iv),
                "sig" => base64_encode($signature),
                "ts" => time(),
                "ver" => "1.0",
            ];
            $jsonData = json_encode($package);
            if (is_string($jsonData)) return base64_encode($jsonData);
            return "";
        }catch (Exception $exception) {
            return "";
        }
    }

    public function decrypt(string $encryptedPackage): string
    {
        try {
            $package = json_decode(base64_decode($encryptedPackage), true);
            if (!$package) {
                throw new Exception("无效的加密包");
            }

            $encryptedData = base64_decode($package["data"]);
            $encryptedAesKey = base64_decode($package["key"]);
            $iv = base64_decode($package["iv"]);
            $signature = base64_decode($package["sig"]);

            $privateKey = file_get_contents($this->privateKeyPath);
            $res = openssl_pkey_get_private($privateKey, $this->passphrase);
            if (!$res) {
                throw new Exception("加载私钥失败");
            }

            $aesKey = "";
            $result = openssl_private_decrypt(
                $encryptedAesKey,
                $aesKey,
                $res,
                OPENSSL_PKCS1_OAEP_PADDING,
            );
            openssl_free_key($res);

            if (!$result) {
                throw new Exception("RSA解密密钥失败: " . openssl_error_string());
            }

            $decryptedData = openssl_decrypt(
                $encryptedData,
                "AES-256-CBC",
                $aesKey,
                OPENSSL_RAW_DATA,
                $iv,
            );
            if ($decryptedData === false) {
                throw new Exception("AES解密失败: " . openssl_error_string());
            }

            $publicKey = file_get_contents($this->publicKeyPath);
            $res = openssl_pkey_get_public($publicKey);
            if (!$res) {
                throw new Exception("加载公钥失败");
            }

            $isValid = openssl_verify(
                $decryptedData,
                $signature,
                $res,
                OPENSSL_ALGO_SHA256,
            );
            openssl_free_key($res);

            if ($isValid !== 1) {
                throw new Exception("数字签名验证失败，数据可能被篡改！");
            }

            return $decryptedData;
        }catch (Exception $exception) {
            return  "";
        }
    }

    /**
     * @throws Exception
     */
    public function sign($data): string
    {
        $privateKey = file_get_contents($this->privateKeyPath);
        $res = openssl_pkey_get_private($privateKey, $this->passphrase);
        if (!$res) {
            throw new Exception("加载私钥失败");
        }

        $signature = "";
        openssl_sign($data, $signature, $res, OPENSSL_ALGO_SHA256);
        openssl_free_key($res);

        return base64_encode($signature);
    }

    /**
     * @throws Exception
     */
    public function verifySignature($data, $signature): bool
    {
        $signature = base64_decode($signature);
        $publicKey = file_get_contents($this->publicKeyPath);
        $res = openssl_pkey_get_public($publicKey);
        if (!$res) {
            throw new Exception("加载公钥失败");
        }

        $result = openssl_verify($data, $signature, $res, OPENSSL_ALGO_SHA256);
        openssl_free_key($res);

        return $result === 1;
    }
}
