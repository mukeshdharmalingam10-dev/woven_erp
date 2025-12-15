<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class FileUploadHelper
{
    /**
     * Store a file in the given directory using a readable filename pattern:
     * originalName_ddmmyyyyHHMMSS.ext (e.g. ars_04122025134532.docx)
     */
    public static function storeWithOriginalName(UploadedFile $file, string $directory, string $prefix = ''): string
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();

        $base = $originalName !== '' ? $originalName : 'file';
        $sanitizedBase = Str::slug($base, '_');

        if ($prefix !== '') {
            $sanitizedBase = $prefix . '_' . $sanitizedBase;
        }

        $timestamp = now()->format('dmYHis'); // e.g. 04122025134532
        $filename = $sanitizedBase . '_' . $timestamp . '.' . $extension;

        return $file->storeAs($directory, $filename, 'public');
    }
}


