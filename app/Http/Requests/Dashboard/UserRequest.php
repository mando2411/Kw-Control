<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UserRequest extends FormRequest
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
        return [
            "name" => "Name",
            "email" => "Email",
            "password" => "Password",
            "role" => "Role",
            "phone" => "Phone",
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', Rule::unique('users')->ignore(request('user'))],
            'password' => $this->isMethod('POST') ? ['nullable', 'min:8'] : ['nullable'],
            'roles' => ['nullable', 'array', 'min:1'],
            'image' => ['nullable'],
            'phone' => ['required_unless:fake,1', 'string', Rule::unique('users')->ignore(request('user'))],

        ];
    }

    /**
     * Get the validated fields.
     *
     * @return array
     */
    public function getSanitized(): array
    {
        $data = $this->validated();
        unset($data['roles'], $data['password']);

        $isCreateRequest = !request('user');

        if ($isCreateRequest && empty($data['email'])) {
            $data['email'] = 'auto+' . Str::uuid() . '@kw-control.local';
        }

        if (!$isCreateRequest && array_key_exists('email', $data) && empty($data['email'])) {
            unset($data['email']);
        }

        if ($this->get('password')) {
            $data['password'] = \Hash::make($this->get('password'));
        }elseif($isCreateRequest){
            $data['password'] =\Hash::make("1");
        }
        $data['creator_id']= auth()->user()->id;
        if(!auth()->user()->hasRole('Administrator')){
            $data['election_id']= auth()->user()->election_id;
        }else{
            $data['election_id']= request('election_id');
        }
        return $data;
    }
}
