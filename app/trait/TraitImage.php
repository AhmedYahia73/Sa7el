<?php

namespace App\trait;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

trait TraitImage
{
    // This Trait Aboute Image

    public function upload(Request $request,$fileName = 'image',$directory){
        if($request->has($fileName)){// if Request has a Image
            $imageFile = $request->file($fileName);

            $manager = new ImageManager(new GdDriver());
            $img = $manager->read($imageFile->getPathname());

            $img = $img->scale(width: 1920);

            // Save to a temporary file with reduced quality
            $image_path = $directory . '/' . uniqid() . '.jpg';
            Storage::disk('public')->makeDirectory($directory);
            $path = storage_path('app/public/' . $image_path);
            $quality = 90;

            // Try reducing quality until size is under 2MB (1024 KB)
            do {
                $img->save($path, $quality);
                $filesize = filesize($path) / 1024; // in KB
                $quality -= 5;
            } while ($filesize > 1024 && $quality > 10);


            // $uploadImage = new request();
            // $imagePath = $request->file($fileName)->store($directory,'public'); // Take Image from Request And Save inStorage;
            return $image_path;
        }
        return Null;
    }
    
    public function update_image(Request $request, $old_image_path,$fileName = 'image',$directory){
        if($request->has($fileName)){// if Request has a Image
            $imageFile = $request->file($fileName);

            $manager = new ImageManager(new GdDriver());
            $img = $manager->read($imageFile->getPathname());

            $img = $img->scale(width: 1920);

            // Save to a temporary file with reduced quality
            $image_path = $directory . '/' . uniqid() . '.jpg';
            Storage::disk('public')->makeDirectory($directory);
            $path = storage_path('app/public/' . $image_path);
            $quality = 90;

            // Try reducing quality until size is under 2MB (1024 KB)
            do {
                $img->save($path, $quality);
                $filesize = filesize($path) / 1024; // in KB
                $quality -= 5;
            } while ($filesize > 1024 && $quality > 10);

            // $uploadImage = new request();
            // $path = $request->file($fileName)->store($directory,'public'); // Take Image from Request And Save inStorage;
            if ($old_image_path && Storage::disk('public')->exists($old_image_path)) {
                Storage::disk('public')->delete($old_image_path);
            }
            return $image_path;
        }
        return Null;
    }

    public function upload_file(Request $request,$fileName = 'image',$directory){        
        if($request->has($fileName)){// if Request has a Image
            $uploadImage = new request();
            $imagePath = $request->file($fileName)->store($directory,'public'); // Take Image from Request And Save inStorage;
            return $imagePath;
        }
        return Null;
    }
    
    public function update_file(Request $request, $old_image_path,$fileName = 'image',$directory){
        if($request->has($fileName)){// if Request has a Image
            $uploadImage = new request();
            $imagePath = $request->file($fileName)->store($directory,'public'); // Take Image from Request And Save inStorage;
            if ($old_image_path && Storage::disk('public')->exists($old_image_path)) {
                Storage::disk('public')->delete($old_image_path);
            }
            return $imagePath;
        }
        return Null;
    }

    // This to upload file
    public function uploadFile($file, $directory, $file_num = 1) {
        if ($file) {
            $imageFile = $file;

            $manager = new ImageManager(new GdDriver());
            $img = $manager->read($imageFile->getPathname());

            $img = $img->scale(width: 1920);
            Storage::disk('public')->makeDirectory($directory);

            // Save to a temporary file with reduced quality
            $path = storage_path('app/public/' . $directory . '/' . uniqid() . '.jpg');
            $quality = 90;

            // Try reducing quality until size is under 2MB (1024 KB)
            do {
                $img->save($path, $quality);
                $filesize = filesize($path) / 1024; // in KB
                $quality -= 5;
            } while ($filesize > 1024 / $file_num && $quality > 10);
            $filePath = $file->store($directory, 'public');
            return $filePath;
        }
        return null;
    }

    // This Trait Aboute file

    public function upload_array_of_file(Request $request,$fileName = 'image',$directory){
        // Check if the request has an array of files
        if ($request->has($fileName)) {
            $uploadedPaths = []; // Array to store the paths of uploaded files
    
            // Loop through each file in the array
            foreach ($request->file($fileName) as $file) {
                // Store each file in the specified directory
                $imagePath = $file->store($directory, 'public');
                $uploadedPaths[] = $imagePath;
            }
    
            return $uploadedPaths; // Return an array of uploaded file paths
        }
    
        return null;
    }
    
    public function deleteImage($imagePath){
        // Check if the file exists
        try {
            if ($imagePath && !empty($imagePath) && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    

    public function storeBase64Image($base64Image, $folderPath = 'admin/manuel/receipt'){

        // Validate if the base64 string has a valid image MIME type
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
            // Extract the image MIME type
            $imageType = $type[1]; // e.g., 'jpeg', 'png', 'gif', etc.

            // Extract the actual base64 encoded data (remove the data URL part)
            $imageData = substr($base64Image, strpos($base64Image, ',') + 1);
            $imageData = base64_decode($imageData);

            // low storage
            $manager = new ImageManager(new GdDriver());
            $image = $manager->read($imageData);
            $quality = 90;
            $minQuality = 10;
            $width = $image->width();
            $minWidth = 800;
            $step = 200;
            $maxSizeKB = 1024;

            // Generate a unique file name with the appropriate extension
            $tmpPath = storage_path('app/tmp_' . uniqid() . '.jpg');
            do {
                $resized = $image->resize($width, null);
                $resized->save($tmpPath, $quality);

                $filesize = filesize($tmpPath) / 1024;

                if ($filesize > $maxSizeKB) {
                    $quality -= 10;
                    if ($quality < $minQuality && $width > $minWidth) {
                        $width -= $step;
                        $quality = 90;
                    }
                }
            } while ($filesize > $maxSizeKB && $quality >= $minQuality && $width >= $minWidth);
            $fileName = uniqid() . '.' . $imageType;
            Storage::disk('public')->makeDirectory($folderPath);
            $finalPath = $folderPath . '/' . $fileName;
            Storage::disk('public')->put($finalPath, file_get_contents($tmpPath));
            // Define the folder path in storage


            @unlink($tmpPath);

            // Return the image path
            return $finalPath;
        }

        return response()->json(['error' => 'Invalid base64 image string'], 400);
    }
}
