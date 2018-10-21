<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/21
 * Time: 0:34
 */

namespace rabbit\rpcclient;

use rabbit\core\Exception;
use rabbit\core\ObjectFactory;
use rabbit\socket\AbstracetSocketConnection;
use Swoole\Coroutine\Client;

/**
 * Class Connection
 * @package rabbit\rpcclient
 */
class Connection extends AbstracetSocketConnection
{
    /**
     * @var \Swoole\Coroutine\Client
     */
    private $connection;

    /**
     * @throws Exception
     */
    public function createConnection(): void
    {
        $client = new Client(SWOOLE_SOCK_TCP | SWOOLE_KEEP);

        $address = $this->pool->getConnectionAddress();
        $timeout = $this->pool->getTimeout();
        $setting = $this->getTcpClientSetting();
        $setting && $client->set($setting);

        list($host, $port) = explode(':', $address);
        if (!$client->connect($host, $port, $timeout)) {
            $error = sprintf('Service connect fail errorCode=%s host=%s port=%s', $client->errCode, $host, $port);
            throw new Exception($error);
        }
        $this->connection = $client;
    }

    /**
     * @return AbstractConnection
     * @throws Exception
     */
    public function reconnect(): AbstractConnection
    {
        $this->createConnection();
        return $this;
    }

    /**
     * @return bool
     */
    public function check(): bool
    {
        return $this->connection->connected;
    }

    /**
     * @return mixed|string
     * @throws Exception
     */
    public function receive()
    {
        $result = $this->recv();
        $this->recv = true;
        return $result;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getTcpClientSetting(): array
    {
        return ObjectFactory::get('rpc.client', []);
    }

    /**
     * @param string $data
     * @return bool
     */
    public function send(string $data): bool
    {
        $result = $this->connection->send($data);
        $this->recv = false;
        return $result;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function recv(): string
    {
        $data = $this->connection->recv();
        if (empty($data)) {
            throw new Exception('ServiceConnection::recv error, errno=' . socket_strerror($this->connection->errCode));
        }
        return $data;
    }

    /**
     * @return bool
     */
    public function close(): bool
    {
        return $this->connection->close();
    }
}