<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;


class LessonsResource extends JsonResource
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
            'Id' => $this->id,
            'TenBaiHoc' => $this->title,
            'IdKhoaHoc' => $this->course_id,
            'STT' => 1,
            'Link1' => $this->backup_link_1,
            'Link2' => $this->backup_link_2,
            'file' => $this->mediaFiles ? $this->mediaFiles->name : null,
            'pass' => $this->downloadable_files_pass,
        ];



    }

}
