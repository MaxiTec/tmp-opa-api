<?php

namespace App\Http\Controllers;
use App\Request\ImagePostRequest;

class DOSpacesController extends Controller
{
    // We use a form Request
    public function store(ImagePostRequest $request)
    {
        $file = $request->asFile('image'); // Nombre de la imagen en el request
        $fileName = (string) Str::uuid(); // si se necesita
        $folder = config('filesystems.disks.do.folder');
        return $folder;
        Storage::disk('do')->putFile(
            "{$folder}",
            file_get_contents($file),
            'public' //tipo de guardado Público
        );

        return response()->json(['message' => 'File uploaded'], 200);
    }
}
