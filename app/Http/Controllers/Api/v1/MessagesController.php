<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Requests\ListMessageGet;
use App\Models\Message;
use App\Models\Job;

class MessagesController extends Controller
{
    public function list(ListMessageGet $request)
    {
        $type = (int)$request->type;

        $messages = Message::accept($type)->with('sender')->with('mould')->paginate();

        $data = $messages->toArray();
        if ($type === 1) {
            foreach ($data['data'] as $index => $message) {
                if ( isset($message['content']['job_id']) ) {
                    $job = Job::with('company')->with('publisher')->find($message['content']['job_id']);
                    $data['data'][$index]['job'] = $job;
                }
            }
        }

        return $this->responseOk($data);
    }

    public function update(Message $message, Request $request)
    {
        $message->update($request->all());

        return $this->responseOk();
    }

    public function destroy(Message $message)
    {
        $message->delete();

        return $this->responseOk();
    }

    public function read(Request $request)
    {
        $ids = $request->input('id', []);

        if (is_array($ids) && count($ids) > 0) {
            Message::whereIn('id', $ids)->update(['is_read' => true]);
        }

        return $this->responseOk();
    }
}
