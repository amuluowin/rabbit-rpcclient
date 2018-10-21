<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/21
 * Time: 22:27
 */

namespace rabbit\rpcclient;


use rabbit\contract\ResultInterface;
use rabbit\core\ObjectFactory;
use rabbit\parser\ParserInterface;
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
     * RpcClient constructor.
     * @param RpcPool $pool
     */
    public function __construct(RpcPool $pool)
    {
        $this->pool = $pool;
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
         * @var ParserInterface $parser
         */
        $client = $this->pool->getConnection();
        $parser = ObjectFactory::get('rpc.parser');
        $arguments = $parser->encode(array_shift($arguments));
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