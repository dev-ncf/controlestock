<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; color: #333; font-size: 10px; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .company-name { font-size: 16px; font-weight: bold; text-transform: uppercase; }
        .info-table { width: 100%; margin-bottom: 20px; border: 1px solid #eee; padding: 10px; background: #fafafa; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #333; color: #fff; padding: 8px; text-align: left; text-transform: uppercase; font-size: 9px; }
        td { padding: 8px; border-bottom: 1px solid #eee; vertical-align: top; }
        .text-right { text-align: right; }
        .debit { color: #d32f2f; font-weight: bold; }
        .credit { color: #2e7d32; font-weight: bold; }
        .balance { font-weight: bold; background: #f9fafb; }
        
        /* Estilo para a lista de produtos */
        .product-list { 
            margin-top: 5px; 
            font-size: 8px; 
            color: #666; 
            border-left: 1px solid #ddd; 
            padding-left: 5px;
            font-style: italic;
        }
        .product-item { margin-bottom: 2px; }

        .footer-total { background: #000; color: #fff; padding: 15px; margin-top: 20px; border-radius: 8px; }
        .total-value { font-size: 16px; font-weight: bold; float: right; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">Sua Empresa, LDA</div>
        <p>NIF: 5000123456 | E-mail: financeiro@empresa.com</p>
        <h2 style="margin-top: 15px;">EXTRATO DE CONTA CORRENTE</h2>
    </div>

    <table class="info-table">
        <tr>
            <td>
                <strong>CLIENTE:</strong> {{ $customer->name }}<br>
                <strong>NIF:</strong> {{ $customer->nif ?? 'N/A' }}
            </td>
            <td class="text-right">
                <strong>DATA DE EMISSÃO:</strong> {{ date('d/m/Y H:i') }}<br>
                <strong>MOEDA:</strong> Metical (MZN)
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th width="10%">Data</th>
                <th width="10%">Tipo</th>
                <th width="35%">Referência / Detalhes</th>
                <th width="15%" class="text-right">Débito (+)</th>
                <th width="15%" class="text-right">Crédito (-)</th>
                <th width="15%" class="text-right">Saldo</th>
            </tr>
        </thead>
        <tbody>
            @php $saldoAcumulado = 0; @endphp
            @foreach($ledger as $item)
                @php 
                    if($item->tipo == 'venda') $saldoAcumulado += $item->valor;
                    else $saldoAcumulado -= $item->valor;
                @endphp
                <tr>
                    <td>{{ date('d/m/Y', strtotime($item->date)) }}</td>
                    <td style="text-transform: uppercase; font-weight: bold; font-size: 8px;">
                        {{ $item->tipo == 'venda' ? 'Fatura' : 'Recibo' }}
                    </td>
                    <td>
                        <span style="font-weight: bold; color: #333;">{{ $item->ref }}</span>
                        
                        {{-- Listagem de Produtos --}}
                        @if($item->tipo == 'venda' && isset($item->items))
                            <div class="product-list">
                                @foreach($item->items as $linha)
                                    <div class="product-item">
                                        {{ $linha->quantity }}x {{ $linha->produto->name ?? 'Produto Removido' }} 
                                        <span style="color: #999;">(MZN {{ number_format($linha->subtotal, 2) }})</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </td>
                    
                    <td class="text-right debit">
                        {{ $item->tipo == 'venda' ? number_format($item->valor, 2) : '-' }}
                    </td>
                    
                    <td class="text-right credit">
                        {{ $item->tipo == 'pagamento' ? number_format($item->valor, 2) : '-' }}
                    </td>
                    
                    <td class="text-right balance">
                        {{ number_format($saldoAcumulado, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer-total">
        <span style="text-transform: uppercase; font-size: 10px; color: #ccc;">Saldo Devedor Atual:</span>
        <span class="total-value">MZN {{ number_format($customer->current_balance, 2) }}</span>
        <div style="clear: both;"></div>
    </div>

    <div style="margin-top: 30px; text-align: center; font-size: 8px; color: #999;">
        <p>Este documento é apenas para fins informativos e de conferência de conta corrente.</p>
        <p>&copy; {{ date('Y') }} MY-ERP System</p>
    </div>
</body>
</html>