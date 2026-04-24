<?php

declare(strict_types=1);

namespace InsiderLatam\Newsletter\Domain\Support;

final class Acf
{
    public static function getField(string $fieldName, $postId = null, $default = null)
    {
        if (! function_exists('get_field')) {
            return $default;
        }

        $value = get_field($fieldName, $postId);

        return $value !== null ? $value : $default;
    }
}
