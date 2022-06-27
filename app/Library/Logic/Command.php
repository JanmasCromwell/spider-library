<?php
#+------------------------------------------------------------------
#| 普通的。
#+------------------------------------------------------------------
#| Author:Janmas Cromwell <janmas-cromwell@outlook.com>
#+------------------------------------------------------------------
namespace App\Library\Logic;

class Command extends Logic
{

    /**
     * 命令
     * @param string $content
     * @return string
     */
    public function done(string $content): string
    {
        list($command, $name) = explode('@', $content);
        return '您搜索的书本' . $name . '获取还没有结束，请稍后重试';
    }
}
