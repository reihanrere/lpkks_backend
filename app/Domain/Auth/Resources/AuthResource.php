<?php

namespace App\Domain\Auth\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'roles'      => $this->getRoleNames(), // dari Spatie
            'permissions'=> $this->getPermissionNames(),
            'created_at' => $this->created_at,
        ];
    }
}
