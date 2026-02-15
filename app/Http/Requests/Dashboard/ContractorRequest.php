<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContractorRequest extends FormRequest
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
"parent_id" => "ParentId",
"user_id" => "UserId",
"election_id" => "ElectionId",
'name' =>"Name",
'email' =>"Email",
'phone' =>"Phone",
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
            'parent_id' => ['nullable'],
            'election_id' => ['nullable'],
            'user_id' => ['nullable'],
            'note' => ['nullable'],
            'token' => ['nullable'],
            'name'=>['required'],
            'email'=>['nullable'],
            'phone'=>['required'],

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
        $data=$this->validated();
         $token = bin2hex(random_bytes(32));
        $data['token']=$token;
		 if(auth()->user()->contractor){
            $data['creator_id']=auth()->user()->creator_id;
        }else{
            $data['creator_id']=auth()->user()->id;
        }        
		 $data['election_id']=auth()->user()->election_id;
        return $data;
     }
}
