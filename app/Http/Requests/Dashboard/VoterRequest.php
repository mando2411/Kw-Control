<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class VoterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function attributes(): array
    {
        $attributes = [
"name" => __('main.Name'),
"type" => __('main.Type'),
"almrgaa" => __('main.Almrgaa'),
"albtn" => __('main.Albtn'),
"alfraa" => __('main.Alfraa'),
"yearOfBirth" => __('main.YearOfBirth'),
"btn_almoyhy" => __('main.BtnAlmoyhy'),
"tary_kh_alandmam" => __('main.TaryKhAlandmam'),
"alrkm_ala_yl_llaanoan" => __('main.AlrkmAlaYlLlaanoan'),
"alktaa" => __('main.Alktaa'),
"alrkm_almd_yn" => __('main.AlrkmAlmdYn'),
"alsndok" => __('main.Alsndok'),
"phone" => __('main.Phone'),
"region" => __('main.Region'),
"status" => __('main.Status'),
"committee_id" => "__('main.committee')",
"family_id" => __('main.Family'),
"alfkhd" => __('main.Alfkhd'),
"age" => __('main.Age'),
"user_id" => "UserId",
];

        return $attributes;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required'],
'type' => ['required'],
'almrgaa' => ['required'],
'albtn' => ['required'],
'alfraa' => ['required'],
'yearOfBirth' => ['required'],
'btn_almoyhy' => ['required'],
'tary_kh_alandmam' => ['required'],
'alrkm_ala_yl_llaanoan' => ['required'],
'alktaa' => ['required'],
'alrkm_almd_yn' => ['required'],
'alsndok' => ['required'],
'phone' => ['required'],
'region' => ['required'],
'status' => ['nullable'],
'committee_id' => ['nullable'],
'family_id' => ['nullable'],
'alfkhd' => ['required'],
'age' => ['required','integer','max:150'],
'user_id' => ['nullable'],

        ];

        return $rules;
    }

    /**
     * Get the validated fields.
     *
     * @return array
     */
     public function getSanitized(): array
     {
        $data = $this->validated();
        $data['status'] = $this->boolean('status');
        return $data;
     }
}
