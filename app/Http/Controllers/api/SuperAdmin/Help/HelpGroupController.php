<?php

namespace App\Http\Controllers\api\SuperAdmin\Help;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\HelpGroup;

class HelpGroupController extends Controller
{
    public function __construct(private HelpGroup $help_group) {}

    public function view()
    {
        $help_groups = $this->help_group 
            ->get();

        return response()->json([
            'help_groups' => $help_groups,
        ]);
    }

    public function show($id)
    {
        $help_group = $this->help_group 
            ->where('id', $id)
            ->first();

        if (empty($help_group)) {
            return response()->json(['errors' => 'help group not found'], 404);
        }

        return response()->json([
            'help_group' => $help_group,
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

        $this->help_group->where('id', $id)->update(['status' => $request->status]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned'
        ]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'    => 'required|array',
            'name.en' => 'required|string',
            'name.ar' => 'required|string',
            'status'  => 'required|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $help_group = $this->help_group->create([
            'name'   => $request->name,
            'status' => $request->status,
        ]); 

        return response()->json(['success' => 'You add data success']);
    }

    public function modify(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'    => 'required|array',
            'name.en' => 'required|string',
            'name.ar' => 'required|string',
            'status'  => 'required|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $help_group = $this->help_group->where('id', $id)->first();

        if (empty($help_group)) {
            return response()->json(['errors' => 'help group not found'], 404);
        }

        $help_group->update([
            'name'   => $request->name,
            'status' => $request->status,
        ]); 

        return response()->json(['success' => 'You update data success']);
    }

    public function delete($id)
    {
        $help_group = $this->help_group->where('id', $id)->first();

        if (empty($help_group)) {
            return response()->json(['errors' => 'help group not found'], 404);
        } 

        return response()->json(['success' => 'You delete data success']);
    }
}
