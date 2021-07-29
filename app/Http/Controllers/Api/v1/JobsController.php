<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\JobUser;
use App\Models\JobPublisher;
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
            ->searchByType($request->input('type', ''))
            ->searchByLocation($request->input('location', ''))
            ->searchBySalary($request->input('salary_min', ''), $request->input('salary_max', ''))
            ->searchByPubdate($request->input('pubdate', ''))
            ->searchByExperience($request->input('experience', ''))
            ->searchByEducation($request->input('education', ''))
            ->with('company')
            ->orderBy('updated_at', 'desc')
            ->paginate()
        ;

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
        $jobs = Job::status(Job::STATUS_ACTIVE)->whereIn('id', $jobid)->paginate();

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
}
