<?php

namespace App\Http\Controllers\api\SuperAdmin\Help;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\TraitImage;

use App\Models\HelpVideo;
use App\Models\HelpGroup;

class HelpVideoController extends Controller
{
    use TraitImage;

    public function __construct(private HelpVideo $help_video) {}

    public function view(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            "help_group_id" => "sometimes|exists:help_groups,id",
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        $help_videos = $this->help_video->with("group:id,name")
        ->when($request->filled('help_group_id'), function ($query) use ($request) {
            $query->where('help_group_id', $request->help_group_id);
        })
        ->when($request->filled('search'), function ($query) use ($request) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        })
        ->latest()
        ->paginate($request->get('per_page', 10)); // الافتراضي 10 عناصر في الصفحة

        return response()->json([
            'help_videos' => $help_videos,
        ]);
    }
    
    public function lists()
    {
        $help_groups = HelpGroup::
        select("id", "name")->get();

        return response()->json([
            'help_groups' => $help_groups,
        ]);
    }

    public function status(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $this->help_video->where('id', $id)->update(['status' => $request->status]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned'
        ]);
    }

    public function show($id)
    {
        $help_video = $this->help_video->where('id', $id)->first();

        if (empty($help_video)) {
            return response()->json(['errors' => 'help video not found'], 404);
        }

        return response()->json([
            'help_video' => $help_video,
        ]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required|array',
            'name.en'       => 'required|string',
            'name.ar'       => 'required|string',
            'description'   => 'required|array',
            'description.en'=> 'required|string',
            'description.ar'=> 'required|string',
            'ar_video'      => 'required|file',
            'en_video'      => 'required|file',
            "help_group_id" => "required|exists:help_groups,id",
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $ar_video = $this->upload_file($request, 'ar_video', 'videos/help');
        $en_video = $this->upload_file($request, 'en_video', 'videos/help');

        $this->help_video->create([
            'name'        => $request->name,
            'description' => $request->description,
            'ar_video'    => $ar_video,
            'en_video'    => $en_video,
            "help_group_id" => $request->help_group_id,
        ]);

        return response()->json(['success' => 'You add data success']);
    }

    public function modify(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required|array',
            'name.en'       => 'required|string',
            'name.ar'       => 'required|string',
            'description'   => 'required|array',
            'description.en'=> 'required|string',
            'description.ar'=> 'required|string',
            'ar_video'      => 'nullable|file',
            'en_video'      => 'nullable|file',
            "help_group_id" => "required|exists:help_groups,id",
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $help_video = $this->help_video->where('id', $id)->first();

        if (empty($help_video)) {
            return response()->json(['errors' => 'help video not found'], 404);
        }

        $data = [
            'name'        => $request->name,
            'description' => $request->description,
            "help_group_id" => $request->help_group_id,
        ];

        if ($request->hasFile('ar_video')) {
            $data['ar_video'] = $this->update_file($request, $help_video->ar_video, 'ar_video', 'videos/help');
        }

        if ($request->hasFile('en_video')) {
            $data['en_video'] = $this->update_file($request, $help_video->en_video, 'en_video', 'videos/help');
        }

        $help_video->update($data);

        return response()->json(['success' => 'You update data success']);
    }

    public function delete($id)
    {
        $help_video = $this->help_video->where('id', $id)->first();

        if (empty($help_video)) {
            return response()->json(['errors' => 'help video not found'], 404);
        }

        $this->deleteImage($help_video->ar_video);
        $this->deleteImage($help_video->en_video);
        $help_video->delete();

        return response()->json(['success' => 'You delete data success']);
    }
}
