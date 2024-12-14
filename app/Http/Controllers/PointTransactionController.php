<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;  
use App\Rules\EnoughPointsRule;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Storage; 
use App\Models\PointTransaction;  
use Illuminate\Support\Facades\Redirect;
use ProtoneMedia\Splade\Facades\Toast; 
use Spatie\QueryBuilder\AllowedFilter;  
use Illuminate\Support\Collection; 
use ProtoneMedia\Splade\AbstractTable; 
use ProtoneMedia\Splade\SpladeTable; 
use Spatie\QueryBuilder\QueryBuilder; 
use ProtoneMedia\Splade\SpladeQueryBuilder; 

use App\Http\Controllers\UserPointController;

use ProtoneMedia\Splade\FileUploads\HandleSpladeFileUploads;  




class PointTransactionController extends Controller
{
    protected $userPointController;

    public function __construct(UserPointController $userPointController)
    {
        $this->userPointController = $userPointController;
    }


    public function store(Request $request)
    {
        $user = $request->user();
        $user_id = $user->id; 

        if($user->role_id === 1 || $user->role_id === 2) {
            $this->validate($request, [
                'amount' => 'required|numeric|min:1',
                'proof' => 'required|image|mimes:jpeg,jpg,png,webp', 
                'payment_method' => 'required', 
            ]);

            if ($request->hasFile('proof')) {
                // $path = $request->file('proof')->store('public/point-transactions');
                $path = $request->file('proof')->store('public/point-transactions');
            }

            PointTransaction::create([
                'user_id' => $user_id,
                'amount' => $request->input('amount'), 
                'proof' => $path, 
                'payment_method' => $request->input('payment_method'), 
                'type' => 'topup',
                'status' => 'pending', 
            ]);

            Toast::title('Success')->message('Transaction has been received and pending for approval.')->success()->rightTop()->autoDismiss(5);
    
            return Redirect::route('wallet.index');
        }else {
            $this->validate($request, [
                'amount' => [
                    'required',
                    'numeric',
                    'min:1',
                    new EnoughPointsRule($user_id, $request->input('amount'))
                ],
                'payment_method' => 'required', 
            ]);
            PointTransaction::create([
                'user_id' => $user_id,
                'amount' => $request->input('amount'), 
                'payment_method' => $request->input('payment_method'), 
                'payment_receiver' => $request->input('payment_receiver'),
                'type' => 'withdrawal',
                'status' => 'pending', 
            ]);

            Toast::title('Success')->message('Transaction has been received, please wait atleast 24 hours for us to review your request.')->success()->rightTop()->autoDismiss(5);
    
            return Redirect::route('wallet.index');
        }

    }

    public function approvedTopup($id) {

        $transaction = PointTransaction::where('id', $id)->first();

        $this->userPointController->addPoints($transaction->amount, $transaction->user_id);
         
        PointTransaction::where('id', $id)->update([
            'status' => 'approved',
        ]);

        Toast::title('Success')->message('Transaction has been approved.')->success()->rightTop()->autoDismiss(5);

        return back(); 
        // return redirect()->back()->with('success', 'Transaction approved successfully.');
    }

    public function approvedWithdrawal($id) {

        $transaction = PointTransaction::where('id', $id)->first();

        if($transaction->status == 'pending') {
            $result = $this->userPointController->subtractPoints($transaction->amount, $transaction->user_id);
            if($result === true) {
                PointTransaction::where('id', $id)->update([
                    'status' => 'approved',
                ]);
            }else {
                PointTransaction::where('id', $id)->update([
                    'status' => 'cancelled',
                ]);
            }
            return back();   
        }else {
            Toast::title('Whooops!')->message('You are not allowed to do this function.')->warning()->rightTop()->autoDismiss(5);

            return back();
        }

        
        // return redirect()->back()->with('success', 'Transaction approved successfully.');
    }

    public function adminPointsTransactions(Request $request) {
        $user =  $request->user(); 
        $user_id = $user->id; 

        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                Collection::wrap($value)->each(function ($value) use ($query) {
                    $query
                        ->orWhere('id', 'LIKE', "%{$value}%")
                        ->orWhere('type', 'LIKE', "%{$value}%")
                        ->orWhere('status', 'LIKE', "%{$value}%");
                });
            });
        });

         
        $transactions = QueryBuilder::for(PointTransaction::class)
        ->defaultSort('-updated_at')
        ->allowedSorts(['id', 'name', 'payment_method', 'status', 'updated_at'])
        ->allowedFilters(['id', 'name', 'type', 'status', $globalSearch])
        ->with('payment_method')
        ->with('user_id');
        
        return view('admin.points-transactions.index', [
            'user' => $user, 
            'transactions' => SpladeTable::for($transactions)
                ->withGlobalSearch(columns: ['id', 'name'])
                ->column('id', sortable: true) 
                ->column('status', sortable: true)
                ->column('type', sortable: true)
                ->column('amount', sortable: true)
                ->column('payment_method', sortable: true, label: 'Payment Method')
                ->column('updated_at', sortable: true)
                ->column('proof')
                ->column('actions')
                ->paginate(15)
                ->perPageOptions([15, 50, 100])
        ]); 

    }

    // // Store a new tree in the database
    // public function store(Request $request) 
    // {
    //     $request->validate([
    //         'amount' => 'required|string|max:255',
    //     ]);

    //     // Create a new tree in the database
    //     PointTransaction::create($request->all());
        
    //     // return Redirect::back();
    //     Toast::title('Success')->message('Test added successfully.')->success()->rightTop()->autoDismiss(3); 

    //     // Redirect back to the form with a success message
    //     return redirect()->route('wallet.index')->with('success', 'Tree added successfully');
    // }   

    public function getHistory(Request $request)
    {
        $user = $request->user();

        $transactions = $user->pointTransactions()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'transactions' => $transactions,
        ]);
    }

    public function viewTransaction(Request $request, $id)
    {
        $transaction = PointTransaction::find($id);

        return response()->json([
            'transaction' => $transaction,
        ]);
    }
}
