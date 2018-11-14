<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/21
 * Time: 22:15
 */

namespace rabbit\rpcclient\pool;


use rabbit\governance\balancer\BalancerInterface;
use rabbit\governance\provider\ProviderInterface;
use rabbit\socket\pool\SocketConfig;

/**
 * Class RpcPoolConfig
 * @package rabbit\rpcclient\pool
 */
class RpcPoolConfig extends SocketConfig
{
    /** @var BalancerInterface */
    private $balancer;

    /** @var ProviderInterface */
    private $provider;
    /**
     * @var bool
     */
    private $isUseProvider = true;

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return BalancerInterface
     */
    public function getBalancer(): BalancerInterface
    {
        return $this->balancer;
    }

    /**
     * @return ProviderInterface
     */
    public function getProvider(): ProviderInterface
    {
        return $this->provider;
    }
}