<?php

namespace Modules\User\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'phone ' => $this->phone,
            'status' => $this->status,
            'user_detail' => new UserDetailResource($this->userDetail),
            // 'adminRoles' => $this->getRoleNames()[0] ?? null,
            'profilePic' => optional($this->getFirstMedia('user_profile'))->getUrl() ?? null,

        ];
    }
}
