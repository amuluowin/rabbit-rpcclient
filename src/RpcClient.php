<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/21
 * Time: 22:27
 */

namespace rabbit\rpcclient;


use rabbit\contract\ResultInterface;
use rabbit\core\Context;
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
     * @param string $service
     * @return RpcClient
     */
    public function create(string $service): RpcClient
    {
        Context::set('rpc.service', $service);
        return $this;
    }

    /**
     * @param $name
     * @param $arguments
     * @return ResultInterface
     * @throws \Exception
     */
    public function __call($name, $arguments): ResultInterface
    {
        $service = Context::get('rpc.service');
        if (($ser = ObjectFactory::get($service)) !== null) {
            return new NavResult($ser->$name(...$arguments));
        }
        /**
         * @var Connection $client
         * @var ParserInterface $parser
         */
        $client = $this->pool->getConnection();
        $parser = ObjectFactory::get('rpc.parser');
        $arguments = $parser->encode(array_shift($arguments));
        $result = $client->send($arguments);

        return new TcpResult($connection, $result);

    }
}