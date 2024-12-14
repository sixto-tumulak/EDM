<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use ProtoneMedia\Splade\Facades\Toast;
use ProtoneMedia\Splade\Facades\SEO;
use ProtoneMedia\Splade\FileUploads\ExistingFile;
use Illuminate\Support\Facades\Storage; 
use Spatie\QueryBuilder\QueryBuilder; 
use App\Models\Job;

class ProfileFeedController extends Controller
{
    public function feedsAPi(Request $request)
    {

        $user =  $request->user(); 
        $user_id = $user->id; 

      
        $jobs = QueryBuilder::for(Job::class)
            ->defaultSort('-updated_at')
            ->allowedSorts(['id', 'updated_at'])
            ->allowedFilters(['id', 'title'])
            ->where('tree', 'like', '%'.request()->get('tree').'%') 
            ->where('address', 'like', '%'.request()->get('address').'%') 
            ->where('address', $user->address)
            ->where('stocks', '>=', 1)  
            ->with(['tree', 'user_id', 'address', 'job_takers'])
            ->paginate(9000);

        return response()->json([
            'feed' => $jobs,
        ]);

        return response()->json([
            'user_address' => $user->address
        ]);
    }

    public function index(Request $request) {
        $user =  $request->user(); 
        $user_id = $user->id; 
        return view('profile.index', [ 
            'user' => $user
        ]); 
    }
}