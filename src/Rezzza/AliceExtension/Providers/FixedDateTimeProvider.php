<?php

namespace Rezzza\AliceExtension\Providers;

class FixedDateTimeProvider
{
    public static function fixedDateTime($time = "now", $timezone = null)
    {
        if ($timezone) {
            $timezone = new \DateTimeZone($timezone);
        }

        return new \DateTime($time, $timezone);
    }

}
