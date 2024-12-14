<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactFormEntry;
use ProtoneMedia\Splade\Facades\Toast; 
use Illuminate\Support\Facades\Redirect; 
use Spatie\QueryBuilder\AllowedFilter;  
use ProtoneMedia\Splade\AbstractTable; 
use ProtoneMedia\Splade\SpladeTable; 
use ProtoneMedia\Splade\Facades\Splade; 
use Spatie\QueryBuilder\QueryBuilder; 
use Illuminate\Support\Collection; 

class ContactFormController extends Controller
{
    public function index()
    {
        // Fetch all contact form entries
        // $entries = ContactFormEntry::all();
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                Collection::wrap($value)->each(function ($value) use ($query) {
                    $query
                        ->orWhere('id', 'LIKE', "%{$value}%")
                        ->orWhere('name', 'LIKE', "%{$value}%")
                        ->orWhere('email', 'LIKE', "%{$value}%")
                        ->orWhere('message', 'LIKE', "%{$value}%"); 
                        // ->orWhere('title', 'LIKE', "%{$value}%");
                });
            });
        });

        // $trees = Tree::all(); 

        $entries = QueryBuilder::for(ContactFormEntry::class)
        ->defaultSort('-updated_at')
        ->allowedSorts(['id', 'updated_at'])
        ->allowedFilters(['id', 'name', $globalSearch]);
    
        return view('admin.contact.index', [
            'entries' => SpladeTable::for($entries)
                ->withGlobalSearch(columns: ['id', 'name'])
                ->column('id', sortable: true) 
                ->column('name')
                ->column('message')
                ->column('email')
                ->column('updated_at', sortable: true)
                ->paginate(15)
                ->perPageOptions([15, 50, 100])
        ]); 
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required',
        ]);

        ContactFormEntry::create($request->all());
        
        Toast::title('Success')->message("Your submission has been received. We will get back to you shortly.")->success()->rightTop()->autoDismiss(5); 
        return Redirect::back();
        // return Redirect::route('pages.contact');
    }
}