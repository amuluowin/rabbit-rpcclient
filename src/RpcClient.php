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
use rabbit\governance\trace\TraceInterface;
use rabbit\pool\PoolInterface;
use rabbit\rpcclient\pool\RpcPool;
use rabbit\rpcserver\RpcParser;
use rabbit\socket\tcp\TcpParserInterface;

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
     * @var PoolInterface[]
     */
    private $pools = [];

    /**
     * @var TraceInterface
     */
    private $tracer;

    /**
     * RpcClient constructor.
     * @param RpcPool $pool
     */
    public function __construct(RpcPool $pool)
    {
        $this->pool = $pool;
        $this->tracer = ObjectFactory::get('tracer');
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
//        $serviceList = ObjectFactory::get('rpc.services');
//        if (isset($serviceList[$service])) {
//            return new NavResult(ObjectFactory::get($serviceList[$service])->$name(...$arguments));
//        }
        /**
         * @var Connection $client
         * @var TcpParserInterface $parser
         */
        if (!isset($this->pools[$service])) {
            $this->pools[$service] = clone $this->pool;
            $this->pools[$service]->getPoolConfig()->setName($service);
        }
        $client = $this->pools[$service]->getConnection();
        $parser = ObjectFactory::get('rpc.parser');
        $data = [
            'service' => $service,
            'method' => $name,
            'params' => $arguments
        ];
        $data = $this->tracer->getCollect($data);
        $data = $parser->encode($data);
        $result = $client->send($data);

        return new TcpResult($client, $result, $parser);

    }
}