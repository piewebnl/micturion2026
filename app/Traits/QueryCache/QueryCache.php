<?php

namespace App\Traits\QueryCache;

use App\Services\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

trait QueryCache
{
    public function getCache(string $key, array $filterValues = [])
    {
        if (config('cache.micturion_query_cache')) {
            return Cache::get($this->generateKey($key, $filterValues));
        }
    }

    public function setCache(string $key, array $filterValues = [], mixed $data = null)
    {
        if (config('cache.micturion_query_cache')) {
            return Cache::put($this->generateKey($key, $filterValues), $data);
        }
    }

    public function clearCache(string $key, string $channel, ?Command $command = null)
    {
        Logger::log(
            'notice',
            $this->channel,
            'Cache cleared : ' . $this->channel,
            [],
            $command
        );
        Cache::forget($key);
    }

    private function generateKey(string $key, array $filterValues): string
    {
        unset($filterValues['view']);
        ksort($filterValues);

        return $key . '_' . crc32(http_build_query($filterValues)); // use crc32 for speed
    }
}
