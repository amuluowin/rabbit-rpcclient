<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/21
 * Time: 22:33
 */

namespace rabbit\rpcclient;


use rabbit\pool\AbstractResult;

class RpcResult extends AbstractResult
{
    /**
     * @param mixed ...$params
     * @return mixed
     */
    public function getResult(...$params)
    {
        return $this->recv(true);
    }

}