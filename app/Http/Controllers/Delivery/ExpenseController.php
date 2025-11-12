<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class ExpenseController extends Controller
{
    /**
     * Display the expenses index page
     */
    public function index(): View
    {
        return view('delivery.expenses');
    }

    /**
     * Get all expenses with pagination and filters
     */
    public function showAll(Request $request): JsonResponse
    {
        try {
            $perPage   = $request->get('per_page', 10);
            $search    = $request->get('search');
            $startDate = $request->get('start_date');
            $endDate   = $request->get('end_date');

            $query = Expense::with(['userInserter', 'userUpdater', 'category'])
                ->orderBy('expense_date', 'desc')
                ->orderBy('created_at', 'desc');

            if ($search) {
                $query->search($search);
            }

            if ($startDate && $endDate) {
                $query->dateRange($startDate, $endDate);
            } elseif ($startDate) {
                $query->where('expense_date', '>=', $startDate);
            } elseif ($endDate) {
                $query->where('expense_date', '<=', $endDate);
            }

            $expenses = $query->paginate($perPage);

            return response()->json([
                'status'  => 200,
                'message' => 'Expenses retrieved successfully',
                'data'    => [
                    'expenses' => $expenses->items(),
                    'meta'     => [
                        'total'        => $expenses->total(),
                        'per_page'     => $expenses->perPage(),
                        'current_page' => $expenses->currentPage(),
                        'last_page'    => $expenses->lastPage(),
                    ]
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 500,
                'message' => 'Error retrieving expenses: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show a specific expense
     */
    public function show($id): JsonResponse
    {
        try {
            $expense = Expense::with(['userInserter', 'userUpdater', 'category'])->findOrFail($id);

            return response()->json([
                'status'  => 200,
                'message' => 'Expense retrieved successfully',
                'data'    => $expense
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 404,
                'message' => 'Expense not found: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Store a new expense
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'description'  => 'required|string|max:255',
            'value'        => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'category_id'  => 'required|integer|exists:expenses_categories,id',
        ]);

        try {
            DB::beginTransaction();

            $expense = Expense::create([
                'description'      => $request->description,
                'value'            => $request->value,
                'expense_date'     => $request->expense_date,
                'category_id'      => $request->category_id,
                'user_inserter_id' => Auth::id(),
                'user_updater_id'  => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'status'  => 201,
                'message' => 'Despesa criada com sucesso!',
                'data'    => $expense->load(['userInserter', 'userUpdater'])
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 500,
                'message' => 'Erro ao criar despesa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing expense
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'description'  => 'required|string|max:255',
            'value'        => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'category_id'  => 'required|integer|exists:expenses_categories,id',
        ]);

        try {
            DB::beginTransaction();

            $expense = Expense::findOrFail($id);

            $expense->update([
                'description'     => $request->description,
                'value'           => $request->value,
                'expense_date'    => $request->expense_date,
                'category_id'     => $request->category_id,
                'user_updater_id' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'status'  => 200,
                'message' => 'Despesa atualizada com sucesso!',
                'data'    => $expense->load(['userInserter', 'userUpdater'])
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 500,
                'message' => 'Erro ao atualizar despesa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an expense
     */
    public function destroy($id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $expense = Expense::findOrFail($id);
            $expense->delete();

            DB::commit();

            return response()->json([
                'status'  => 200,
                'message' => 'Despesa excluída com sucesso!'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 500,
                'message' => 'Erro ao excluir despesa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get expenses summary/statistics
     */
    public function summary(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date');
            $endDate   = $request->get('end_date');

            $query = Expense::query();

            if ($startDate && $endDate) {
                $query->dateRange($startDate, $endDate);
            }

            $totalExpenses  = $query->sum('value');
            $expenseCount   = $query->count();
            $averageExpense = $expenseCount > 0 ? $totalExpenses / $expenseCount : 0;

            return response()->json([
                'status' => 200,
                'data'   => [
                    'total_expenses'  => $totalExpenses,
                    'expense_count'   => $expenseCount,
                    'average_expense' => $averageExpense,
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 500,
                'message' => 'Error retrieving expenses summary: ' . $e->getMessage()
            ], 500);
        }
    }

    public function loadCategories()
    {
        try {
            $categories = DB::table('expenses_categories')->orderBy('description')->get();
            return response()->json([
                'status' => 200,
                'data'   => $categories
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 500,
                'message' => 'Erro ao buscar as categorias: ' . $e->getMessage()
            ], 500);
        }
    }
}
