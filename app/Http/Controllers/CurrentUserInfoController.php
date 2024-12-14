<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request; 

use Illuminate\Support\Facades\Auth;


class CurrentUserInfoController extends Controller {
    public function index(Request $request) {
        $user = $request->user();

        return response()->json([
            'user' => $user,
        ]);
    }
}