<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserIntention;
use App\Http\Requests\StoreUserIntentionPost;
use App\Helper\ApiResponse;

class UserIntentionsController extends Controller
{
    public function list()
    {
        $userIntentions = UserIntention::get();

        $data = $userIntentions->toArray();
        return $this->responseOk($data);
    }

    public function show(UserIntention $userIntention)
    {
        $data = $userIntention->toArray();
        return $this->responseOk($data);
    }

    public function store(StoreUserIntentionPost $request)
    {
        $userIntention = UserIntention::firstOrNew(
            [
                'type' => $request->type,
                'city' => $request->city,
                'position->rd' => $request->position['rd'],
                'industry->th' => $request->industry['th'],
                // 'salary' => $request->salary,
                'user_id' => $request->user()->id,
            ],
            [
                'type' => $request->type,
                'city' => $request->city,
                'position' => $request->position,
                'industry' => $request->industry,
                'salary' => $request->salary,
                'user_id' => $request->user()->id,
            ]
        );

        if ($userIntention !== NULL) {
            return $this->responseFail(ApiResponse::API_ACCEPTED, '求职意向已经存在了');
        }

        $userIntention->save();

        return $this->responseOk();
    }

    public function update(UserIntention $userIntention, Request $request)
    {
        $userIntention->update($request->all());

        return $this->responseOk();
    }

    public function destroy(UserIntention $userIntention)
    {
        $userIntention->delete();

        return $this->responseOk();
    }
}
