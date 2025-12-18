<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UploadFileController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:5120',
            'path' => 'required|string'
        ]);

        $file = $request->file('file');
        $path = $request->input('path');

        $filename = Str::random(25) . '.' . $file->getClientOriginalExtension();
        $file->move(public_path($path), $filename);

        return response()->json([
            "success" => true,
            "message" => "File uploaded successfully",
            "data" => [
                "filename" => $filename,
                "path" => $path,
                "url" => $path . '/' . $filename
            ]
        ]);
    }
}
