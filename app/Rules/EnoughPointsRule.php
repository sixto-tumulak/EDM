<?php


namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\UserPoint;
use App\Models\Tree;

class EnoughPointsRule implements Rule
{
    protected $user_id;
    protected $point_value; 

    public function __construct($user_id, $point_value)
    {
        $this->user_id = $user_id;
        $this->point_value = $point_value; 
    }

    public function passes($attribute, $point_value)
    {
        // Check if the user has enough points
        $userPoints = UserPoint::where('user_id', '=', $this->user_id)
        ->value('points');
        return $userPoints;
    }

    public function message()
    {
        return 'You do not have enough points to perform this transaction.';
    }
}
