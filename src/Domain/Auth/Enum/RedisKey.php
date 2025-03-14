<?php

namespace Src\Domain\Auth\Enum;

enum RedisKey: string
{
    case SENT_COUNT = 'sent_count_user_';
    case LAST_SENT = 'last_sent_user_';
    case FAILED_COUNT = 'failed_count_user_';
    case LAST_FAILED = 'last_failed_user_';

}
