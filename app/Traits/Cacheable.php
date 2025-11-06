<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait Cacheable
{
    /**
     * Get cached data or store it
     */
    protected function remember(string $key, callable $callback, int $ttl = 3600)
    {
        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Clear a single cache key
     */
    protected function clearCache(string $key): void
    {
        Cache::forget($key);
    }

    /**
     * Clear all related caches for constants
     */
    protected function clearConstantCaches(): void
    {
        Cache::forget('constant_data');
        Cache::forget('constant_data_detail');
    }
}

