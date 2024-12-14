<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barangay; // Import the Barangay model
use Illuminate\Support\Collection; 
use ProtoneMedia\Splade\AbstractTable; 
use ProtoneMedia\Splade\SpladeTable; 
use Spatie\QueryBuilder\QueryBuilder; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use ProtoneMedia\Splade\Facades\Toast; 
use App\Http\Requests\BarangayUpdateRequest;
use Spatie\QueryBuilder\AllowedFilter;  

class BarangayController extends Controller
{
    // Display the form to add a new barangay
    public function create(Barangay $barangay)
    {
        return view('admin.barangays.create');
    } 
    
   public function edit(Barangay $barangay)
   {

       return view('admin.barangays.index', compact('barangay'));
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

        
       $barangays = QueryBuilder::for(Barangay::class)
       ->defaultSort('-updated_at')
       ->allowedSorts(['id', 'name', 'updated_at'])
       ->allowedFilters(['id', 'name', $globalSearch]);
   
   return view('admin.barangays.index', [
       'barangays' => SpladeTable::for($barangays)
           ->withGlobalSearch(columns: ['id', 'name'])
           ->column('id', sortable: true) 
           ->column('name', sortable: true)
           ->column('updated_at', sortable: true)
           ->column('action')
           ->paginate(15)

   ]); 
   } 
   

   public function update(Request $request, Barangay $barangay)
   {
       // Validate the request data as needed
       $request->validate([
           'name' => 'required|string|max:255',
       ]);
       
       $barangay->update($request->all());

       Toast::title('Success')->message('Barangay updated successfully')->success()->rightTop()->autoDismiss(3);

       return Redirect::route('admin.barangays.index')->with('success', 'Barangay updated successfully');
   } 

   // Store a new barangay in the database
   public function store(Request $request) 
   {
       $request->validate([
           'name' => 'required|string|max:255|unique:barangays',
       ]);

       // Create a new barangay in the database
       Barangay::create($request->all());
       
       // return Redirect::back();
       Toast::title('Success')->message('Barangay added successfully.')->success()->rightTop()->autoDismiss(3); 

       // Redirect back to the form with a success message
       return redirect()->route('admin.barangays.index')->with('success', 'Barangay added successfully');
   }  

   public function destroy(Request $request, $barangayId) {
       
       $barangay = Barangay::find($barangayId);


       $barangay->delete(); 

       Toast::title('Success')->message($barangay->name . ' has been deleted.')->success()->rightTop()->autoDismiss(5); 
       // Redirect to the barangay list or another appropriate page after deletion
       return redirect()->route('admin.barangays.index')->with('success', $barangay->name . ' deleted successfully'); 
   }
}