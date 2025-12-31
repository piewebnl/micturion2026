<?php

namespace App\Helpers;

// Pagination helper class
class PaginationHelper
{
    public static function calculateLastPage(int $total, int $perPage): int
    {

        if ($perPage > 0 && $total > $perPage) {
            return intval(ceil($total / $perPage));
        }

        return 1;
    }

    public static function slicePerPage(array $array, int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;

        return array_slice($array, $offset, $perPage);
    }
}
