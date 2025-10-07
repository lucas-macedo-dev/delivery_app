<?php

namespace App\Imports;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToModel;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class OrdersImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Model|Order|null
     */
    public function model(array $row): Model|Order|null
    {
        return new Order([
            'ifood_id'              => $row['id_completo_do_pedido'] ?? null,
            'ifood_order_number'    => $row['id_curto_do_pedido'] ?? null,
            'order_date'            => Carbon::parse($row['data_e_hora_do_pedido'])->format('Y-m-d H:i:s'),
            'total_amount_order'    => $row['total_pago_pelo_cliente_r'] ?? 0,
            'total_amount_received' => $row['valor_dos_itens_r'] ?? 0,
            'status'                => $row['status_final_do_pedido'] === 'CONCLUIDO' ? 'completed' : 'cancelled',
        ]);
    }
}
