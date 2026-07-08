<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Produto;
use App\Models\Cliente;
use App\Models\Fornecedor;

class ReportService
{
    /**
     * Relatório de Lucratividade por Período
     * Calcula Receita, Custo e Lucro Bruto
     */
    public function getProfitabilityReport($startDate, $endDate)
    {
        return DB::table('venda_items')
            ->join('vendas', 'venda_items.venda_id', '=', 'vendas.id')
            ->select(
                DB::raw('SUM(subtotal) as total_revenue'),
                DB::raw('SUM(quantity * cost_price) as total_cost'),
                DB::raw('SUM(subtotal - (quantity * cost_price)) as gross_profit')
            )
            ->whereBetween('vendas.date', [$startDate, $endDate])
            ->where('vendas.status', '!=', 'canceled')
            ->first();
    }

    /**
     * Resumo Geral do Estoque (Valor Patrimonial)
     */
    public function getStockReport()
    {
        return [
            'total_items' => Produto::sum('stock_quantity'),
            // Valor total investido em stock (Preço de Custo * Qtd)
            'total_valuation' => Produto::selectRaw('SUM(stock_quantity * purchase_price) as valuation')->value('valuation'),
            // Produtos que precisam de reposição
            'low_stock_products' => Produto::whereRaw('stock_quantity <= min_stock')->get(['name', 'stock_quantity', 'min_stock'])
        ];
    }

    /**
     * Resumo Financeiro (Contas a Receber vs Contas a Pagar)
     */
    public function getFinancialOverview()
    {
        return [
            'total_to_receive' => Cliente::sum('current_balance'), // Dívida dos clientes
            'total_to_pay'     => Fornecedor::sum('balance_to_pay'),  // Nossa dívida com fornecedores
            'cash_flow_today'  => DB::table('pagamentos')->whereDate('payment_date', today())->sum('amount_paid')
        ];
    }

    /**
     * Top Produtos mais vendidos (Ranking)
     */
    public function getTopSellers($limit = 10)
    {
        return DB::table('venda_items')
            ->join('produtos', 'venda_items.produto_id', '=', 'produtos.id')
            ->select('produtos.name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_earned'))
            ->groupBy('produtos.id', 'produtos.name')
            ->orderBy('total_qty', 'desc')
            ->limit($limit)
            ->get();
    }
}