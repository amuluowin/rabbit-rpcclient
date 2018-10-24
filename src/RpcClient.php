<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/21
 * Time: 22:27
 */

namespace rabbit\rpcclient;


use Psr\Http\Message\ServerRequestInterface;
use rabbit\contract\ResultInterface;
use rabbit\core\Context;
use rabbit\core\ObjectFactory;
use rabbit\governance\trace\TraceInterface;
use rabbit\pool\PoolInterface;
use rabbit\rpcclient\parser\TcpParserInterface;
use rabbit\rpcclient\pool\RpcPool;
use rabbit\rpcserver\Request;
use rabbit\rpcserver\RpcParser;
use rabbit\server\AttributeEnum;

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
        $serviceList = ObjectFactory::get('rpc.services');
        if (isset($serviceList[$service])) {
            return new NavResult(ObjectFactory::get($serviceList[$service])->$name(...$arguments));
        }
        /**
         * @var Connection $client
         * @var TcpParserInterface $parser
         */
        $client = $this->pool->getConnection();
        $parser = ObjectFactory::get('rpc.parser');
        $data = [
            'service' => $service,
            'method' => $name,
            'params' => $arguments
        ];

        /**
         * @var ServerRequestInterface $request
         */
        $request = Context::get('request');
        $traceData = $request->getAttribute(AttributeEnum::TRACE_ATTRIBUTE);
        $traceData = $this->tracer->getCollect($data, $traceData ? $traceData['traceId'] : null);
        $data = $parser->encode($traceData);
        $result = $client->send($data);

        return new TcpResult($client, $traceData['traceId']);

    }
}