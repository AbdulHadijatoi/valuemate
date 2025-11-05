<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait Cacheable
{
    /**
     * Get cache key for a specific resource
     */
    protected function getCacheKey(string $type, $params = []): string
    {
        $key = $type;
        
        if (!empty($params)) {
            $paramsString = implode('_', array_filter($params));
            if ($paramsString) {
                $key .= '_' . md5($paramsString);
            }
        }
        
        return $key;
    }

    /**
     * Get cached data or store it
     */
    protected function remember(string $key, callable $callback, int $ttl = 3600)
    {
        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Clear cache by key pattern
     */
    protected function clearCache(string $pattern): void
    {
        // If using Redis, we can use pattern matching
        if (config('cache.default') === 'redis') {
            try {
                $redis = Cache::getStore()->getRedis();
                $keys = $redis->keys($pattern);
                if (!empty($keys)) {
                    $redis->del($keys);
                }
            } catch (\Exception $e) {
                // Fallback to simple forget if Redis pattern matching fails
                Cache::forget($pattern);
            }
        } else {
            // For other cache drivers, we'll need to track keys manually
            // For now, we'll clear specific known patterns
            Cache::forget($pattern);
        }
    }

    /**
     * Clear all related caches for constants
     */
    protected function clearConstantCaches(): void
    {
        Cache::forget('constant_data');
        Cache::forget('constant_data_detail');
    }

    /**
     * Clear all caches related to a specific model
     */
    protected function clearModelCaches(string $model, $id = null): void
    {
        $cacheKeys = [
            'locations',
            'property_types',
            'service_types',
            'companies',
            'banners',
            'guidelines',
            'document_requirements',
            'settings',
            'payment_methods',
            'request_types',
            'service_pricings',
            'property_service_types',
        ];

        foreach ($cacheKeys as $key) {
            // Clear all paginated data caches
            $this->clearCache($key . '_data_*');
            
            // Clear specific item cache if ID provided
            if ($id) {
                Cache::forget($key . '_' . $id);
            }
        }

        // Always clear constant caches when any model changes
        $this->clearConstantCaches();
    }

    /**
     * Clear cache for a specific resource type
     */
    protected function clearResourceCache(string $resource, $id = null): void
    {
        // Clear all data caches for this resource (works with Redis pattern matching)
        if (config('cache.default') === 'redis') {
            $this->clearCache($resource . '_data_*');
        } else {
            // For non-Redis drivers, we need to clear common cache patterns
            // This is a limitation, but we can still clear the main cache
            Cache::forget($resource . '_data');
        }
        
        // Clear specific item cache if ID provided
        if ($id) {
            Cache::forget($resource . '_' . $id);
        }

        // Clear constant caches as they depend on these resources
        $this->clearConstantCaches();
    }
}

