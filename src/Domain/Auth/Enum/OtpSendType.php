<?php

namespace Src\Domain\Auth\Enum;

enum OtpSendType: int
{
    case EMAIL = 1;
    case SMS = 2;
}
