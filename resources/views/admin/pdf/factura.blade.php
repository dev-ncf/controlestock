<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fatura {{ $venda->invoice_number }}</title>
    <style>
        /* Aumentado o tamanho base de 12px para 14px */
        body { font-family: 'sans-serif'; font-size: 14px; color: #333; line-height: 1.4; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .info { width: 100%; margin-top: 20px; }
        .info td { vertical-align: top; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f2f2f2; }
        .totals { width: 100%; margin-top: 20px; text-align: right; }
        .footer { margin-top: 50px; text-align: center; font-size: 11px; color: #777; }
        .status-paga { color: green; font-weight: bold; }
        .status-divida { color: red; font-weight: bold; }
        /* Classe para dar maior destaque ao número da fatura */
        .invoice-title { font-size: 16px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SUA EMPRESA, LDA</h1>
        <p>NIF: 5000123456 | Telefone: +258 84 000 0000</p>
        <p>Nampula, Moçambique</p>
    </div>

    <table class="info">
        <tr>
            <td>
                <strong>CLIENTE:</strong> {{ $venda->cliente->name }}<br>
                <strong>NIF:</strong> {{ $venda->cliente->nif ?? 'Consumidor Final' }}
            </td>
            <td style="text-align: right;">
                <!-- Exemplo de formatação com 6 dígitos (ex: 000001). 
                     Caso prefira o número original sem zeros, substitua por: {{ $venda->invoice_number }} -->
                <strong class="invoice-title">FATURA Nº: {{ str_pad($venda->invoice_number, 6, '0', STR_PAD_LEFT) }}</strong><br>
                <strong>DATA:</strong> {{ $venda->date->format('d/m/Y H:i') }}<br>
                <strong>VENCIMENTO:</strong> {{ $venda->due_date->format('d/m/Y') }}
            </td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <!-- Nova coluna para numeração sequencial dos itens -->
                <th style="width: 5%; text-align: center;">#</th>
                <th>Descrição</th>
                <th style="width: 10%; text-align: center;">Qtd</th>
                <th style="width: 20%; text-align: right;">Preço Unit.</th>
                <th style="width: 20%; text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venda->items as $item)
            <tr>
                <!-- Gera a numeração sequencial dos itens (1, 2, 3...) -->
                <td style="text-align: center;">{{ $loop->iteration }}</td>
                <td>{{ $item->produto->name }}</td>
                <td style="text-align: center;">{{ $item->quantity }}</td>
                <td style="text-align: right;">MZN {{ number_format($item->unit_price, 2) }}</td>
                <td style="text-align: right;">MZN {{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td style="text-align: right; padding-right: 15px;"><strong>TOTAL DA FATURA:</strong></td>
            <td style="width: 30%; text-align: right;"><strong>MZN {{ number_format($venda->total_amount, 2) }}</strong></td>
        </tr>
        <tr>
            <td style="text-align: right; padding-right: 15px;">TOTAL PAGO:</td>
            <td style="text-align: right;">MZN {{ number_format($totalPago, 2) }}</td>
        </tr>
        <tr>
            <td style="text-align: right; padding-right: 15px;"><strong style="font-size: 16px;">SALDO DEVEDOR:</strong></td>
            <td style="text-align: right;"><strong style="font-size: 16px; color: #d9534f;">MZN {{ number_format($falta, 2) }}</strong></td>
        </tr>
    </table>

    <div class="footer">
        <p>Obrigado pela sua preferência!</p>
        <p>Este documento foi processado por computador.</p>
    </div>
</body>
</html>