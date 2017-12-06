<?php

namespace PayU\PaymentGateway\Api;

/**
 * Interface PayUProxyConfigInterface
 * @package PayU\PaymentGateway\Api
 */
interface PayUProxyConfigInterface
{
    /**
     * @param string $proxyHost
     *
     * @return $this
     */
    public function setProxyHost($proxyHost);

    /**
     * @param int $proxyPort
     *
     * @return $this
     */
    public function setProxyPort($proxyPort);

    /**
     * @param string $proxyUser
     *
     * @return $this
     */
    public function setProxyUser($proxyUser);

    /**
     * @param string $proxyPassword
     *
     * @return $this
     */
    public function setProxyPassword($proxyPassword);
}
