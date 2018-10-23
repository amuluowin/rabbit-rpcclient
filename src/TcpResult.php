<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/21
 * Time: 22:33
 */

namespace rabbit\rpcclient;


use rabbit\core\ObjectFactory;
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
        return $parser->decode($this->recv(true))['data'];
    }

}