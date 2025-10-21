<?php
declare(strict_types=1);

namespace Qlcloud\Cloud;

class Config
{
    public array $config = [];

    public function __construct()
    {
        $this->config = $this->getConfig();
    }

    private function getConfig(): array
    {
        return [
            "current_domain" => $this->getCurrentRequestDomain(),
            "license" => $this->getLicense(),
        ];
    }

    private function getCurrentRequestDomain(): string
    {
        $currentHost = $_SERVER["HTTP_HOST"] ?? "";
        if (empty($currentHost)) {
            $currentHost = $_SERVER["SERVER_NAME"] ?? "";
        }
        $host = explode(":", $currentHost)[0];
        $host = strtolower($host);
        $doubleSuffixes = [
            "com.cn",
            "net.cn",
            "org.cn",
            "gov.cn",
            "edu.cn",
            "co.uk",
            "com.au",
            "com.br",
            "com.mx",
            "com.ar",
            "github.io",
            "gitlab.io",
            "vercel.app",
            "netlify.app",
        ];
        $parts = explode(".", $host);
        $count = count($parts);
        if ($count < 2) {
            return $host;
        }
        $lastTwo = $parts[$count - 2] . "." . $parts[$count - 1];
        $lastThree = $count >= 3 ? $parts[$count - 3] . "." . $lastTwo : null;
        if (in_array($lastThree, $doubleSuffixes)) {
            return $parts[$count - 3] .
                "." .
                $parts[$count - 2] .
                "." .
                $parts[$count - 1];
        } elseif (in_array($lastTwo, $doubleSuffixes)) {
            return $parts[$count - 2] . "." . $parts[$count - 1];
        } else {
            return $parts[$count - 2] . "." . $parts[$count - 1];
        }
    }

    private function getLicense(): string
    {
        return "eyJkYXRhIjoiVnJxZUd5NEhmUkRGQk43RzQ5R214QT09Iiwia2V5IjoiV0RYZzF0NTFoeVRiSXRyOWpDakZMZjVSUlBHdmJpTjcrU213NjJlc0tGRkdkN09zdElqbU1ubGxBQkViWW1leVVTaXZRakgxa0lDNjYwSDQwWUhaWnhZNTZcL1wvRUxSNmhYaWxNalhTY2xIM0E0K1J1dzlnMWNuSTJ0NnhEcjVLbmk1SWNKYzRVTDZiT3J4dDhDb3Q4aHlzb2k3cmJWNWlpbkJkNXRSdGRZZWU3SkZ2VWpuczF1enFyelVxcE1YcVQ2ZFlmeTZcL0xWN29NTlI2dDB4VnBsQlVjN2VmSWJ1ZXF3aTc0WURSXC9aczdGY0xnOTZQUzNETGFRYlFZaHp0MXpQQTZGYkdEUHNVWGpyV0N4V0N6MHFOb2puSnlOY2dTVEJcL1FpU05RMzRxbTJGY1k2Z1puU1FEelk2SzZ4b0x3a25jQ2tIeE43dWVKSUhhXC91RmVkNkFnPT0iLCJpdiI6Inl1SU8yZld3TzA4TFB5SDJENFhBN2c9PSIsInNpZyI6IlJNandIS3l3THZzd1wvbjI0dzJkQ1d3eVg0WUtodldFbXdjUENKbU84SURaZEVVZm5mcEw5YlByS2hQZVRRcVRcL1h6blNBWHB3XC9CNUZseWg3Z2tSTUIzQldmRHdKbzFJU1QyV1dUZzJVVVFkUExNUk9oZDVHN2dzblZSdGkyUzkwUWFhdjFMZDhVVWtzQnF6bGQ5ejRidTZJOEtxQkRYSnlGTnNNWGtkbGxhZkF2a2ZwQ0NjSmw3RndBSDk3NXFIOEdBc2VqYnU4U2tkNkkwODRYUnlicFJocnN0blJpUnVCUjBaV1Q2c1BjRkh0MkFaXC9BK0ZxazU0bUFjNDFcL3dEZUlnZjk2Z1wvXC9rVGlqOUJ4eldrUGJEN3dDUEZ5WG1ZTENNV0NGeXN4M29BQWE1bGtXMnFMc1d6cWlIaDhyYkNJNytPMVhJQWtFREdrMVdjRyt1RlFSa1E9PSIsInRzIjoxNzYwNjM5MTIyLCJ2ZXIiOiIxLjAifQ==";
    }
}
