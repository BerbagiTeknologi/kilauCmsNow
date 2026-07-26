<?php

namespace App\Http\Resources\Donasi;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Storage;

class DonasiCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($item) {
            return [
                'id' => $item->id,
                'nama' => $item->nama,
                'file_url' => $item->file ? Storage::url($item->file) : null,
                'icon_iklan' => $item->icon_iklan ?? 'fa-money-bill-wave',
                'name_button_iklan' => $item->name_button_iklan ?? 'Berbagi Sekarang',
                'status' => $item->statuskilauiklan,
                'link' => $item->link,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ];
        })->toArray();
    }
}
