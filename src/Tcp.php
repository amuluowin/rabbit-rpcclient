<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/21
 * Time: 0:34
 */

namespace rabbit\rpcclient;

use rabbit\core\ObjectFactory;
use rabbit\socket\TcpClient;
use Swoole\Coroutine\Client;

/**
 * Class Connection
 * @package rabbit\rpcclient
 */
class Tcp extends TcpClient
{
    /**
     * @return array
     * @throws \Exception
     */
    protected function getTcpClientSetting(): array
    {
        return ObjectFactory::get('rpcclient.setting', []);
    }
}