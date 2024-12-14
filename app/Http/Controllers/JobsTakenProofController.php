<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobsTakenProof; 
use Illuminate\Support\Facades\Storage; 
use ProtoneMedia\Splade\FileUploads\HandleSpladeFileUploads;  
use ProtoneMedia\Splade\FileUploads\ExistingFile; 

use App\Models\VolunteerJobsTaken;   

class JobsTakenProofController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'files.*' => 'required', 
        ]);

        $transaction = VolunteerJobsTaken::find(request('transactionId'));

        

        $paths = [];

        foreach ($request->file('files') as $file) {

            $path = $file->store('public/jobstakenproofs');

            // $path = $file->store('uploads'); 
            $paths[] = $path;
            

            JobsTakenProof::create([
                'transaction_id' => request('transactionId'), 
                'proof' => $path, 
            ]);  


        }

        $transaction->update([
            'status' => 'reviewing', 
        ]);

        return response()->json(['paths' => $paths, 'transaction_id' => request('transactionId'), 'transaction' => $transaction]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
