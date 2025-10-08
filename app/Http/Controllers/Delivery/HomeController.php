<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Order;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(
    ): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        $data['customers']          = Customer::all()->count();
        $data['orders']             = Order::all()->count();
        $data['amount']             = Order::all()->sum('total_amount_received');
        $data['expenses']           = Expense::all()->sum('value');
        $data['last_orders']        = Order::orderBy('order_date', 'desc')->limit(3)->get();
        $data['most_saled_product'] = (new ProductController)->mostSaledProduct();


        return view('delivery.home', ['data' => $data]);
    }

    public function searchData(Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate', now());

        if ( $startDate) {
            $data['customers']          = Customer::whereBetween('created_at', [$startDate, $endDate])->count();
            $data['orders']             = Order::whereBetween('created_at', [$startDate, $endDate])->count();
            $data['amount']             = Order::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount_received');
            $data['expenses']           = Expense::whereBetween('created_at', [$startDate, $endDate])->sum('value');
            $data['last_orders']        = Order::whereBetween('created_at', [$startDate, $endDate])->orderBy('order_date', 'desc')->limit(3)->get();
            $data['most_saled_product'] = (new ProductController)->mostSaledProduct();
        }
        $data['customers']          = Customer::whereBetween('created_at', [$startDate, $endDate])->count();
        $data['orders']             = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $data['amount']             = Order::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount_received');
        $data['expenses']           = Expense::whereBetween('created_at', [$startDate, $endDate])->sum('value');
        $data['last_orders']        = Order::whereBetween('created_at', [$startDate, $endDate])->orderBy('order_date', 'desc')->limit(3)->get();
        $data['most_saled_product'] = (new ProductController)->mostSaledProduct();

        return response()->json([
            'status' => 200,
            'data'   => $data
        ]);
    }
}
