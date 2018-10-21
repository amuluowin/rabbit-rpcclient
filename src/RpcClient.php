<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/21
 * Time: 22:27
 */

namespace rabbit\rpcclient;


use rabbit\contract\ResultInterface;
use rabbit\pool\ConnectionInterface;
use rabbit\pool\PoolInterface;
use rabbit\rpcclient\pool\RpcPool;
use rabbit\rpcserver\RpcParser;

/**
 * Class RpcClient
 * @package rabbit\rpcclient
 */
class RpcClient
{
    /**
     * @var PoolInterface
     */
    private $pool;

    /**
     * @var RpcParser
     */
    private $parser;

    /**
     * RpcClient constructor.
     * @param RpcPool $pool
     */
    public function __construct(RpcPool $pool, RpcParser $parser)
    {
        $this->pool = $pool;
        $this->parser = $parser;
    }

    /**
     * @param $name
     * @param $arguments
     * @return ResultInterface
     */
    public function __call($name, $arguments): ResultInterface
    {
        /**
         * @var Connection $client
         */
        $client = $this->pool->getConnection();
        $arguments = $this->parser->encode(array_shift($arguments));
        $result = $client->send($arguments);

        return $this->getResult($client, $result);
    }

    /**
     * @param ConnectionInterface $connection
     * @param $result
     * @return ResultInterface
     */
    private function getResult(ConnectionInterface $connection, $result): ResultInterface
    {
        return new RpcResult($connection, $result);
    }
}