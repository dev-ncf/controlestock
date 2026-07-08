<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index()
{
    $now = now();

    // Lucro Diário
    $dailyProfit = $this->reportService->getProfitabilityReport($now->copy()->startOfDay(), $now->copy()->endOfDay());
    
    // Lucro Semanal
    $weeklyProfit = $this->reportService->getProfitabilityReport($now->copy()->startOfWeek(), $now->copy()->endOfWeek());
    
    // Lucro Mensal (já existente no seu código)
    $monthlyProfit = $this->reportService->getProfitabilityReport($now->copy()->startOfMonth(), $now->copy()->endOfMonth());
    
    // Lucro Anual
    $annualProfit = $this->reportService->getProfitabilityReport($now->copy()->startOfYear(), $now->copy()->endOfYear());

    // Outros dados mantidos
    $stockData = $this->reportService->getStockReport();
    $financial = $this->reportService->getFinancialOverview();
    $topSellers = $this->reportService->getTopSellers(5);

    return view('admin.dashboard', compact(
        'dailyProfit', 
        'weeklyProfit', 
        'monthlyProfit', 
        'annualProfit', 
        'stockData', 
        'financial', 
        'topSellers'
    ));
}
}