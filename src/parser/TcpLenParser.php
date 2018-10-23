<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/21
 * Time: 23:49
 */

namespace rabbit\rpcclient\parser;


use rabbit\core\ObjectFactory;
use rabbit\helper\JsonHelper;
use rabbit\parser\ParserInterface;

/**
 * Class TcpLenParser
 * @package rabbit\socket
 */
class TcpLenParser implements TcpParserInterface
{
    /**
     * @var int
     */
    private $headLen;

    /**
     * @var int
     */
    private $packageOffset;

    /**
     * @var string
     */
    private $packageType;

    /**
     * @var ParserInterface
     */
    private $parser;

    /**
     * TcpLenParser constructor.
     * @param ParserInterface $parser
     * @throws \Exception
     */
    public function __construct(ParserInterface $parser)
    {
        $this->headLen = ObjectFactory::get('rpc.setting')['package_body_offset'];
        $this->packageOffset = ObjectFactory::get('rpc.setting')['package_length_offset'];
        $this->packageType = ObjectFactory::get('rpc.setting')['package_length_type'];
        $this->parser = $parser;
    }

    /**
     * @param mixed $data
     * @return string
     */
    public function encode(array $data): string
    {
        $data = $this->parser->encode(JsonHelper::encode($data, JSON_UNESCAPED_UNICODE));
        $total_length = $this->headLen + strlen($data) - $this->packageOffset;
        return pack($this->packageType, $total_length) . $data;
    }

    /**
     * @param string $data
     * @return mixed
     */
    public function decode(string $data)
    {
        $data = substr($data, $this->headLen);
        $data = $this->parser->decode(JsonHelper::decode($data, true))['data'];
        return $data;
    }
}