<?php

namespace App\Library\Wechat\Sender;


abstract class Sender
{
    protected $msgType = '';

    public function __construct()
    {
        $class = static::class;
        $classInfoMap = explode('\\', $class);
        $this->msgType = strtolower(array_pop($classInfoMap));
    }

    abstract protected function format($to, $from, string $content);

    public function handle($to, $from, $content)
    {
        $data = $this->format($to, $from, $content);
        $data = $this->array2xml($data);
        return $data;
    }

    protected function array2xml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_array($val)) {
                $xml .= "<" . $key . ">" . $this->array2xml($val) . "</" . $key . ">";
            } else {
                if (is_string($val)) {
                    $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
                } else {
                    $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
                }
            }
        }
        $xml .= "</xml>";
        return $xml;
    }
}
