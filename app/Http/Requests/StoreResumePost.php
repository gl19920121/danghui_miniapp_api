<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResumePost extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'avatar' => 'nullable|mimes:jpeg,jpg,png',
            'name' => 'required',
            'sex' => 'required',
            'age' => 'required|numeric',
            'location.province' => 'required',
            'location.city' => 'required',
            'location.district' => 'nullable',
            'work_years_flag' => 'required|numeric',
            'work_years' => 'required_if:work_years_flag,0|numeric',
            'education' => 'required',
            'major' => 'nullable|string|max:255',
            'phone_num' => 'required|max:11',
            'email' => 'required|string|max:255',
            'wechat' => 'nullable|string|max:255',
            'qq' => 'nullable|string|max:255',
            'cur_industry.st' => 'nullable',
            'cur_industry.nd' => 'nullable',
            'cur_industry.rd' => 'nullable',
            'cur_industry.th' => 'nullable',
            'cur_position.st' => 'nullable',
            'cur_position.nd' => 'nullable',
            'cur_position.rd' => 'nullable',
            'cur_company' => 'nullable|string|max:255',
            'cur_salary' => 'nullable|numeric',
            'cur_salary_count' => 'nullable|numeric',
            'exp_industry.st' => 'nullable',
            'exp_industry.nd' => 'nullable',
            'exp_industry.rd' => 'nullable',
            'exp_industry.th' => 'nullable',
            'exp_position.st' => 'required',
            'exp_position.nd' => 'required',
            'exp_position.rd' => 'required',
            'exp_work_nature' => 'nullable',
            'exp_location.province' => 'required',
            'exp_location.city' => 'required',
            'exp_location.district' => 'nullable',
            'exp_salary_flag' => 'required|numeric',
            'exp_salary_min' => 'required_if:exp_salary_flag,0|numeric',
            'exp_salary_max' => 'required_if:exp_salary_flag,0|numeric',
            'exp_salary_count' => 'required_if:exp_salary_flag,0|numeric',
            'work_experience' => 'required|array',
            'work_experience.*.company_name' => 'required',
            'work_experience.*.company_nature' => 'nullable',
            'work_experience.*.company_scale' => 'nullable',
            'work_experience.*.company_investment' => 'nullable',
            'work_experience.*.company_industry.st' => 'nullable',
            'work_experience.*.company_industry.nd' => 'nullable',
            'work_experience.*.company_industry.rd' => 'nullable',
            'work_experience.*.company_industry.th' => 'nullable',
            'work_experience.*.job_type.st' => 'required',
            'work_experience.*.job_type.nd' => 'required',
            'work_experience.*.job_type.rd' => 'required',
            'work_experience.*.salary' => 'required',
            'work_experience.*.salary_count' => 'required',
            'work_experience.*.subordinates' => 'nullable|numeric',
            'work_experience.*.start_at' => 'required',
            'work_experience.*.end_at' => 'required_without:work_experience.*.is_not_end',
            'work_experience.*.is_not_end' => 'filled',
            'work_experience.*.work_desc' => 'required',
            'project_experience' => 'nullable|array',
            'project_experience.*.name' => 'nullable',
            'project_experience.*.role' => 'nullable',
            'project_experience.*.start_at' => 'nullable',
            'project_experience.*.end_at' => 'nullable',
            'project_experience.*.is_not_end' => 'filled',
            'project_experience.*.body' => 'nullable',
            'education_experience' => 'required|array',
            'education_experience.*.school_name' => 'required',
            'education_experience.*.school_level' => 'required',
            'education_experience.*.major' => 'nullable',
            'education_experience.*.start_at' => 'nullable',
            'education_experience.*.end_at' => 'nullable',
            'education_experience.*.is_not_end' => 'filled',
            'social_home' => 'nullable',
            'personal_advantage' => 'nullable',
            'attachment' => 'nullable|file|mimes:doc,docx',
            'jobhunter_status' => 'nullable|numeric',
            'blacklist' => 'nullable',
            'remark' => 'nullable',
            'source' => 'required',
            'source_remarks' => 'nullable|string|max:255',
        ];
    }
}
