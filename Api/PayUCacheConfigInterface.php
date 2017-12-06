<?php

namespace PayU\PaymentGateway\Api;

/**
 * Interface PayUCacheConfigInterface
 * @package PayU\PaymentGateway\Api
 */
interface PayUCacheConfigInterface
{
    const TYPE_MEMCACHE = 'memcache';
    const TYPE_FILE = 'file';

    /**
     * Set cache type memcache|file
     *
     * @param string $type
     *
     * @return $this
     */
    public function setType($type);

    /**
     * Set cache directory
     *
     * @param string $directory
     *
     * @return $this
     */
    public function setDirectory($directory);

    /**
     * Set cache host for memcache type
     *
     * @param string $host
     *
     * @return $this
     */
    public function setMemCacheHost($host);

    /**
     * Set cache port for memcache type
     *
     * @param string $port
     *
     * @return $this
     */
    public function setMemCachePort($port);

    /**
     * set cache weight for memcache type
     *
     * @param int $weight
     *
     * @return $this
     */
    public function setMemCacheWeight($weight);
}
