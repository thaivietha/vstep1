<?php

namespace App\Http\Controllers\Traits;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

trait FileUploadTrait
{

    /**
     * File upload trait used in controllers to upload files
     */
    public function saveFiles(Request $request)
    {
        ini_set('memory_limit', '-1');
        if (!file_exists(public_path('storage/uploads'))) {
            mkdir(public_path('storage/uploads'), 0777);
            mkdir(public_path('storage/uploads/thumb'), 0777);
        }

        $finalRequest = $request;

        foreach ($request->all() as $key => $value) {
            if ($request->hasFile($key)) {
                if ($request->has($key . '_max_width') && $request->has($key . '_max_height')) {
                    // Check file width
                    $extension = array_last(explode('.', $request->file($key)->getClientOriginalName()));
                    $filename = md5(time()) . '.' . $extension; // a unique file name
                    $file = $request->file($key);
                    $image = Image::make($file);
                    if (!file_exists(public_path('storage/uploads/thumb'))) {
                        mkdir(public_path('storage/uploads/thumb'), 0777, true);
                    }

                    Image::make($file)->resize(50, 50)->save(public_path('storage/uploads/thumb') . '/' . $filename);

                    $width = $image->width();
                    $height = $image->height();
                    if ($width > $request->{$key . '_max_width'} && $height > $request->{$key . '_max_height'}) {
                        $image->resize($request->{$key . '_max_width'}, $request->{$key . '_max_height'});
                    } elseif ($width > $request->{$key . '_max_width'}) {
                        $image->resize($request->{$key . '_max_width'}, null, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                    } elseif ($height > $request->{$key . '_max_width'}) {
                        $image->resize(null, $request->{$key . '_max_height'}, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                    }
                    $image->save(public_path('storage/uploads') . '/' . $filename);
                    $finalRequest = new Request(array_merge($finalRequest->all(), [$key => $filename]));
                } else {

                    $extension = array_last(explode('.', $request->file($key)->getClientOriginalName()));
                    $filename = md5(time()) . '.' . $extension; // a unique file name
                    $request->file($key)->move(public_path('storage/uploads'), $filename);
                    $finalRequest = new Request(array_merge($finalRequest->all(), [$key => $filename]));
                }
            }
        }
        return $finalRequest;
    }


    public function saveAllFiles(Request $request, $downloadable_file_input = null, $model_type = null, $model = null,$folder=null)
    {


        ini_set('memory_limit', '-1');
        if (!file_exists(public_path('storage/uploads'))) {
            mkdir(public_path('storage/uploads'), 0777);
            mkdir(public_path('storage/uploads/thumb'), 0777);
        }
        if($folder){
            if (!file_exists(public_path('storage/uploads/'.$folder))) {
                mkdir(public_path('storage/uploads/'.$folder), 0777,true);
            }
        }

        $finalRequest = $request;

        foreach ($request->all() as $key => $value) {

            if ($request->hasFile($key)) {
                if ($key == $downloadable_file_input) {
                    foreach ($request->file($key) as $item) {
                        if($model){
                            $file = $model->mediaFiles;
                            if ($file){
                                if (File::exists(public_path('/storage/uploads/' . $file->file_name))) {
                                    File::delete(public_path('/storage/uploads/' . $file->file_name));
                                }
                                $file->delete();
                            }
                        }

                        $extension = array_last(explode('.', $item->getClientOriginalName()));
                        $filename = md5(time()) . '.' . $extension; // a unique file name
                        $size = $item->getSize() / 1024;
                        $item->move(public_path('storage/uploads/'.$folder), $filename);
                        Media::create([
                            'model_type' => $model_type,
                            'model_id' => $model->id,
                            'name' => $filename,
                            'url' => asset('storage/uploads/'.$folder.'/' . $filename),
                            'type' => $item->getClientMimeType(),
                            'file_name' => $folder.'/'.$filename,
                            'size' => $size,
                        ]);
                    }
                    $finalRequest = $finalRequest = new Request($request->except($downloadable_file_input));


                } else {
                    if ($key != 'video_file') {
                        if ($key == 'add_pdf') {
                            $file = $request->file($key);

                            $extension = array_last(explode('.', $request->file($key)->getClientOriginalName()));
                            $name = array_first(explode('.', $request->file($key)->getClientOriginalName()));
                            $filename = time() . '-' . str_slug($name) . '.' . $extension;

                            $size = $file->getSize() / 1024;
                            $file->move(public_path('storage/uploads'), $filename);
                            Media::create([
                                'model_type' => $model_type,
                                'model_id' => $model->id,
                                'name' => $filename,
                                'url' => asset('storage/uploads/' . $filename),
                                'type' => 'lesson_pdf',
                                'file_name' => $filename,
                                'size' => $size,
                            ]);
                            $finalRequest = new Request(array_merge($finalRequest->all(), [$key => $filename]));
                        } elseif ($key == 'add_audio') {
                            $file = $request->file($key);

                            $extension = array_last(explode('.', $request->file($key)->getClientOriginalName()));
                            $filename = md5(time()) . '.' . $extension; // a unique file name
                            $size = $file->getSize() / 1024;
                            $file->move(public_path('storage/uploads'), $filename);
                            Media::create([
                                'model_type' => $model_type,
                                'model_id' => $model->id,
                                'name' => $filename,
                                'type' => 'lesson_audio',
                                'file_name' => $filename,
                                'url' => asset('storage/uploads/' . $filename),
                                'size' => $size,
                            ]);
                            $finalRequest = new Request(array_merge($finalRequest->all(), [$key => $filename]));
                        } else {
                            $extension = array_last(explode('.', $request->file($key)->getClientOriginalName()));
                            $filename = md5(time()) . '.' . $extension; // a unique file name
                            $request->file($key)->move(public_path('storage/uploads'), $filename);
                            $finalRequest = new Request(array_merge($finalRequest->all(), [$key => $filename]));
                            $model->lesson_image = $filename;
                            $model->save();
                        }

                    }
                }
            }
        }

        return $finalRequest;
    }

    public function saveLogos(Request $request)
    {
        if (!file_exists(public_path('storage/logos'))) {
            mkdir(public_path('storage/logos'), 0777);
        }
        $finalRequest = $request;

        foreach ($request->all() as $key => $value) {
            if ($request->hasFile($key)) {
                $extension = array_last(explode('.', $request->file($key)->getClientOriginalName()));
                $filename = md5(time()) . '.' . $extension; // a unique file name
                $request->file($key)->move(public_path('storage/logos'), $filename);
                $finalRequest = new Request(array_merge($finalRequest->all(), [$key => $filename]));

            }
        }

        return $finalRequest;
    }


    public function saveLessonsFile(Request $request,$model_type){

        $receiver = new FileReceiver('lessons_file', $request, HandlerFactory::classFromRequest($request));
        if (!$receiver->isUploaded()) {
            throw new UploadMissingFileException();
        }
        $fileReceived = $receiver->receive(); // receive file
        if ($fileReceived->isFinished()) { // file uploading is complete / all chunks are uploaded
            $file = $fileReceived->getFile(); // get file
            $extension = $file->getClientOriginalExtension();
            $fileName = md5(time()) . '.' . $extension; // a unique file name

            $disk = Storage::disk('uploads');
            $path = $disk->putFileAs('files', $file, $fileName);
            $size = $file->getSize() / 1024;

            $media = Media::create([
                'model_type' => $model_type,
                'name' => $fileName,
                'url' => asset('storage/uploads/' . $path),
                'type' => $file->getClientMimeType(),
                'file_name' => $path,
                'size' => $size,
            ]);

            // delete chunked file
            unlink($file->getPathname());
            return  $media;
        }
        $handler = $fileReceived->handler();
        return [
            'done' => $handler->getPercentageDone(),
            'status' => true
        ];
    }
}