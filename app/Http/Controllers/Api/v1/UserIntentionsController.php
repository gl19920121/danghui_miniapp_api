<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserIntention;
use App\Http\Requests\StoreUserIntentionPost;
use App\Helper\ApiResponse;
use Illuminate\Support\Facades\Gate;

class UserIntentionsController extends Controller
{
    public function list()
    {
        $userIntentions = UserIntention::get();

        $data = Array();
        $data['data'] = $userIntentions->toArray();
        $data['max_size'] = UserIntention::MAX_SIZE;
        return $this->responseOk($data);
    }

    public function show(UserIntention $userIntention)
    {
        $data = $userIntention->toArray();
        return $this->responseOk($data);
    }

    public function store(StoreUserIntentionPost $request)
    {
        $response = Gate::inspect('create', UserIntention::class, $request->user());
        if ( ! $response->allowed() ) {
            return $this->responseFail(ApiResponse::API_FORBIDDEN, $response->message());
        }

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

        if ($userIntention->id !== NULL) {
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
