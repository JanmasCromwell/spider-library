<?php

namespace App\Library\Wechat\Message;

use App\Model\User;

/**
 *   return '<xml>
 * <ToUserName><![CDATA[' . $data['FromUserName'] . ']]></ToUserName>
 * <FromUserName><![CDATA[' . $data['ToUserName'] . ']]></FromUserName>
 * <CreateTime>' . time() . '</CreateTime>
 * <MsgType><![CDATA[text]]></MsgType>
 * <Content><![CDATA[您好，您的openid是' . $data['FromUserName'] . '，感谢您的关注]]></Content>
 * <MsgId>' . uniqid() . '</MsgId>
 * </xml>';
 */
class Subscribe extends AbstractMessage
{

    public function handle(array $data)
    {
        //<xml>
        //  <ToUserName><![CDATA[toUser]]></ToUserName>
        //  <FromUserName><![CDATA[FromUser]]></FromUserName>
        //  <CreateTime>123456789</CreateTime>
        //  <MsgType><![CDATA[event]]></MsgType>
        //  <Event><![CDATA[subscribe]]></Event>
        //</xml>
        $openid = $data['FromUserName'];
        if (User::where('openid', $openid)->count()) {
            $user = User::query()->where('openid', $openid)->first();
            $username = $user->username;
            $content = <<<CONTENT
感谢你的再次关注！
您的用户名是:$username
您可以输入并发送
    `搜书:书名`来查找书籍
    `已读历史`来查看读书记录
    `书签`来查看书签记录
    `书源:网址`来提交书源
注意：以上符号皆为英文符号
CONTENT;

            return (new \App\Library\Wechat\Sender\Text())->handle($data['FromUserName'], $data['ToUserName'], $content);
        }
        $username = User::generateUserName();
        $password = User::generatePasswd();
        $res = User::create([
            'openid'    => $openid,
            'avatar'    => 'asdasdasd',
            'username'  => $username,
            'create_at' => time(),
            'password'  => password_hash($password, PASSWORD_DEFAULT)
        ]);

        if ($res) {
            $content = <<<CONTENT
感谢你的关注！\r\n您的用户名是:$username\r\n您的密码是：$password\r\n还请牢记账号，它将会是您找回密码的唯一凭证。您可以在输入框中输入并发送`搜书:书名`来查找书籍，输入`已读历史`来查看读书记录，输入`书签`来查看书签记录，输入`书源:http://xxxxxx.com`来提交书源。注意：以上符号皆为英文符号
CONTENT;

            return (new \App\Library\Wechat\Sender\Text())->handle($data['FromUserName'], $data['ToUserName'], $content);

        }

        return '';


    }
}