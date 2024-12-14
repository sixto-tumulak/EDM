<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Redirect;
use ProtoneMedia\Splade\Facades\Toast; 
use Spatie\QueryBuilder\AllowedFilter;  
use Illuminate\Support\Collection; 
use ProtoneMedia\Splade\AbstractTable; 
use ProtoneMedia\Splade\SpladeTable; 
use Spatie\QueryBuilder\QueryBuilder; 
use ProtoneMedia\Splade\SpladeQueryBuilder; 
use App\Models\User; 
use App\Models\Barangay; 


class UserController extends Controller
{
    public function index(Request $request) {
        $user =  $request->user(); 
        $user_id = $user->id; 
         
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                Collection::wrap($value)->each(function ($value) use ($query) {
                    $query
                        ->orWhere('id', 'LIKE', "%{$value}%")
                        ->orWhere('name', 'LIKE', "%{$value}%")
                        ->orWhere('email', 'LIKE', "%{$value}%");
                });
            });
        });

         
        $users = QueryBuilder::for(User::class)
        ->defaultSort('-updated_at')
        ->allowedSorts(['id', 'name', 'updated_at'])
        ->allowedFilters(['id', 'name', 'email', $globalSearch]);
    
        return view('admin.users.index', [
            'users' => SpladeTable::for($users)
                ->withGlobalSearch(columns: ['id', 'name'])
                ->column('id', sortable: true) 
                ->column('name', sortable: true)
                ->column('short_name', sortable: true)
                ->column('email', sortable: true)
                ->column('role_id', sortable: true)
                ->column('updated_at', sortable: true)
                ->paginate(15)
                ->perPageOptions([15, 50, 100]), 
            'user' => $user, 
            'address' => Barangay::all(), 
        ]); 
    }   
}