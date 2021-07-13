<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Job;
use App\Models\JobUser;
use App\Helper\ApiResponse;
use Auth;

class JobsController extends Controller
{
    public function __construct()
    {
        // code
    }

    public function list(Request $request)
    {
        $jobs = Job::status(Job::STATUS_ACTIVE)
            ->searchByType($request->input('type', ''))
            ->searchByLocation($request->input('location', ''))
            ->searchBySalary($request->input('salary_min', ''), $request->input('salary_max', ''))
            ->searchByCreatedAt($request->input('duration', ''))
            ->searchByExperience($request->input('experience', ''))
            ->searchByEducation($request->input('education', ''))
            ->paginate()
        ;

        $data = $jobs->toArray();
        return $this->responseOk($data);
    }

    public function show(Job $job, Request $request)
    {
        $data = $job->toArray();
        return $this->responseOk($data);
    }

    public function collect(Job $job)
    {
        $jobUser = JobUser::collect(Auth::user()->id, $job->id)->first();

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
