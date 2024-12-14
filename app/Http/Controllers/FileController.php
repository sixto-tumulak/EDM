<?php

namespace App\Http\Controllers;

use App\Models\Files;
use App\Traits\Upload;
use Illuminate\Http\Request;


class FilesController extends Controller
{
    use Upload;

    public function store(Request $request)
    {

        $file_details = [];

        //check if request has files
        if ($request->hasFile('files')) {
          // loop through each file and upload it

            foreach ($request->file('files') as $key => $file) {
                //Upload to Storage
                $path = $this->UploadFile($file, 'Products');

                //reformat the file details
                array_push($file_details, [
                    'path' => $path,
                ]);
            }

            //add each file details to database
            foreach ($file_details as $key => $value) {
                Files::create($value);
            }
            //clear the file details array
            $file_details = [];
        }
    } 
}