<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/21
 * Time: 23:50
 */

namespace rabbit\rpcclient\parser;


use rabbit\core\ObjectFactory;
use rabbit\helper\JsonHelper;
use rabbit\parser\ParserInterface;
use rabbit\socket\tcp\TcpParserInterface;

/**
 * Class TcpEofParser
 * @package rabbit\socket
 */
class TcpEofParser implements TcpParserInterface
{
    /**
     * @var string
     */
    private $eofCheck;

    /**
     * @var ParserInterface
     */
    private $parser;

    /**
     * TcpEofParser constructor.
     * @param ParserInterface $parser
     */
    public function __construct(ParserInterface $parser)
    {
        $this->eofCheck = ObjectFactory::get('rpc.setting')['package_eof'];
        $this->parser = $parser;
    }

    /**
     * @param mixed $data
     * @return string
     */
    public function encode(array $data): string
    {
        $data = $this->parser->encode(JsonHelper::encode($data, JSON_UNESCAPED_UNICODE));
        return $data . $this->eofCheck;
    }

    /**
     * @param string $data
     * @return mixed
     */
    public function decode(string $data)
    {
        $data = $this->parser->decode(rtrim($data, $this->eofCheck));
        $data = JsonHelper::decode($data, true);
        return $data;
    }
}