<?php

namespace App\Http\Requests\Transportation\Bus;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BusStoreRequest extends FormRequest
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
        $branchId = auth()->user()->branch_id ?? 1;

        return [
            'area_name'    => [
                'required',
                'string',
                'max:255',
                Rule::unique('buses')->where(function ($query) use ($branchId) {
                    return $query->where('branch_id', $branchId);
                })
            ],
            'bus_number'   => 'nullable|string|max:100',
            'capacity'     => 'nullable|integer|min:1|max:200',
            'driver_name'  => 'nullable|string|max:255',
            'driver_phone' => 'nullable|string|max:50',
            'license_plate' => 'nullable|string|max:100',
            'status'       => 'nullable|integer|in:1,2'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'area_name'     => ___('transportation.area_name'),
            'bus_number'    => ___('transportation.bus_number'),
            'capacity'      => ___('transportation.capacity'),
            'driver_name'   => ___('transportation.driver_name'),
            'driver_phone'  => ___('transportation.driver_phone'),
            'license_plate' => ___('transportation.license_plate'),
            'status'        => ___('common.status')
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'area_name.required' => ___('transportation.area_name_required'),
            'area_name.unique'   => ___('transportation.area_name_unique'),
            'capacity.min'       => ___('transportation.capacity_min'),
            'capacity.max'       => ___('transportation.capacity_max')
        ];
    }
}
