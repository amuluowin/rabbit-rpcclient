<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/22
 * Time: 18:28
 */

namespace rabbit\rpcclient;


use rabbit\contract\ResultInterface;

/**
 * Class NavResult
 * @package rabbit\rpcclient
 */
class NavResult implements ResultInterface
{

    /**
     * @var mixed
     */
    private $result;

    /**
     * NavResult constructor.
     * @param $result
     */
    public function __construct($result)
    {
        $this->result = $result;
    }

    /**
     * @param mixed ...$params
     * @return mixed
     */
    public function getResult(...$params)
    {
        return $this->result;
    }

}