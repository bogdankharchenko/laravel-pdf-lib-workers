<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Generator;

use RuntimeException;

class Names
{
    /**
     * "bottom-left" → "BottomLeft", "BASIC WL" → "BasicWl", "fillAndOutline" → "FillAndOutline", "4A0" → "4A0".
     */
    public static function pascal(string $value): string
    {
        $parts = preg_split('/[^A-Za-z0-9]+/', $value, flags: PREG_SPLIT_NO_EMPTY) ?: [];
        $name = '';

        foreach ($parts as $part) {
            // All-caps words read better as words ("BASIC" → "Basic"). Codes with digits ("4A0", "3B", "SRA4")
            // and mixed case ("UseNone") are kept.
            $part = preg_match('/^[A-Z]{2,}$/', $part) ? ucfirst(strtolower($part)) : ucfirst($part);
            // Keep digit runs apart: "RC4-40" → "RC4_40", not "RC440".
            $name .= ($name !== '' && ctype_digit($name[-1]) && ctype_digit($part[0]) ? '_' : '').$part;
        }

        return $name;
    }

    /**
     * An enum case name for a value. Names starting with a digit get a leading underscore ("4A0" → "_4A0").
     */
    public static function enumCase(string $value): string
    {
        $name = self::pascal($value);

        if ($name === '') {
            throw new RuntimeException("Cannot name an enum case for value \"{$value}\"");
        }

        return ctype_digit($name[0]) ? '_'.$name : $name;
    }

    /**
     * Enum case names for values; fails if two values would collide.
     *
     * @param  list<string>  $values
     * @return array<string, string> value => case name
     */
    public static function enumCases(array $values, string $enum): array
    {
        $cases = [];

        foreach ($values as $value) {
            $case = self::enumCase($value);

            if (in_array($case, $cases, true)) {
                throw new RuntimeException("Enum {$enum}: values collide as case {$case}");
            }

            $cases[$value] = $case;
        }

        return $cases;
    }
}
