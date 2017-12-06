<?php

namespace PayU\PaymentGateway\Model;

use PayU\PaymentGateway\Api\PayUCacheConfigInterface;
use Magento\Framework\Filesystem;

/**
 * Class CacheConfig
 * @package PayU\PaymentGateway\Model
 */
class CacheConfig implements PayUCacheConfigInterface
{
    /**
     * @var \OpenPayU_Configuration
     */
    private $openPayUConfig;

    /**
     * @var string
     */
    private $cacheDirectory;

    /**
     * @var string
     */
    private $memCacheHost;

    /**
     * @var string
     */
    private $memCachePort;

    /**
     * @var int
     */
    private $memCacheWeight;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * CacheConfig constructor.
     *
     * @param \OpenPayU_Configuration $openPayUConfig
     * @param Filesystem $fileSystem
     */
    public function __construct(\OpenPayU_Configuration $openPayUConfig, Filesystem $fileSystem)
    {
        $this->openPayUConfig = $openPayUConfig;
        $this->fileSystem = $fileSystem;
        $this->setDefaultConfig();
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $config = $this->openPayUConfig;
        if ($type === PayUCacheConfigInterface::TYPE_FILE) {
            $config::setOauthTokenCache(new \OauthCacheFile($this->cacheDirectory));
        }
        if ($type === PayUCacheConfigInterface::TYPE_MEMCACHE) {
            $config::setOauthTokenCache(
                new \OauthCacheMemcached($this->memCacheHost, $this->memCachePort, $this->memCacheWeight)
            );
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setDirectory($directory)
    {
        $this->cacheDirectory = $directory;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setMemCacheHost($host)
    {
        $this->memCacheHost = $host;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setMemCachePort($port)
    {
        $this->memCachePort = $port;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setMemCacheWeight($weight)
    {
        $this->memCacheWeight = $weight;

        return $this;
    }

    /**
     * Set Default Config
     *
     * @return void
     */
    private function setDefaultConfig()
    {
        $cacheDir = $this->fileSystem->getDirectoryRead('cache')->getAbsolutePath();
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir);
        }
        $this->setDirectory($cacheDir);
        $this->setType(PayUCacheConfigInterface::TYPE_FILE);
    }

}
