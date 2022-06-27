<?php
#+------------------------------------------------------------------
#| 普通的。
#+------------------------------------------------------------------
#| Author:Janmas Cromwell <janmas-cromwell@outlook.com>
#+------------------------------------------------------------------
namespace App\Model;

class WeChatmessage extends Model
{
    protected $table = 'wechat_message';
    protected $fillable = [
        'msg_id',
        'content',
        'openid',
        'create_time',
        'type',
        'command',
    ];
    public $timestamps = false;

    static function isExists(string $msgId)
    {
        return self::query()->where('msg_id', $msgId)->count() > 0;
    }

    /**
     * @param string $msgId 消息id
     * @param int $msgType 消息类型 0普通消息 1指令
     * @param string $content 消息内容
     * @param string $fromUserName 发送者
     * @param string $command 发送者
     * @return WeChatmessage|\Hyperf\Database\Model\Model
     */
    static function saveMessage($fromUserName, $msgId, $msgType = 0, $content, $command = '')
    {
        $data = [
            'msg_id'      => $msgId,
            'content'     => $content,
            'openid'      => $fromUserName,
            'create_time' => time(),
            'type'        => $msgType,
            'command'     => $command
        ];
        return self::create($data);
    }
}
