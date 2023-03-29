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
            'Id' => $this['uuid'],
            'TenBaiHoc' => $this['title'],
            'IdKhoaHoc' => $this['course']['uuid'],
            'STT' => $this['stt'],
            'Link1' => $this['backup_link_1'],
            'Link2' => $this['backup_link_2'],
            'file' => $this['lessons_files'] ? $this['lessons_files']['name'] : null,
            'pass' => $this['downloadable_files_pass'],
        ];



    }

}
