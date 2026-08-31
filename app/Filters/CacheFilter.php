<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class CacheFilter implements FilterInterface
{
    protected $cacheEnabled;
    protected static $cacheKey = null; // To share cacheKey between before and after

    public function __construct()
    {
        // Read the caching configuration from .env
        $this->cacheEnabled = env('app.cacheEnabled', false);
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        if (!$this->cacheEnabled || $request->getMethod() !== 'GET') {
            // Skip caching if disabled or not a GET request
            return;
        }

        // Generate a cache key considering URL, query parameters, and headers
        self::$cacheKey = $this->generateCacheKey($request);

        // Check if a cached response exists
        $cachedResponse = cache(self::$cacheKey);

        if ($cachedResponse) {
            // Serve the cached response and exit
            echo $cachedResponse;
            exit;
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        if (!$this->cacheEnabled || $request->getMethod() !== 'GET') {
            // Skip caching if disabled or not a GET request
            return;
        }

        // Ensure we have a valid cache key
        if (self::$cacheKey === null) {
            return;
        }

        // Read the cache TTL from .env
        $cacheTTL = getenv('app.cacheTTL') ?? 3600; // Default to 1 hour if not set

        // Parse the TTL argument
        // $cacheTTL = $this->parseTTL($arguments) ?? 3600; // Default to 1 hour if not set

        $cacheSignature = '<!-- Cached on ' . date('Y-m-d H:i:s') . ' -->';

        // Cache the response body
        cache()->save(self::$cacheKey, $response->getBody() . $cacheSignature, $cacheTTL);
    }

    private function generateCacheKey(RequestInterface $request): string
    {
        // Include URI, query parameters, and Accept header to generate a unique key
        $uri = $request->getUri()->getPath();
        $query = $request->getGet(); // Include all query parameters
        $acceptHeader = $request->getHeaderLine('Accept'); // Content negotiation

        // Combine URI, query, and headers into a unique hash
        return md5($uri . json_encode($query) . $acceptHeader);
    }

    private function parseTTL($arguments): ?int
    {
        if (is_array($arguments) && count($arguments) > 0) {
            foreach ($arguments as $arg) {
                if (strpos($arg, 'ttl=') === 0) {
                    return (int) substr($arg, 4); // Extract the TTL value
                }
            }
        }
        return null;
    }
}
