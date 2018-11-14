<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/21
 * Time: 22:33
 */

namespace rabbit\rpcclient;


use Psr\Http\Message\RequestInterface;
use rabbit\core\ObjectFactory;
use rabbit\governance\trace\TraceInterface;
use rabbit\pool\AbstractResult;

class TcpResult extends AbstractResult
{
    /**
     * @param mixed ...$params
     * @return mixed
     */
    public function getResult(...$params)
    {
        $timeout = array_shift($params);

        $result = $this->parser->decode($this->recv(true, $timeout))['data'];

        $data = [];
        $data['recvTime'] = floor(microtime(true) * 1000);
        $data['result'] = $result;
        /**
         * @var TraceInterface $tracer
         * @var RequestInterface $request
         */
        $tracer = ObjectFactory::get('tracer');
        $tracer->addCollect($data);
        $tracer->flushCollect();
        return $result;
    }

}