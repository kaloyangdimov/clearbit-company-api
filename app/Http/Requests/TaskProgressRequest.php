<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

class TaskProgressRequest extends ApiRequest
{
    public function authorize()
    {
        $allow = Task::where('user_id', auth()->user()->id)->where('id', request()->task_id)->first() ? true: false;
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
            'task_id' => 'required|integer|exists:tasks,id',
        ];
    }
}
