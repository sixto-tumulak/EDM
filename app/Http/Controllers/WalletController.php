<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

use Spatie\QueryBuilder\AllowedFilter;  
use Illuminate\Support\Collection; 
use ProtoneMedia\Splade\AbstractTable; 
use ProtoneMedia\Splade\SpladeTable; 
use Spatie\QueryBuilder\QueryBuilder; 
use ProtoneMedia\Splade\SpladeQueryBuilder; 



use App\Models\PaymentMethod;
use App\Models\PointTransaction;   

class WalletController extends Controller {
    public function index(Request $request) {
        $user = $request->user();
        return view('wallet.index', [
            'user' => $user, 
            'payment_methods' => PaymentMethod::all(), 
        ]); 
    }

    public function walletTransactions(Request $request) {



        $user =  $request->user(); 
        $user_id = $user->id; 
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                Collection::wrap($value)->each(function ($value) use ($query) {
                    $query
                        ->orWhere('id', 'LIKE', "%{$value}%")
                        ->orWhere('status', 'LIKE', "%{$value}%");
                });
            });
        });

         
        $transactions = QueryBuilder::for(PointTransaction::class)
        ->defaultSort('-updated_at')
        ->allowedSorts(['id', 'name', 'payment_method', 'status', 'updated_at'])
        ->allowedFilters(['id', 'name', 'status', $globalSearch])
        ->with('payment_method')
        ->where('user_id', $user_id);

        if($user->role_id === 1) {
            return view('wallet.transactions', [
                'user' => $user, 
                'transactions' => SpladeTable::for($transactions)
                    ->withGlobalSearch(columns: ['id', 'name'])
                    ->column('id', sortable: true) 
                    ->column('status', sortable: true, searchable: true)
                    ->column('amount')
                    ->column('type')
                    ->column('payment_method', sortable: true, label: 'Payment Method')
                    ->column('updated_at', sortable: true)
                    ->column('proof')
                    ->paginate(15)
                    ->perPageOptions([15, 50, 100])
            ]); 
        }else {
            return view('wallet.transactions', [
                'user' => $user, 
                'transactions' => SpladeTable::for($transactions)
                    ->withGlobalSearch(columns: ['id', 'name'])
                    ->column('id', sortable: true) 
                    ->column('status', sortable: true, searchable: true)
                    ->column('amount')
                    ->column('type')
                    ->column('payment_method', sortable: true, label: 'Payment Method')
                    ->column('updated_at', sortable: true)
                    ->paginate(15)
                    ->perPageOptions([15, 50, 100])
            ]);
        }

    }
}  