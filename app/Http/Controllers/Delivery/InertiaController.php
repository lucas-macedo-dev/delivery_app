<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InertiaController extends Controller
{
    public function renderShoppingList(): \Inertia\Response
    {
        return Inertia::render('ShoppingList', [
            'title' => 'Shopping List',
            'description' => 'A comprehensive shopping list to manage your purchases effectively.',
            'breadcrumbs' => [
                ['name' => 'Home', 'href' => route('delivery.home')],
                ['name' => 'Tables', 'href' => route('delivery.shoppingList')],
            ],
        ])->withViewData([
            'title' => 'Shopping List',
            'description' => 'A comprehensive shopping list to manage your purchases effectively.',
        ]);
    }
}
