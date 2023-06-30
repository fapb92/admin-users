<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $role = $this->getActiveRole();
        return [
            'id' => $this->id,
            "name" => $this->name,
            "email" => $this->email,
            "register_at" => $this->created_at,
            "last_update" => $this->updated_at,
            "avatar_url" => $this->avatar_url,
            "role_permission_data"=>new ActiveRoleResource($role)
        ];
    }
}
