<?php
declare(strict_types=1);

namespace Tcloud\Exam;

class Log extends Base
{
    public static function init(): self
    {
        return  (new self());
    }
}