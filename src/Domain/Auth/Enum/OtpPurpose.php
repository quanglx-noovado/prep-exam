<?php

namespace Src\Domain\Auth\Enum;

enum OtpPurpose: int
{
    case NEW_DEVICE = 1;
    case REMOVE_DEVICE = 2;
}
