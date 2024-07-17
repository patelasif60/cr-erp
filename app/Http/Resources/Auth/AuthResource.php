<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Resources\Json\JsonResource;
use Auth;
use App\User;
use Illuminate\Support\Facades\Hash;


class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'token' => $this->id,#$this->createToken('Laravel Password Grant Client')->accessToken,
            'token_type' => 'Bearer',
            'user_id'=>$this->id,
            'user_name'=>$this->name,
            'user_email'=>$this->email,
            'user_role_id'=>$this->role,
            'user_role' => $this->user_role['role'],
        ];
    }
}
