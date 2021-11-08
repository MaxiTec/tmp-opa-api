<?php

namespace App\Http\Traits;
use Illuminate\Support\Facades\Storage;

trait UploadImageTrait {

    public function uploadImage($request, $innerFolder = NULL) {
        $file = $request->file('brand_img'); // Nombre de la imagen en el request
        $fileName = $file->getClientOriginalName();
        $folder = config('filesystems.disks.do.folder');
        $path = $innerFolder ? $folder . '/' . $innerFolder : $folder;
        // return $folder;
        $storagePath = Storage::disk('do')->putFile("{$path}",$file,'public'); //tipo de guardado Público

        if($storagePath) {
            return $file->hashName();
        }
        return false;
    }

    public function deleteImage($fileName, $innerFolder = NULL) {
        $folder = config('filesystems.disks.do.folder');
        $path = $innerFolder ? $folder . '/' . $innerFolder : $folder;
        Storage::disk('do')->delete("{$path}/{$fileName}");
        return response()->json(['message' => 'File deleted'], 200);
    }
}