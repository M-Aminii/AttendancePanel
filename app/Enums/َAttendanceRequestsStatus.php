<?php
namespace App\Enums;

use Spatie\Enum\Enum;

/**
 * @method static self pending()
 * @method static self approved()
 * @method static self rejected()
 */
final class َAttendanceRequestsStatus extends Enum
{
    const PENDING = 'pending';
    const APPROVED = 'approved';
    const REJECTED = 'rejected';

    protected static function values(): array
    {
        return [
            'pending' => self::PENDING,
            'approved' => self::APPROVED,
            'rejected' => self::REJECTED,
        ];
    }

    protected static function labels(): array
    {
        return [
            self::PENDING => 'pending',
            self::APPROVED => 'approved',
            self::REJECTED => 'rejected',
        ];
    }
}
