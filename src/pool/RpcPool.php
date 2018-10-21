<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/21
 * Time: 0:23
 */

namespace rabbit\rpcclient\pool;


use rabbit\pool\ConnectionInterface;
use rabbit\pool\ConnectionPool;
use rabbit\rpcclient\Connection;
use rabbit\rpcclient\RpcClient;

/**
 * Class RpcPool
 * @package rabbit\rpcclient\pool
 */
class RpcPool extends ConnectionPool
{
    /**
     * @return ConnectionInterface
     */
    public function createConnection(): ConnectionInterface
    {
        return new Connection($this);
    }

    /**
     * @return string
     */
    public function getConnectionAddress(): string
    {
        $serviceList = $this->getServiceList();
        return current($serviceList);
    }

    protected function getServiceList()
    {
        $name = $this->poolConfig->getName();
        $uri = $this->poolConfig->getUri();
        if (empty($uri)) {
            $error = sprintf('Service does not configure uri name=%s', $name);
            throw new \InvalidArgumentException($error);
        }

        return $uri;
    }
}