<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use JsonSerializable;

/**
 * UserResource
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        /**
         * User
         *
         * @var User $user
         */
        $user = Auth::user();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'user_type' => $this->user_type,
            'phone_code' => $this->phone_code,
            'phone_number' => $this->phone_number,
            'profile_image' => $this->profile_image_url,
            'is_profile_completed' => $this->is_profile_completed,
            'status' => $this->status,
        ];
    }
}
