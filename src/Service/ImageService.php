<?php

namespace App\Service;

use Intervention\Image\ImageManager;

class ImageService
{
    public function savePostImage($image, String $link)
    {
        $manager = new ImageManager(['driver' => 'imagick']);
        $img = $manager->make($image);
        $img->resize(800, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save($link, 75, 'jpg');
    }
}