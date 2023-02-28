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
//        dd($this);
        return [
            'Id' => $this->uuid,
            'TenBaiHoc' => $this->title,
            'IdKhoaHoc' => $this->course->uuid,
//            'STT' => 1,
            'Link1' => $this->backup_link_1,
            'Link2' => $this->backup_link_2,
            'file' => $this->mediaFiles ? $this->mediaFiles->name : null,
            'pass' => $this->downloadable_files_pass,
        ];



    }

}
