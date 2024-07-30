<?php
namespace App\Enums;

use Spatie\Enum\Enum;

/**
 * @method static self man()
 * @method static self woman()
 */
final class UserGender extends Enum
{
    const GENDER_MAN = 'man';
    const GENDER_WOMAN = 'woman';

    protected static function values(): array
    {
        return [
            'man' => self::GENDER_MAN,
            'woman' => self::GENDER_WOMAN,
        ];
    }

    protected static function labels(): array
    {
        return [
            self::GENDER_MAN => 'man',
            self::GENDER_WOMAN => 'woman',
        ];
    }
}
