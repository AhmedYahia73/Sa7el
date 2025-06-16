<?php

namespace App\Http\Controllers\api\ServiceProvider\Video;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\TraitImage;

use App\Models\ProviderVideos;

class VideoController extends Controller
{
    public function __construct(private ProviderVideos $provider_video){}
    use TraitImage;

    public function view(Request $request){
        $provider_video = $this->provider_video
        ->where('provider_id', $request->user()->provider_id)
        ->get(); 

        return response()->json([
            'provider_video' => $provider_video, 
        ]);
    }

    public function status(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'status' => 'required|boolean',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $this->provider_video
        ->where('id', $id)
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned'
        ]);
    }

    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'video' => 'required',
            'status' => 'required|boolean',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $video_path = $this->upload_file($request, 'video', 'provider/video/provider_video');
        $this->provider_video
        ->create([
            'description' => $request->description ?? null,
            'video' => $video_path,
            'status' => $request->status,
            'provider_id' => $request->user()->provider_id,
        ]);

        return response()->json([
            'success' => 'You add video success'
        ]);
    }

    public function modify(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'video' => 'required',
            'status' => 'required|boolean',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $provider_video = $this->provider_video
        ->where('id', $id)
        ->where('provider_id', $request->user()->provider_id)
        ->first();
        if (empty($provider_video)) {
            return response()->json([
                'errors' => 'provider not found'
            ], 400);
        }
        $video_path = $this->update_file($request, $provider_video->video, 'video', 'provider/video/provider_video');

        $provider_video->update([
            'description' => $request->description ?? null,
            'video' => $video_path,
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => 'You update video success'
        ]);
    }

    public function delete($id){
        $provider_video = $this->provider_video
        ->where('id', $id)
        ->first();
        if (empty($provider_video)) {
            return response()->json([
                'errors' => 'provider not found'
            ], 400);
        }
        $this->deleteImage($provider_video->video);
        $provider_video->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
