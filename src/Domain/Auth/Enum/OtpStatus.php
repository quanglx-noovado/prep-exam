<?php

namespace Src\Domain\Auth\Enum;

enum OtpStatus: int
{
    case PENDING = 1;
    case SENT = 2;
    case CONFIRMED = 3;
    case FAILED = 4;
}
