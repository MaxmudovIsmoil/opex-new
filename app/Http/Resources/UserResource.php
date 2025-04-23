<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'full_name' => $this->full_name,
            'username' => $this->username,
            'order_type_id' => $this->order_type_id,
            'instance_id' => $this->instnace_id,
            'can_create_order' => $this->can_create_order,
            'can_order_detail_edit' => $this->can_order_detail_edit,
            'language' => $this->language,
            'status' => $this->status,
            'rule' => $this->rule,
        ];
    }
}
