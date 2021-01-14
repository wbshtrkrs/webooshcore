<?php

namespace App\Service\WebooshCore;

use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use PhpParser\Node\Scalar\String_;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ErrorException;
use Weboosh\WebooshCore\app\Util\WebooshShortPixel;


class ImageService
{
    public static $sizes = [
        'full' => 0,
        'lg' => 1200,
        'md' => 500,
        'sm' => 200,
        'xs' => 100
    ];

    public static function UploadImage($uploadedFile){
        $stdClass = '\stdClass';
        if (empty($uploadedFile)) return '';

        if ($uploadedFile instanceof UploadedFile){
            $originalName = $uploadedFile->getClientOriginalName();
            $extension = $uploadedFile->getClientOriginalExtension();
        } else if ($uploadedFile instanceof $stdClass){
            if (empty($uploadedFile->name)) return '';
            if (filter_var($uploadedFile->url, FILTER_VALIDATE_URL)) return $uploadedFile->name;
            $extensions = explode('.', $uploadedFile->name);
            $extension = @$extensions[count($extensions) - 1];
            $originalName = $uploadedFile->name;
            $uploadedFile = $uploadedFile->url;
        } else if (is_string($uploadedFile)){
            try{
                file_get_contents($uploadedFile);
            } catch (ErrorException $e) {
                return '';
            }
            $extension = \File::extension($uploadedFile);
            $originalName = \File::name($uploadedFile) . '.' . $extension;
        } else {
            return '';
        }
        $originalNameWithoutExt = substr($originalName, 0, strlen($originalName) - strlen($extension) - 1);

        $filename = self::sanitize($originalNameWithoutExt);
        $allowed_filename = self::createUniqueFilename( $filename, $extension );

        $uploadSuccess = true;
        $manager = new ImageManager();
        foreach(self::$sizes as $size=>$ratio) {
            $path = public_path(env('UPLOAD_IMAGE')) . $size . DIRECTORY_SEPARATOR;

            $full_path = $path . $allowed_filename;

            if(!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            if ($ratio == 0){
                $uploadSuccess = FileService::Save($uploadedFile, $allowed_filename, $path);
                if (!$uploadSuccess) {
                    break;
                }
            } else {
                $pathFull = public_path(env('UPLOAD_IMAGE')) . 'full' . DIRECTORY_SEPARATOR . $allowed_filename;

                $image = $manager->make( $pathFull );
                $image->resize($ratio, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $image->save( $full_path );
            }
        }

        if( !$uploadSuccess ) {
            return false;
        }

        return $allowed_filename;

    }

    public static function delete( $filename ){
        if (empty($filename)) return;

        if (is_string($filename)) {
            static::deleteSingle($filename);
        };
        if (is_array($filename)) {
            foreach($filename as $singleFilename){
                static::deleteSingle($singleFilename);
            }
        };
    }

    public static function deleteSingle( $filename ){
        foreach(self::$sizes as $size=>$ratio){
            $full_path = base_path('public/'.env('UPLOAD_IMAGE')) . $size . DIRECTORY_SEPARATOR . $filename;
            if ( File::exists( $full_path ) ){
                File::delete( $full_path );
            }
        }
    }

    private static function createUniqueFilename( $filename, $extension )
    {
        $full_size_dir = 'full';
        $full_image_path = base_path('public/'.env('UPLOAD_IMAGE')) . $full_size_dir . DIRECTORY_SEPARATOR . $filename . '.' . $extension;

        if ( File::exists( $full_image_path ) ){
            // Generate token for image
            $imageToken = substr(sha1(mt_rand()), 0, 5);
            return $filename . '-' . $imageToken . '.' . $extension;
        }

        return $filename . '.' . $extension;
    }

    private static function SaveAll( $uploadedFile, $allowed_filename ){
        $uploadedFilePath = $uploadedFile;
        if ($uploadedFile instanceof UploadedFile){
            $uploadedFilePath = $uploadedFile->path();
        }

        try{
            $manager = new ImageManager();
            foreach(self::$sizes as $size=>$ratio){
                $path = base_path('public/'.env('UPLOAD_IMAGE')) . $size . DIRECTORY_SEPARATOR;
                $full_path = $path . $allowed_filename;

                if(!File::exists($path)) {
                    File::makeDirectory($path, 0777, true, true);
                }

                if ($ratio == 0 && !is_string($uploadedFile) ){
                    File::copy($uploadedFilePath, $full_path);

                } else {
                    $image = $manager->make( $uploadedFile );
                    if ($ratio != 0){
                        $image->resize($ratio, null, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                    }
                    $image->save( $full_path );
                }
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

    public static function SyncImage($oldImages, $newImages){
        if (is_string($oldImages)) $oldImages = [$oldImages];
        if (is_string($newImages)) $newImages = [$newImages];

        $newImagesUpdated = [];
        foreach($oldImages as $oldImage){
            if (empty($oldImage)) continue;
            $existInNew = false;
            foreach($newImages as $newImage){
                if($newImage->name == $oldImage && substr($newImage->url, 0, 4) != 'data') $existInNew = true;
            }

            if(!$existInNew) static::delete($oldImage);
            else $newImagesUpdated[] = $oldImage;
        }

        foreach($newImages as $newImage){
            if (empty($newImage)) continue;
            if(substr($newImage->url, 0, 4) == 'data') $newImagesUpdated[] = static::UploadImage($newImage);
        }
        return $newImagesUpdated;
    }


}
