<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Auth;
use App\Http\Requests\StoreResumePost;
use App\Models\Resume;
use App\Models\ResumeWork;
use App\Models\ResumePrj;
use App\Models\ResumeEdu;
use App\Helper\ApiResponse;
use Illuminate\Support\Facades\Gate;

class ResumesController extends Controller
{
    public function list(Request $request)
    {
        $resume = Resume::my()->with('resumeWorks')->with('resumePrjs')->with('resumeEdus')->paginate();

        $data = $resume->toArray();
        return $this->responseOk($data);
    }

    public function show(Resume $resume, Request $request)
    {
        $response = Gate::inspect('view', $resume, $request->user());
        if ( ! $response->allowed() ) {
            return $this->responseFail(ApiResponse::API_FORBIDDEN, $response->message());
        }

        $data = $resume->toArray();
        $data['work_experience'] = $resume->resumeWorks;
        $data['project_experience'] = $resume->resumePrjs;
        $data['education_experience'] = $resume->resumeEdus;
        return $this->responseOk($data);
    }

    public function mine(Request $request)
    {
        // $resume = $request->user()->resumes->first();
        $resume = Resume::where('upload_uid', Auth::user()->openid)->first();

        if ($resume !== null) {
            $data = $resume->toArray();
            $data['has_resume'] = true;
            $data['work_experience'] = $resume->resumeWorks;
            $data['project_experience'] = $resume->resumePrjs;
            $data['education_experience'] = $resume->resumeEdus;
        } else {
            $data = [
                'has_resume' => false
            ];
        }

        return $this->responseOk($data);
    }

    public function uploadAvatar(Request $request)
    {
        $avatarPath = NULL;
        $avatar = $request->file('avatar');
        if($request->hasFile('avatar')) {
            if (!$avatar->isValid()) {
                return $this->responseFail(ApiResponse::API_FILE_IS_NOT_VALID, '头像上传失败');
            }
            $avatarPath = Storage::disk('resume_avatar')->putFile(date('Y-m-d').'/'.Auth::user()->id, $avatar);
        }
        unset($avatar);

        $data = [ 'avatar_url' => $avatarPath ];
        return $this->responseOk($data);
    }

    public function store(StoreResumePost $request) //StoreResumePost Request
    {
        $avatarPath = NULL;
        $avatar = $request->file('avatar');
        if($request->hasFile('avatar')) {
            if (!$avatar->isValid()) {
                return $this->responseFail(ApiResponse::API_FILE_IS_NOT_VALID, '头像上传失败');
            }
            $avatarPath = Storage::disk('resume_avatar')->putFile(date('Y-m-d').'/'.$request->user()->id, $avatar);
        }
        unset($avatar);

        $resumePath = NULL;
        $file = $request->file('attachment');
        if($request->hasFile('attachment')) {
            if (!$file->isValid()) {
                return $this->responseFail(ApiResponse::API_FILE_IS_NOT_VALID, '简历附件上传失败');
            }
            $resumePath = Storage::disk('resume_append')->putFile(date('Y-m-d').'/'.$request->user()->id, $file);
        }
        unset($file);

        $data = $request->except(['avatar', 'attachment',
            'work_experience', 'project_experience', 'education_experience',
            'avatar_url', 'education_show',
            // 'is_show_real_name', 'work_at', 'birthday', 'self_introduction'
        ]);
        // die(var_dump($request->all()));
        $data['upload_uid'] = Auth::user()->openid;
        $data['avatar'] = $avatarPath;
        $data['attachment_path'] = $resumePath;
        // $data['source'] = array_keys($data['source']);
        $data['source'] = ['applets'];
        if ($request->exp_salary_flag === 1) {
            $data['exp_salary_min'] = NULL;
            $data['exp_salary_max'] = NULL;
            $data['exp_salary_count'] = NULL;
        }

        $work = $request->input('work_experience', []);
        $project = $request->input('project_experience', []);
        $education = $request->input('education_experience', []);

        DB::beginTransaction();
        try {
            $resume = new Resume($data);
            $resume->save();

            foreach ($work as $key => $value) {
                // unset($value['work_at']);
                $resumeWork = new ResumeWork($value);
                $resumeWork->resume_id = $resume->id;
                $resumeWork->save();
            }
            foreach ($project as $key => $value) {
                $resumePrj = new ResumePrj($value);
                $resumePrj->resume_id = $resume->id;
                $resumePrj->save();
            }
            foreach ($education as $key => $value) {
                $resumeEdu = new ResumeEdu($value);
                $resumeEdu->resume_id = $resume->id;
                $resumeEdu->save();
            }

            // ResumeUser::store($resume->id, Auth::user()->id, 'upload');

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->responseFail(ApiResponse::API_FAIL, $exception->getMessage());
        }

        return $this->responseOk();
    }

    public function update(Resume $resume, Request $request)
    {
        $avatarPath = NULL;
        $avatar = $request->file('avatar');
        if($request->hasFile('avatar')) {
            if (!$avatar->isValid()) {
                return $this->responseFail(ApiResponse::API_FILE_IS_NOT_VALID, '头像上传失败');
            }
            $avatarPath = Storage::disk('resume_avatar')->putFile(date('Y-m-d').'/'.$request->user()->id, $avatar);
        }
        unset($avatar);

        $resumePath = NULL;
        $file = $request->file('attachment');
        if($request->hasFile('attachment')) {
            if (!$file->isValid()) {
                return $this->responseFail(ApiResponse::API_FILE_IS_NOT_VALID, '简历附件上传失败');
            }
            $resumePath = Storage::disk('resume_append')->putFile(date('Y-m-d').'/'.$request->user()->id, $file);
        }
        unset($file);

        $data = $request->except('avatar', 'attachment', 'work_experience', 'project_experience', 'education_experience');
        foreach ($data as $key => $value) {
            if (!empty($value)) {
                $resume->$key = $value;
            }
        }
        $resume->save();
        // foreach ($data as $key => $value) {
        //     if (empty($value)) {
        //         unset($data[$key]);
        //     }
        // }
        // $resume->update($data);

        return $this->responseOk($resume->toArray());
    }

    public function destroy(Resume $resume)
    {
        if(Storage::disk('resume_append')->exists($resume->attachment_path)) {
            $delResult = Storage::disk('resume_append')->delete($resume->attachment_path);
            if($delResult === false) {
                return $this->responseFail();
            }
        }

        $resume->delete();

        return $this->responseOk();
    }

    public function send($resume, $request)
    {
        $resume->job_id = $request->job_id;
        $resume->save();

        return $this->responseOk();
    }

    public function workStore(Resume $resume, Request $request)
    {
        $resumeWork = new ResumeWork($request->all());
        $resumeWork->resume_id = $resume->id;
        $resumeWork->save();

        $data = $resumeWork->toArray();
        return $this->responseOk($data);
    }

    public function workUpdate(Resume $resume, ResumeWork $resumeWork, Request $request)
    {
        $data = $request->except('id', 'resume_id');
        foreach ($data as $key => $value) {
            $resumeWork->$key = $value;
        }
        $resume->resumeWorks()->save($resumeWork);

        return $this->responseOk();
    }

    public function workDestroy(Resume $resume, ResumeWork $resumeWork)
    {
        $resume->resumeWorks()->delete($resumeWork);

        return $this->responseOk();
    }

    public function educationStore(Resume $resume, Request $request)
    {
        $resumeEdu = new ResumeEdu($request->all());
        $resumeEdu->resume_id = $resume->id;
        $resumeEdu->save();

        $data = $resumeEdu->toArray();
        return $this->responseOk($data);
    }

    public function educationUpdate(Resume $resume, ResumeEdu $resumeEdu, Request $request)
    {
        $data = $request->except('id', 'resume_id');
        foreach ($data as $key => $value) {
            $resumeEdu->$key = $value;
        }
        $resume->resumeEdus()->save($resumeEdu);

        return $this->responseOk();
    }

    public function educationDestroy(Resume $resume, ResumeEdu $resumeEdu)
    {
        $resume->resumeEdus()->delete($resumeEdu);

        return $this->responseOk();
    }

    public function projectStore(Resume $resume, Request $request)
    {
        $resumePrj = new ResumePrj($request->all());
        $resumePrj->resume_id = $resume->id;
        $resumePrj->save();

        $data = $resumePrj->toArray();
        return $this->responseOk($data);
    }

    public function projectUpdate(Resume $resume, ResumePrj $resumePrj, Request $request)
    {
        $data = $request->except('id', 'resume_id');
        foreach ($data as $key => $value) {
            $resumePrj->$key = $value;
        }
        $resume->resumePrjs()->save($resumePrj);

        return $this->responseOk();
    }

    public function projectDestroy(Resume $resume, ResumePrj $resumePrj)
    {
        $resume->resumePrjs()->delete($resumePrj);

        return $this->responseOk();
    }
}
