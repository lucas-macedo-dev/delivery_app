<?php

namespace App\Imports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToModel;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class OrdersImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Order([
            'ifood_id'              => $row['id_do_pedido'] ?? null,
            'ifood_order_number'    => $row['n_pedido'] ?? null,
            'order_date'            => Carbon::parse(ExcelDate::excelToDateTimeObject($row['data'])->format('Y-m-d H:i:s')),
            'total_amount_order'    => isset($row['total_do_parceiro']) ? str_replace(',', '.', $row['total_do_parceiro']) : 0,
            'total_amount_received' => isset($row['total_do_pedido']) ? str_replace(',', '.', $row['total_do_pedido']) : 0,
            'status'                => !empty($row['origem_do_cancelamento']) ? 'cancelled' : 'completed',
        ]);
    }
}
