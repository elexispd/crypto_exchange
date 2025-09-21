<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KycDocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'document_type' => $this->document_type,
            'document_number' => $this->document_number,
            'front_image' => $this->front_image,
            'status' => $this->status,
            'rejection_reason' => $this->rejection_reason,
            'verified_at' => $this->verified_at,
            'created_at' => $this->created_at,
        ];
    }
}
