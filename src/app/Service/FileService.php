<?php

namespace App\Service\WebooshCore;

use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use weboosh\webooshcore\app\Util\WebooshShortPixel;


class FileService{
    public static function UploadFile($uploadedFile){
        if (empty($uploadedFile)) return '';

        $originalName = $uploadedFile->getClientOriginalName();
        $extension = $uploadedFile->getClientOriginalExtension();
        $originalNameWithoutExt = substr($originalName, 0, strlen($originalName) - strlen($extension) - 1);

        if($originalName == 'blob'){
            $originalName = 'file.pdf';
            $extension = 'pdf';
            $originalNameWithoutExt = 'file';
        }

        $filename = self::sanitize($originalNameWithoutExt);
        $allowed_filename = self::createUniqueFilename( $filename, $extension );

        $uploadSuccess = static::Save( $uploadedFile, $allowed_filename );

        if( !$uploadSuccess ) {
            return false;
        }

        return $allowed_filename;

    }

    public static function delete( $filename ){
        $full_path = public_path(env('UPLOAD_FILE')) . $filename;
        if ( File::exists( $full_path ) ){
            File::delete( $full_path );
        }
    }

    private static function createUniqueFilename( $filename, $extension )
    {
        $full_path = public_path(env('UPLOAD_FILE')) . $filename . '.' . $extension;

        if ( File::exists( $full_path ) ){
            // Generate token for image
            $imageToken = substr(sha1(mt_rand()), 0, 5);
            return $filename . '-' . $imageToken . '.' . $extension;
        }

        return $filename . '.' . $extension;
    }

    public static function Save( $uploadedFile, $allowed_filename, $path = null ){
        try{
            if (empty($path)) {
                $path = public_path(env('UPLOAD_FILE')) ;
            }

            $full_path = $path . $allowed_filename;

            if(!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            File::move($uploadedFile->path(), $full_path);
            chmod($full_path, 0777);

            if (!empty(env('SHORT_PIXEL_API_KEY', null))) {
                $shortPixel = new VodeaShortPixel();
                $shortPixel->fromFiles($full_path, $path);
            }
        } catch (\Exception $e){
            \Log::error($e);
            return false;
        }
        return true;
    }

    private static function sanitize($string, $force_lowercase = true, $anal = false){
        $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
            "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
            "â€”", "â€“", ",", "<", ".", ">", "/", "?");
        $clean = trim(str_replace($strip, "", strip_tags($string)));
        $clean = preg_replace('/\s+/', "-", $clean);
        $clean = ($anal) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean ;

        return ($force_lowercase) ?
            (function_exists('mb_strtolower')) ?
                mb_strtolower($clean, 'UTF-8') :
                strtolower($clean) :
            $clean;
    }
}
