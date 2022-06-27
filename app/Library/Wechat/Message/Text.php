<?php
#+------------------------------------------------------------------
#| 普通的。
#+------------------------------------------------------------------
#| Author:Janmas Cromwell <janmas-cromwell@outlook.com>
#+------------------------------------------------------------------
namespace App\Library\Wechat\Message;


use App\Constants\Instruction;
use App\Library\Logic\Command;
use App\Library\Logic\Search;
use App\Library\Logic\Source;
use App\Library\Logic\Tag;
use App\Model\User;
use App\Model\WeChatmessage;

class Text extends AbstractMessage
{

    public function handle(array $data)
    {

        if (!User::existsOpenid($data['FromUserName'])) {
            $username = User::generateUserName();
            $password = User::generatePasswd();
            User::create([
                'openid'    => $data['FromUserName'],
                'avatar'    => 'asdasdasd',
                'username'  => $username,
                'create_at' => time(),
                'password'  => password_hash($password, PASSWORD_DEFAULT)
            ]);
        }

        //保存消息
        $content = trim($data['Content']);
        $command = '';
        if (strpos($content, ':') !== false) {
            $eq = strpos($content, ':');
            $command = substr($content, 0, $eq);
            $content = substr($content, $eq + 1);
        }
        if (!WeChatmessage::isExists($data['MsgId'])) {
            $type = empty($command) ? 0 : 1;
            WeChatmessage::saveMessage($data['FromUserName'], $data['MsgId'], $type, $content, $command);
        }
        if (!empty($command)) {
            switch ($command) {
                case Instruction::Search:
                    $commandInstance = new Search();
                    break;
                case Instruction::Source:
                    $commandInstance = new Source();
                    break;
                case Instruction::Tag:
                    $commandInstance = new Tag();
                    break;
                case Instruction::Command:
                    $commandInstance = new Command();
                    break;
                default:
                    $commandInstance = new Robot();
            }
            $content = $commandInstance->done($content);
        }
        return (new \App\Library\Wechat\Sender\Text())->handle($data['FromUserName'], $data['ToUserName'], $content);
    }
}
