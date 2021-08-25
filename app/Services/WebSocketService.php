<?php
/**
 * Created by PhpStorm.
 * User: wxiangqian
 * Date: 2020-10-28
 * Time: 15:23
 */

namespace App\Services;
use Hhxsv5\LaravelS\Swoole\WebSocketHandlerInterface;
use Illuminate\Support\Facades\Log;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;
use App\Models\Message;
use Auth;

/**
 * @see https://wiki.swoole.com/#/start/start_ws_server
 */
class WebSocketService implements WebSocketHandlerInterface
{
    private $wsTable;

    public function __construct()
    {
        $this->wsTable = app('swoole')->wsTable;
    }

    public function onOpen(Server $server, Request $request)
    {
        if ( ! Auth::check() ) {
            // 未登录用户直接断开连接
            $server->disconnect($request->fd);
            return;
        }

        $flag = 'open';
        $userId = Auth::user()->id;

        $this->wsTable->set('uid:' . $userId, ['value' => $request->fd]); // 绑定uid到fd的映射
        $this->wsTable->set('fd:' . $request->fd, ['value' => $userId]); // 绑定fd到uid的映射

        $data = [
            'sum' => Message::acceptUnread()->count(),
            'broadcast' => Message::AcceptBroadcastUnread()->count(),
            'notice' => Message::AcceptNoticeUnread()->count(),
        ];
        $this->push($server, $request->fd, $data, $flag);
    }

    public function onMessage(Server $server, Frame $frame)
    {
        $flag = 'message';
        $data = json_decode($frame->data);

        if (isset($data->flag) && $data->flag === 'heart') { // 心跳检测
            // $this->push($server, $frame->fd, 'heart');
            return;
        }

        if ( ! (isset($data->type) && isset($data->msg) && isset($data->mould_id))) {
            return;
        }

        $type = $data->type;
        $fromUid = $this->wsTable->get('fd:' . $frame->fd)['value'];
        $msg = $data->msg;
        $mouldId = $data->mould_id;

        switch ($type) {
            case 0: // 广播
                foreach ($this->wsTable as $key => $row) {
                    if (strpos($key, 'uid:') === 0 && $server->isEstablished($row['value'])) {
                        $toUid = $this->wsTable->get('fd:' . $row['value'])['value'];
                        $content = [
                            'from' => $fromUid,
                            'data' => $msg,
                        ];
                        $this->push($server, $row['value'], $content, $flag, $type);
                        $message = new Message;
                        $message->send_uid = $fromUid;
                        $message->accept_uid = $toUid;
                        $message->type = $data->type;
                        $message->mould_id = $data->mould_id;
                        $message->content = $data->msg;
                        $message->save();
                    }
                }
                break;
            case 1: // 沟通邀请
                $toUid = $data->accept_uid;
                $toFd = $this->wsTable->get('uid:' . $toUid)['value'];
                $message = new Message;
                $message->send_uid = $fromUid;
                $message->accept_uid = $toUid;
                $message->type = $data->type;
                $message->mould_id = $data->mould_id;
                $message->content = $data->msg;
                $message->save();
                $content = [
                    'from' => $fromUid,
                    'data' => $msg,
                ];
                $this->push($server, $toFd, $content, $flag, $type);
                break;
            case 2: // 私聊消息
                $toUid = $data->accept_uid;
                $toFd = $this->wsTable->get('uid:' . $toUid)['value'];
                $message = new Message;
                $message->send_uid = $fromUid;
                $message->accept_uid = $toUid;
                $message->type = $data->type;
                $message->mould_id = $data->mould_id;
                $message->content = $data->msg;
                $message->save();
                $content = [
                    'from' => $fromUid,
                    'data' => $msg,
                ];
                $this->push($server, $toFd, $content, $flag, $type);
                break;

            default:
                break;
        }
    }

    public function onClose(Server $server, $fd, $reactorId)
    {
        $uid = $this->wsTable->get('fd:' . $fd);
        if ($uid !== false) {
            $this->wsTable->del('uid:' . $uid['value']); // 解绑uid映射
        }
        $this->wsTable->del('fd:' . $fd);// 解绑fd映射
    }

    private function push($server, $fd, $data, $flag, $type = null)
    {
        $data['flag'] = $flag;
        if ($type) {
            $data['type'] = $type;
        }
        $data = is_array($data) ? json_encode($data) : $data;

        $server->push($fd, $data);
    }

}
