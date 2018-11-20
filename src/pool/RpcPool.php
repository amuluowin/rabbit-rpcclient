<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/21
 * Time: 0:23
 */

namespace rabbit\rpcclient\pool;


use rabbit\governance\balancer\BalancerInterface;
use rabbit\governance\provider\ProviderInterface;
use rabbit\rpcclient\Tcp;
use rabbit\socket\pool\TcpPool;

/**
 * Class RpcPool
 * @package rabbit\rpcclient\pool
 */
class RpcPool extends TcpPool
{
    /**
     * @return string
     */
    public function getConnectionAddress(): string
    {
        $serviceList = $this->getServiceList();
        /** @var BalancerInterface $balancer */
        if (($balancer = $this->poolConfig->getBalancer()) !== null) {
            return $balancer->getCurrentService($serviceList);
        }
        return current($serviceList);
    }

    /**
     * @return array
     */
    protected function getServiceList()
    {
        if (($provider = $this->poolConfig->getProvider()) === null) {
            throw new \InvalidArgumentException('please set service provider!');
        }
        /** @var ProviderInterface $provider */
        return $provider->getServices($this->poolConfig->getName());
    }
}