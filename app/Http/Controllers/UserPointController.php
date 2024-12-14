<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserPoint;   
use ProtoneMedia\Splade\Facades\Toast; 
use App\Rules\EnoughPointsRule; 
use Illuminate\Http\Response; 

class UserPointController extends Controller
{
   
    public function addPoints($points, $user_id) {
        // Try to find a record with the given user_id
        $userPoint = UserPoint::where('user_id', $user_id)->first();

        // If a record doesn't exist, create a new one
        if (!$userPoint) {
            UserPoint::create([
                'user_id' => $user_id,
                'points' => $points,
            ]);
        } else {
            // If a record exists, update the points
            $userPoint->update([
                'points' => $userPoint->points + $points,
            ]);
        }

        Toast::title('Success')->message('Points added successfully!')->success()->rightTop()->autoDismiss(5);
        return back(); 
    }

    public function subtractPoints($points, $user_id) {
        // Try to find a record with the given user_id
        $userPoint = UserPoint::where('user_id', $user_id)->first();

        if(!$userPoint || $userPoint->points < $points) {
            Toast::title('Warning')->message("You don't have enough points to complete this transaction.")->warning()->rightTop()->autoDismiss(5);
            return false;
        }else {
            $userPoint->update([
                'points' => $userPoint->points - $points,
            ]);

            Toast::title('Success')->message('Points deducted successfully!')->success()->rightTop()->autoDismiss(5);
            return true; 
        } 
    }
}
