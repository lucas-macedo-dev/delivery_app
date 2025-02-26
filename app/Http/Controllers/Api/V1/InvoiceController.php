<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\InvoiceResource;
use App\Models\Invoice;
use App\Traits\HttpResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
{
    use HttpResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
//        return InvoiceResource::collection(Invoice::with('user')->get());
        $teste = new Invoice();
        return $teste->filter($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id'      => 'required|exists:users,id',
            'type'         => 'required|in:C,P,B|max:1',
            'value'        => 'required|numeric|between:1,9999.99',
            'paid'         => 'required|numeric|between:0,1',
            'payment_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return $this->error('Data Invalid', 422, $validator->errors());
        }

        $created = Invoice::create($request->all());

        if ($created->save()) {
            return $this->response('Invoice Created', 200, new InvoiceResource($created->load('user')));
        }

        return $this->response('Invoice not Created', 400);
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice): InvoiceResource
    {
        return new InvoiceResource($invoice);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice): \Illuminate\Http\JsonResponse
    {
        if(!auth()->user()->tokenCan('invoice-store')) {
            return $this->response('Unauthorized', 403);
        }


        $validator = Validator::make($request->all(), [
            'user_id'      => 'required|exists:users,id',
            'type'         => 'required|in:C,P,B|max:1',
            'value'        => 'required|numeric|between:1,9999.99',
            'paid'         => 'required|numeric|between:0,1',
            'payment_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return $this->error('Data Invalid', 422, $validator->errors());
        }

        $valideted = $validator->validated();

        $updated = $invoice->update([
                'user_id'      => $valideted['user_id'],
                'type'         => $valideted['type'],
                'value'        => $valideted['value'],
                'paid'         => $valideted['paid'],
                'payment_date' => $valideted['paid'] ? $valideted['payment_date'] : null
            ]
        );

        if ($updated) {
            return $this->response('Invoice Updated', 200, new InvoiceResource($invoice));
        }

        return $this->response('Invoice not Updated', 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice): \Illuminate\Http\JsonResponse
    {
        $deleted = $invoice->delete();

        if ($deleted) {
            return $this->response('Invoice Deleted', 200);
        }

        return $this->response('Invoice not Deleted', 400);
    }

}
