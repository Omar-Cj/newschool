<?php

namespace App\Http\Requests\Academic\ClassRoom;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClassRoomStoreRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $schoolId = $this->user()->school_id;
        $branchId = $this->user()->branch_id;

        return [
            'room_no' => [
                'required',
                'string',
                'max:10',
                Rule::unique('class_rooms', 'room_no')
                    ->where('school_id', $schoolId)
                    ->where('branch_id', $branchId),
            ],
            'capacity'  => 'required|max:10',
            'status'    => 'required'
        ];
    }
}
