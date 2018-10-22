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
use rabbit\helper\ArrayHelper;
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
     * @var array
     */
    private $services = [];

    /**
     * RpcClient constructor.
     * @param RpcPool $pool
     */
    public function __construct(RpcPool $pool)
    {
        $this->pool = $pool;
//        $this->services = ArrayHelper::merge(getServices(), getApis());
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
        $service = isset($this->services[$service]) ? $this->services[$service] : $service;
        if (($ser = ObjectFactory::get($service, null, false)) !== null) {
            return new NavResult($ser->$name(...$arguments));
        }
        /**
         * @var Connection $client
         * @var ParserInterface $parser
         */
        $client = $this->pool->getConnection();
        $parser = ObjectFactory::get('rpc.parser');
        $data = [
            'service' => $service,
            'method' => $name,
            'params' => $arguments,
            'traceId' => 0,
            'spanId' => 0,
            'host' => current(swoole_get_local_ip()),
            'port' => 80,
            'time' => time()
        ];
        $data = $parser->encode($data);
        $result = $client->send($data);

        return new TcpResult($client, $result);

    }
}