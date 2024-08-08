<?php
namespace App\Enums;

use Spatie\Enum\Enum;

/**
 * @method static self active()
 * @method static self inactive()
 * @method static self complete()
 * @method static self suspend()
 */
final class LocationStatus extends Enum
{
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';
    const COMPLETE ='complete';
    const SUSPEND ='suspend';

    protected static function values(): array
    {
        return [
            'active' => self::ACTIVE,
            'inactive' => self::INACTIVE,
            'complete'=> self::COMPLETE,
            'suspend'=> self::SUSPEND
        ];
    }

    protected static function labels(): array
    {
        return [
            self::ACTIVE => 'active',
            self::INACTIVE => 'inactive',
            self::COMPLETE => 'complete',
            self::SUSPEND => 'suspend'
        ];
    }
}
