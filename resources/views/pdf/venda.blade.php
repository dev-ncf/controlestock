<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fatura {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .details { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { bg-color: #f2f2f2; }
        .total { text-align: right; margin-top: 20px; font-size: 16px; font-weight: bold; }
        .status-unpaid { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SUA EMPRESA, LDA</h1>
        <p>NIF: 5000123456 | Luanda, Angola</p>
    </div>

    <table class="details">
        <tr>
            <td><strong>CLIENTE:</strong> {{ $invoice->customer->name }}</td>
            <td style="text-align: right;"><strong>FATURA Nº:</strong> {{ $invoice->invoice_number }}</td>
        </tr>
        <tr>
            <td><strong>DATA:</strong> {{ $invoice->date->format('d/m/Y') }}</td>
            <td style="text-align: right;"><strong>VENCIMENTO:</strong> {{ $invoice->due_date->format('d/m/Y') }}</td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th>Descrição</th>
                <th>Qtd</th>
                <th>Preço Unit.</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->unit_price, 2) }}</td>
                <td>{{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        TOTAL A PAGAR: KZ {{ number_format($invoice->total_amount, 2) }}
    </div>

    @if($invoice->status == 'unpaid')
        <p class="status-unpaid">DOCUMENTO EM DÍVIDA</p>
    @else
        <p style="color: green;">PAGO</p>
    @endif
</body>
</html>