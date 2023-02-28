<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;


class CoursesResource extends JsonResource
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
            'Id' => $this->uuid,
            'TenKhoaHoc' => $this->title,
//            'STT' => 1,
            'Count' => count($this->publishedLessons),
            'TrangThai' => $this->published,

        ];
    }

    public static function allowedIncludes()
    {
        return ['baihoc', 'category'];
    }
}
