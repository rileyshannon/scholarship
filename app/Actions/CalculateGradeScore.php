<?php

namespace App\Actions;

class CalculateGradeScore
{
    public function handle(array $data): int
    {
        return array_sum(array_filter(
            $data,
            fn ($key) => str_ends_with($key, '_grade'),
            ARRAY_FILTER_USE_KEY
        ));
    }
}
