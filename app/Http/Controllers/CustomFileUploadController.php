<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class CustomFileUploadController
{
    public function handle(Request $request)
    {
        // Skip signature validation - just validate the user is authenticated
        if (!auth()->check()) {
            abort(401, 'Unauthenticated');
        }

        $disk = FileUploadConfiguration::disk();
        $filePaths = $this->validateAndStore($request->file('files'), $disk);

        return ['paths' => $filePaths];
    }

    public function validateAndStore($files, $disk)
    {
        Validator::make(['files' => $files], [
            'files.*' => FileUploadConfiguration::rules()
        ])->validate();

        $fileHashPaths = collect($files)->map(function ($file) use ($disk) {
            $filename = TemporaryUploadedFile::generateHashNameWithOriginalNameEmbedded($file);

            return $file->storeAs('/'.FileUploadConfiguration::path(), $filename, [
                'disk' => $disk
            ]);
        });

        // Strip out the temporary upload directory from the paths.
        return $fileHashPaths->map(function ($path) {
            return str_replace(FileUploadConfiguration::path('/'), '', $path);
        });
    }
}