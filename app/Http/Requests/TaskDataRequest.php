<?php

namespace App\Http\Requests;

use App\Models\Task;

class TaskDataRequest extends ApiRequest
{
    public function authorize()
    {
        $allow = Task::where('user_id', auth()->user()->id)->whereJsonContains('criteria->company_domain', request()->company_domain)->first() ? true : false;

        return $allow;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'company_domain' => 'required|string'
        ];
    }
}
