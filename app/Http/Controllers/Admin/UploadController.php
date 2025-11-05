<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function rte(Request $request)
    {
        // Expected: Query string name/type/size and file in form-data key "fileforphp"
        if (!$request->hasFile('fileforphp')) {
            return response('ERROR:No file uploaded', 200)->header('Content-Type', 'text/plain');
        }

        $file = $request->file('fileforphp');

        if (!$file->isValid()) {
            return response('ERROR:Invalid upload', 200)->header('Content-Type', 'text/plain');
        }

        // Basic validation: allow common image and document types
        $mime = $file->getMimeType() ?: '';
        $allowed = [
            'image/jpeg','image/png','image/gif','image/webp','image/svg+xml',
            'application/pdf','text/plain','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        if (!in_array($mime, $allowed)) {
            return response('ERROR:Unsupported file type', 200)->header('Content-Type', 'text/plain');
        }

        $ext = strtolower($file->getClientOriginalExtension() ?: 'bin');
        $folder = 'uploads/rte/'.date('Y/m/d');
        $name = Str::uuid()->toString().'.'.$ext;

        try {
            $path = $file->storeAs($folder, $name, 'public');
        } catch (\Throwable $e) {
            return response('ERROR:'.$e->getMessage(), 200)->header('Content-Type', 'text/plain');
        }

        $url = asset('storage/'.$path);
        return response('READY:'.$url, 200)->header('Content-Type', 'text/plain');
    }
}

