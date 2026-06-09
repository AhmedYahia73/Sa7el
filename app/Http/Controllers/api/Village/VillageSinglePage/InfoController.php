<?php

namespace App\Http\Controllers\api\Village\VillageSinglePage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Village;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

class InfoController extends Controller
{
    public function __construct(private Village $village){}

    public function view(Request $request){
        $village = $this->village
        ->where('id', $request->user()->village_id)
        ->with('zone')
        ->withCount(['units', 'population'])
        ->first();

        return response()->json([
            'village' => $village
        ]);
    }

    public function online_users(Request $request){
        $village_id = $request->user()->village_id;

        $user_ids = PersonalAccessToken::where('tokenable_type', User::class)
            ->pluck('tokenable_id');

        $users = User::whereIn('id', $user_ids)
            ->whereHas('appartment_code', function($q) use ($village_id) {
                $q->where('village_id', $village_id);
            })
            ->where('role', 'user')
            ->when($request->search, fn($q) => $q->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            }))
            ->paginate($request->get('per_page', 20));

        return response()->json($users);
    }

    public function logout_user(Request $request, $id){
        $village_id = $request->user()->village_id;

        $user = User::where('id', $id)
            ->whereHas('appartment_code', function($q) use ($village_id) {
                $q->where('village_id', $village_id);
            })
            ->where('role', 'user')
            ->first();

        if (!$user) {
            return response()->json(['errors' => 'user not found'], 404);
        }

        $user->tokens()->delete();

        return response()->json(['success' => 'user logged out successfully']);
    }
}
