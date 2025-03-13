<?php

namespace Src\Infrastructure\Persistence\Mysql;

use App\Models\Otp as OtpModel;
use Carbon\Carbon;
use Src\Domain\Auth\Entity\Otp;
use Src\Domain\Auth\Enum\OtpPurpose;
use Src\Domain\Auth\Enum\OtpSendType;
use Src\Domain\Auth\Enum\OtpStatus;
use Src\Domain\Auth\Exception\OtpNotFoundException;
use Src\Domain\Auth\Repository\OtpRepository;

class MysqlOtpRepository implements OtpRepository
{
    /**
     * @throws OtpNotFoundException
     */
    public function getLatestOtp(int $userId, int $deviceId, OtpPurpose $purpose): Otp
    {
        $model = OtpModel::query()
            ->where('user_id', $userId)
            ->where('device_id', $deviceId)
            ->where('purpose', $purpose->value)
//            ->where('status', OtpStatus::SENT->value)
            ->latest()
            ->first();
        if (!$model) {
            throw new OtpNotFoundException();
        }
        $entity = new Otp(
            userId: $userId,
            deviceId: $deviceId,
            otp: $model->otp,
            sentType: OtpSendType::from($model->sent_type),
            status: OtpStatus::from($model->status),
            purpose: $purpose,
            expiresAt: Carbon::parse($model->expires_at),
        );
        $entity->setId($model->id);

        return $entity;
    }

    public function create(Otp $otp): Otp
    {
        $model = OtpModel::query()
            ->create([
                'user_id' => $otp->getUserId(),
                'device_id' => $otp->getDeviceId(),
                'sent_type' => $otp->getSentType()->value,
                'otp' => $otp->getOtp(),
                'status' => $otp->getStatus()->value,
                'purpose' => $otp->getPurpose()->value,
                'expires_at' => $otp->getExpiresAt()
            ]);

        $otp->setId($model->id);

        return $otp;
    }

    public function update(Otp $otp): void
    {
        OtpModel::query()
            ->where('id', $otp->getId())
            ->update([
                'status' => $otp->getStatus()->value
            ]);
    }

}
