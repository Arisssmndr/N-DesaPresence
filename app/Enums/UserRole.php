<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case KEPALA_DESA = 'kepala_desa';
    case PERANGKAT = 'perangkat';
    case AUDITOR = 'auditor';
    case STAF = 'staf';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator Sistem',
            self::KEPALA_DESA => 'Kepala Desa',
            self::PERANGKAT => 'Perangkat Desa',
            self::AUDITOR => 'Auditor / Inspektorat',
            self::STAF => 'Staf Desa',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function administrativeRoles(): array
    {
        return [
            self::ADMIN->value,
            self::KEPALA_DESA->value,
        ];
    }
}
