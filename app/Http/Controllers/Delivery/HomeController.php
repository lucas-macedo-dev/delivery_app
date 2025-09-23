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
}
