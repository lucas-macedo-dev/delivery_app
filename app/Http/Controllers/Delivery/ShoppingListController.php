<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ShoppingListController extends Controller
{
    public function index(Request $request)
    {
        $query = ShoppingList::query();

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $lists = $query->withCount([
            'items',
            'items as purchased_items_count' => function ($query) {
                $query->where('purchased', true);
            }
        ])
            ->with('items')
            ->latest()
            ->paginate(12);

        // Calcular valor total de cada lista
        $lists->getCollection()->transform(function ($list) {
            $list->total_value = $list->items->sum(function ($item) {
                return $item->estimated_price * $item->quantity;
            });
            return $list;
        });

        $stats = [
            'total' => ShoppingList::count(),
            'pending' => ShoppingList::where('status', 'pending')->count(),
            'in_progress' => ShoppingList::where('status', 'in_progress')->count(),
            'completed' => ShoppingList::where('status', 'completed')->count(),
            'total_value' => DB::table('shopping_list_items')
                ->join('shopping_lists', 'shopping_list_items.shopping_list_id', '=', 'shopping_lists.id')
                ->where('shopping_lists.status', '!=', 'cancelled')
                ->sum(DB::raw('shopping_list_items.estimated_price * shopping_list_items.quantity')),
        ];

        return Inertia::render('ShoppingList/Index', [
            'lists' => $lists,
            'stats' => $stats,
        ]);
    }

    public function create()
    {
        return Inertia::render('ShoppingList/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $list = ShoppingList::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('shopping-lists.show', $list->id)
            ->with('success', 'Lista criada com sucesso!');
    }

    public function show(ShoppingList $shoppingList)
    {
        $shoppingList->load('items');

        $shoppingList->total_value = $shoppingList->items->sum(function ($item) {
            return $item->estimated_price * $item->quantity;
        });

        $shoppingList->purchased_items_count = $shoppingList->items
            ->where('purchased', true)
            ->count();

        return Inertia::render('ShoppingList/Show', [
            'list' => $shoppingList,
        ]);
    }

    public function edit(ShoppingList $shoppingList)
    {
        return Inertia::render('ShoppingList/Edit', [
            'list' => $shoppingList,
        ]);
    }

    public function update(Request $request, ShoppingList $shoppingList)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $shoppingList->update($validated);

        return back()->with('success', 'Lista atualizada com sucesso!');
    }

    public function destroy(ShoppingList $shoppingList)
    {
        $shoppingList->delete();

        return redirect()->route('shopping-lists.index')
            ->with('success', 'Lista excluída com sucesso!');
    }

    public function updateStatus(Request $request, ShoppingList $shoppingList)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        $shoppingList->update(['status' => $validated['status']]);

        return back()->with('success', 'Status atualizado!');
    }

    // =========== ITEM METHODS ===========

    public function storeItem(Request $request, ShoppingList $shoppingList)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'required|string|max:10',
            'estimated_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $shoppingList->items()->create($validated);

        return back()->with('success', 'Item adicionado!');
    }

    public function updateItem(Request $request, ShoppingList $shoppingList, ShoppingListItem $item)
    {
        $validated = $request->validate([
            'product_name' => 'string|max:255',
            'quantity' => 'numeric|min:0.01',
            'unit' => 'string|max:10',
            'estimated_price' => 'nullable|numeric|min:0',
            'actual_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $item->update($validated);

        return back()->with('success', 'Item atualizado!');
    }

    public function toggleItemPurchased(Request $request, ShoppingList $shoppingList, ShoppingListItem $item)
    {
        $item->update([
            'purchased' => $request->input('purchased', !$item->purchased),
            'purchased_at' => $request->input('purchased') ? now() : null,
        ]);

        return back();
    }

    public function destroyItem(ShoppingList $shoppingList, ShoppingListItem $item)
    {
        $item->delete();

        return back()->with('success', 'Item removido!');
    }
}
