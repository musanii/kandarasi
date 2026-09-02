<?php

namespace App\Support;

/**
 * A working set of ISO 4217 currency codes covering major global and
 * regional currencies -- not exhaustive (180+ exist), but enough for a
 * pan-African / internationally trading org. Extend as needed.
 */
class Currencies
{
    public static function options(): array
    {
        return [
            'KES' => 'KES — Kenyan Shilling',
            'UGX' => 'UGX — Ugandan Shilling',
            'TZS' => 'TZS — Tanzanian Shilling',
            'RWF' => 'RWF — Rwandan Franc',
            'ETB' => 'ETB — Ethiopian Birr',
            'NGN' => 'NGN — Nigerian Naira',
            'GHS' => 'GHS — Ghanaian Cedi',
            'ZAR' => 'ZAR — South African Rand',
            'EGP' => 'EGP — Egyptian Pound',
            'XOF' => 'XOF — West African CFA Franc',
            'XAF' => 'XAF — Central African CFA Franc',
            'USD' => 'USD — US Dollar',
            'EUR' => 'EUR — Euro',
            'GBP' => 'GBP — British Pound',
            'CHF' => 'CHF — Swiss Franc',
            'CAD' => 'CAD — Canadian Dollar',
            'AUD' => 'AUD — Australian Dollar',
            'AED' => 'AED — UAE Dirham',
            'SAR' => 'SAR — Saudi Riyal',
            'CNY' => 'CNY — Chinese Yuan',
            'INR' => 'INR — Indian Rupee',
            'JPY' => 'JPY — Japanese Yen',
        ];
    }

    public static function codes(): array
    {
        return array_keys(self::options());
    }
}
