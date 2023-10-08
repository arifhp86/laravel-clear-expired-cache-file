<?php

namespace Arifhp86\ClearExpiredCacheFile\Support;

class Formatter
{
    public static function formatSpace($space): string
    {
        $sz = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $factor = floor((strlen($space) - 1) / 3);

        return sprintf('%.2f', $space / pow(1024, $factor)) . $sz[$factor];
    }

    public static function formatDuration(float $seconds): string
    {
        if ($seconds < 1) {
            return sprintf('%.0f', $seconds * 1000) . 'ms';
        }

        if ($seconds < 60) {
            return sprintf('%.2f', $seconds) . 's';
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = fmod($seconds, 60);

        return sprintf('%02d', $hours) . ':' . sprintf('%02d', $minutes) . ':' . sprintf('%02d', $seconds);
    }
}
