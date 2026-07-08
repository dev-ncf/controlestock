<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function profit(Request $request)
{
    // 1. Intervalo de datas
    $start = $request->query('start_date', now()->startOfMonth()->format('Y-m-d'));
    $end = $request->query('end_date', now()->endOfMonth()->format('Y-m-d'));

    // 2. Query de Estatísticas Globais
    $stats = \DB::table('venda_items')
        ->join('vendas', 'venda_items.venda_id', '=', 'vendas.id')
        ->selectRaw('
            SUM(subtotal) as total_revenue, 
            SUM(quantity * cost_price) as total_cost,
            (SUM(subtotal) - SUM(quantity * cost_price)) as gross_profit
        ')
        ->whereBetween('vendas.date', [$start . ' 00:00:00', $end . ' 23:59:59'])
        ->where('vendas.status', '!=', 'canceled')
        ->first();

    // 3. Listagem Detalhada de Produtos (Para fundamentar o lucro)
    $soldProducts = \DB::table('venda_items')
        ->join('vendas', 'venda_items.venda_id', '=', 'vendas.id')
        ->join('produtos', 'venda_items.produto_id', '=', 'produtos.id')
        ->selectRaw('
            produtos.name, 
            SUM(venda_items.quantity) as qty,
            SUM(venda_items.subtotal) as item_revenue,
            SUM(venda_items.quantity * venda_items.cost_price) as item_cost,
            SUM(venda_items.subtotal - (venda_items.quantity * venda_items.cost_price)) as item_profit
        ')
        ->whereBetween('vendas.date', [$start . ' 00:00:00', $end . ' 23:59:59'])
        ->where('vendas.status', '!=', 'canceled')
        ->groupBy('produtos.id', 'produtos.name')
        ->orderBy('item_profit', 'desc')
        ->get();

    // 4. Lógica para Download de PDF
    if ($request->has('download')) {
        $pdf = Pdf::loadView('admin.pdf.profit_pdf', compact('stats', 'soldProducts', 'start', 'end'));
        return $pdf->download("relatorio-lucratividade-{$start}-a-{$end}.pdf");
    }

    return view('admin.reports.profit', compact('stats', 'soldProducts', 'start', 'end'));
}
}