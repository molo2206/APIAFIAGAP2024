<?php

namespace App\Http\Controllers\utils;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class MethodsController extends Controller
{
    public static function uploadImage($field, $destination)
    {
        if ($field) {
            $image = Image::make($field);
            $png_url = time() . ".png";
            $width = $image->width();
            $height = $image->height();
            $image->resize($width / 2, $height / 2); // Redimensionnement de l'image à 120 x 80 px
            $image->save(public_path() . $destination . $png_url);
            return $png_url;
        }
    }

    public static function removeImageUrl($url)
    {
        $path = str_replace(env('APP_URL'), "", $url);
        if (File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }

    public static function removeMultipleImage($field, $destination)
    {
        $data = json_decode($field);
        foreach ($data as $file) {
            if (File::exists(public_path($destination . $file))) {
                File::delete(public_path($destination . $file));
            }
        }
    }

    public static function uploadMultipleImage($field, $destination)
    {
        $images = [];
        if ($field) {
            foreach ($field as $file) {
                $image = Image::make($file);
                $png_url = md5(rand(1000, 10000)) . ".png";
                $width = $image->width();
                $height = $image->height();
                //$image->resize($width / 2, $height / 2); // Redimensionnement de l'image à 120 x 80 px
                $image->save(public_path() . $destination . $png_url);
                array_push($images, $png_url);
            }
        }

        return $images;
    }
}
