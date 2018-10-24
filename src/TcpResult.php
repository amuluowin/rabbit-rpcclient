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
use rabbit\parser\ParserInterface;
use rabbit\pool\AbstractResult;

class TcpResult extends AbstractResult
{
    /**
     * @param mixed ...$params
     * @return mixed
     */
    public function getResult(...$params)
    {
        /**
         * @var ParserInterface $parser
         */
        $parser = ObjectFactory::get('rpc.parser');
        $result = $parser->decode($this->recv(true))['data'];

        $data = [];
        $data['result'] = $result;
        /**
         * @var TraceInterface $tracer
         * @var RequestInterface $request
         */
        $tracer = ObjectFactory::get('tracer');
        $tracer->addCollect($this->result, $data);
        $tracer->flushCollect($this->result);
        return $result;
    }

}