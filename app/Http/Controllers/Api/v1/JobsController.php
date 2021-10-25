<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Job;
use App\Models\JobUser;
use App\Models\JobPublisher;
use App\Models\JobResume;
use App\Helper\ApiResponse;
use Auth;

class JobsController extends Controller
{
    public function __construct()
    {
        // ...
    }

    public function list(Request $request)
    {
        $jobs = Job::status(Job::STATUS_ACTIVE)
            // ->searchByLocation($request->input('location', ''))
            ->searchByType($request->input('type', ''))
            ->searchBySalary($request->input('salary_min', ''), $request->input('salary_max', ''))
            ->searchByPubdate($request->input('pubdate', ''))
            ->searchByExperience($request->input('experience', ''))
            ->searchByEducation($request->input('education', ''))
            ->search($request->input('search', ''))
            ->with('company')
        ;
        if ($request->filled('location')) {
            $jobs->orderByRaw('(CASE WHEN location->"$.city" LIKE "%'.$request->location.'%" THEN 1 WHEN location->"$.province" LIKE "%'.$request->location.'%" THEN 2 ELSE 3 END)');
        }
        $jobs = $jobs->orderBy('updated_at', 'desc')->paginate();

        $data = $jobs->toArray();
        return $this->responseOk($data);
    }

    public function listCollect(Request $request)
    {
        // $jobs = Job::status(Job::STATUS_ACTIVE)
        //     ->whereHas('collects', function ($query) {
        //         return $query->from(config('database.connections.mysql.database').'.users');
        //     })
        //     ->paginate()
        // ;
        $jobid = JobUser::myCollect()->get()->pluck('job_id');
        $jobs = Job::status(Job::STATUS_ACTIVE)->whereIn('id', $jobid)->with('company')->paginate();

        $data = $jobs->toArray();
        return $this->responseOk($data);
    }

    public function show(Job $job, Request $request)
    {
        $data = $job->toArray();
        $data['company'] = $job->company;
        $data['publisher'] = $job->publisher;
        return $this->responseOk($data);
    }

    public function doCollect(Job $job)
    {
        $jobUser = JobUser::isCollect(Auth::user()->id, $job->id)->first();

        if ($jobUser) {
            $jobUser->delete();
        } else {
            JobUser::create([
                'job_id' => $job->id,
                'user_id' => Auth::user()->id,
                'type' => 'collect',
            ]);
        }

        return $this->responseOk();
    }

    public function doDeliver(Job $job)
    {
        $jobUser = JobUser::isDeliver(Auth::user()->id, $job->id)->first();

        if ($jobUser) {
            $jobUser->delete();
        } else {
            JobUser::create([
                'job_id' => $job->id,
                'user_id' => Auth::user()->id,
                'type' => 'deliver',
            ]);
        }

        return $this->responseOk();
    }

    public function listDeliver(Request $request)
    {
        $jobid = JobUser::myDeliver()->get()->pluck('job_id');
        $jobs = Job::status(Job::STATUS_ACTIVE)->whereIn('id', $jobid)->with('company')->paginate();

        $data = $jobs->toArray();
        return $this->responseOk($data);
    }

    // public function push($request)
    // {
    //     $swoole = app('swoole');

    //     foreach ($swoole->wsTable as $ws) {
    //         Log::debug('ws', $ws);
    //         $fd = $ws['value'];
    //         $data = [
    //             'flag' => 'message',
    //             'type' => 1,
    //             'msg' => [ 'content' => 'tttttt' ],
    //             'mould_id' => 1
    //         ];
    //         $success = $swoole->push($fd, json_encode($data));
    //     }

    //     return;
    // }
}
