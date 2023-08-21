<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FileSizeExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('format_size', [$this, 'formatSize']),
        ];
    }

    public function formatSize(int $size, int $precision = 2): string
    {
        $units = ['o', 'Ko', 'Mo', 'Go', 'To'];

        $pow = floor(($size ? log($size) : 0) / log(1000));
        $pow = min($pow, count($units) - 1);

        $formattedSize = $size / pow(1000, $pow);

        return number_format($formattedSize, $precision) . ' ' . $units[$pow];
    }
}
