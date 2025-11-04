<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index(
    ): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        $data['last_orders']        = Order::orderBy('order_date', 'desc')->limit(3)->get();
        return view('delivery.home', ['data' => $data]);
    }

    public function searchData(Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate   = $request->input('endDate', $startDate);

        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate   = Carbon::parse($endDate)->endOfDay();

        $data['customers']          = Customer::whereBetween('created_at', [$startDate, $endDate])->count();
        $data['orders']             = Order::whereBetween('order_date', [$startDate, $endDate])->count();
        $data['amount']             =
            Order::whereBetween('order_date', [$startDate, $endDate])->sum('total_amount_received');
        $data['expenses']           = Expense::whereBetween('created_at', [$startDate, $endDate])->sum('value');
        $data['last_orders']        =
            Order::whereBetween('created_at', [$startDate, $endDate])->orderBy('order_date', 'desc')->limit(3)->get();
        $data['most_saled_product'] = (new ProductController)->mostSaledProduct($startDate, $endDate);

        return response()->json([
            'status' => 200,
            'data'   => $data
        ]);
    }
}
