<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório de Lucratividade</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; text-transform: uppercase; }
        .stats-box { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .stats-box td { padding: 15px; border: 1px solid #eee; text-align: center; }
        .label { font-size: 10px; color: #888; font-weight: bold; display: block; margin-bottom: 5px; }
        .value { font-size: 16px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #f9f9f9; padding: 10px; font-size: 10px; text-transform: uppercase; border-bottom: 2px solid #333; }
        td { padding: 8px; border-bottom: 1px solid #eee; }
        .text-right { text-align: right; }
        .total-row { background: #333; color: #fff; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Relatório de Lucratividade</h2>
        <p>Período: {{ date('d/m/Y', strtotime($start)) }} a {{ date('d/m/Y', strtotime($end)) }}</p>
    </div>

    <table class="stats-box">
        <tr>
            <td>
                <span class="label">FATURAÇÃO TOTAL</span>
                <span class="value">MZN {{ number_format($stats->total_revenue, 2) }}</span>
            </td>
            <td>
                <span class="label">CUSTO TOTAL (CPV)</span>
                <span class="value">MZN {{ number_format($stats->total_cost, 2) }}</span>
            </td>
            <td style="background-color: #f0fff4;">
                <span class="label">LUCRO BRUTO</span>
                <span class="value" style="color: #2f855a;">MZN {{ number_format($stats->gross_profit, 2) }}</span>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th align="left">Produto</th>
                <th align="center">Qtd</th>
                <th class="text-right">Venda (MZN)</th>
                <th class="text-right">Custo (MZN)</th>
                <th class="text-right">Lucro (MZN)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($soldProducts as $p)
            <tr>
                <td>{{ $p->name }}</td>
                <td align="center">{{ $p->qty }}</td>
                <td class="text-right">{{ number_format($p->item_revenue, 2) }}</td>
                <td class="text-right">{{ number_format($p->item_cost, 2) }}</td>
                <td class="text-right"><strong>{{ number_format($p->item_profit, 2) }}</strong></td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="2">TOTAL</td>
                <td class="text-right">{{ number_format($stats->total_revenue, 2) }}</td>
                <td class="text-right">{{ number_format($stats->total_cost, 2) }}</td>
                <td class="text-right">{{ number_format($stats->gross_profit, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>