<?php
namespace App\Enums;

use Spatie\Enum\Enum;

/**
 * @method static self active()
 * @method static self inactive()
 */
final class UserStatus extends Enum
{
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';

    protected static function values(): array
    {
        return [
            'active' => self::ACTIVE,
            'inactive' => self::INACTIVE,
        ];
    }

    protected static function labels(): array
    {
        return [
            self::ACTIVE => 'active',
            self::INACTIVE => 'inactive',
        ];
    }
}
