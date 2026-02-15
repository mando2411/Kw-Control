<?php

namespace App\Traits;

trait ImageTrait
{
    public function uploadImage($image,$disk){
        // Get original file name and extension
        $extension = $image->getClientOriginalExtension();

        // Create unique filename
        $filename = time() . '_' . uniqid() . '.' . $extension;

        // Store file in media disk
        $path = $image->storeAs('', $filename, $disk);

        // Create the full URL path for database
        $urlPath = config('app.url') . '/storage/'.$disk.'/' . $filename;

        return $urlPath;
    }
}
