<?php

namespace App\Http\Controllers;

use App\Tables\Trees;
use Illuminate\Http\Request;


use App\Models\Tree;
use Illuminate\Support\Collection; 
use ProtoneMedia\Splade\AbstractTable; 
use ProtoneMedia\Splade\SpladeTable; 
use Spatie\QueryBuilder\QueryBuilder; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use ProtoneMedia\Splade\Facades\Toast; 
use App\Http\Requests\TreeUpdateRequest;
use Spatie\QueryBuilder\AllowedFilter; 

use Session; 

class TreeController extends Controller
{   

    public function treesApi(Request $request, Tree $tree) {
        if ($request->method() === 'GET') {
            $trees = Tree::all();

            return response()->json([
                'trees' => $trees,
            ]);
        }       
    } 
     // Display the form to add a new tree
     public function create(Tree $tree)
     {
         return view('admin.trees.create');
     } 

    public function edit(Tree $tree)
    {

        return view('admin.trees.edit', compact('tree'));
    }


    public function index(Request $request) {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                Collection::wrap($value)->each(function ($value) use ($query) {
                    $query
                        ->orWhere('id', 'LIKE', "%{$value}%")
                        ->orWhere('name', 'LIKE', "%{$value}%");
                });
            });
        });

         
        // $trees = QueryBuilder::for(Tree::class)
        // ->defaultSort('-updated_at')
        // ->allowedSorts(['id', 'name', 'updated_at'])
        // ->allowedFilters(['id', 'name', $globalSearch]);
    
        // return view('admin.trees.index', [
        //     'trees' => SpladeTable::for($trees)
        //         ->withGlobalSearch(columns: ['id', 'name'])
        //         ->column('id', sortable: true) 
        //         ->column('name', sortable: true)
        //         ->column('tree_value')
        //         ->column('updated_at', sortable: true, as: fn($value) => \Carbon\Carbon::parse($value)->format('F j, y h:i a'))
        //         ->column('action')
        //         ->paginate(15)
        //         ->perPageOptions([15, 50, 100])
        // ]); 
        
        return view('admin.trees.index', [
            'trees' => Trees::class
        ]); 
    } 

    public function configure(SpladeTable $table) {
        $table->withGlobalSearch(columns: ['id', 'name'])
        ->column('id', sortable: true) 
        ->column('name', sortable: true)
        ->column('tree_value')
        ->column('updated_at', sortable: true)
        ->column('action')
        ->export()
        ->paginate(15)
        ->bulkAction(
            label: 'Delete trees',
            each: fn (Tree $tree) => $projetreect->delete(),
            confirm: true,
        );   
    }
    

    public function update(Request $request, Tree $tree)
    {
        // Validate the request data as needed
        $request->validate([
            'name' => 'required|string|max:255',
            'tree_value' => 'required|numeric|min:1|max:100', 
        ]);
        
        $tree->update($request->all());

        Toast::title('Success')->message('Tree updated successfully')->success()->rightTop()->autoDismiss(3);

        return Redirect::route('admin.trees.index')->with('success', 'Tree updated successfully');
    } 

    // Store a new tree in the database
    public function store(Request $request) 
    {
        $request->validate([
            'name' => 'required|string|unique:trees|max:255',
            'tree_value' => 'required|numeric|min:1||max:100',
        ]);

        // Create a new tree in the database
        Tree::create($request->all());
        
        // return Redirect::back();
        Toast::title('Success')->message('Tree added successfully.')->success()->rightTop()->autoDismiss(3); 

        // Redirect back to the form with a success message
        return redirect()->route('admin.trees.index')->with('success', 'Tree added successfully');
    }  

    public function destroy(Request $request, $treeId) {
        
        $tree = Tree::find($treeId);

        // if(!$tree) {
        //     Toast::title('Error!')->message('Tree cannot be deleted.')->error()->rightTop()->autoDismiss(3); 
        // }

        $tree->delete(); 

        Toast::title('Success')->message($tree->name . ' has been deleted.')->success()->rightTop()->autoDismiss(5); 
        // Redirect to the tree list or another appropriate page after deletion
        return redirect()->route('admin.trees.index')->with('success', $tree->name . ' deleted successfully'); 
    }
    
}
 