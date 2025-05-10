<?php

namespace App\Http\Controllers\api\User\ProblemReport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\trait\image;

use App\Models\ProblemReport as ProblemsReport;

class ProblemReportController extends Controller
{
    public function __construct(private ProblemsReport $problem_report){}
    use image;
    
    public function add_report(Request $request){
        $validator = Validator::make($request->all(), [
            'google_map' => 'required',
            'description' => 'required',
            'image' => 'required',
            'village_id' => 'required|exists:villages,id',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'error' => $firstError,
            ],400);
        }
        $reportRequest = $validator->validated();
        if ($request->has('image')) {
            $image_path =$this->upload($request, 'image', '/images/problem_report');
            $reportRequest['image'] = $image_path;
        }
        $reportRequest['user_id'] = $request->user()->id;
        $this->problem_report
        ->create($reportRequest);

        return response()->json([
            'success' => 'You add data success'
        ]);
    }
}
