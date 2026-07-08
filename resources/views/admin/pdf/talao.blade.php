<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0px; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9pt;
            line-height: 1.2;
            width: 72mm; /* Área útil para papel de 80mm */
            margin: 0 auto;
            padding: 10px;
            color: #000;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        
        .header { margin-bottom: 15px; }
        .brand { font-size: 14pt; font-weight: 900; margin-bottom: 2px; }
        
        .separator {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .info-table { width: 100%; margin-bottom: 10px; font-size: 8pt; }
        
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .items-table th { border-bottom: 1px solid #000; padding: 5px 0; text-align: left; font-size: 8pt; }
        .items-table td { padding: 5px 0; vertical-align: top; }

        .totals { width: 100%; margin-top: 5px; }
        .total-final { font-size: 12pt; font-weight: bold; margin-top: 5px; border: 1px solid #000; padding: 5px; text-align: center; }

        .qr-code { margin: 15px 0; }
        .footer { font-size: 7pt; margin-top: 10px; color: #333; }
    </style>
</head>
<body>

    <div class="header text-center">
        <div class="brand">SUA EMPRESA, LDA</div>
        <div style="font-size: 8pt;">
            NIF: 5000123456 | Tel: +258 84 000 0000<br>
            Avenida Principal, Maputo
        </div>
    </div>

    <div class="separator"></div>

    <div class="text-center bold uppercase" style="font-size: 10pt; letter-spacing: 2px;">
        {{ $falta <= 0 ? 'Recibo de Venda' : 'Venda a Crédito' }}
    </div>

    <div class="separator"></div>

    <table class="info-table">
        <tr>
            <td>DOC: <strong>{{ $venda->invoice_number }}</strong></td>
            <td class="text-right">DATA: {{ $venda->date->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td colspan="2">CLIENTE: <strong>{{ $customer->name ?? $venda->cliente->name }}</strong></td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th width="15%">QTD</th>
                <th width="55%">DESCRIÇÃO</th>
                <th width="30%" class="text-right">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venda->items as $item)
            <tr>
                <td>{{ $item->quantity }}</td>
                <td>
                    <div class="bold">{{ $item->produto->name }}</div>
                    <div style="font-size: 7pt;">un x {{ number_format($item->unit_price, 2) }}</div>
                </td>
                <td class="text-right"><strong>{{ number_format($item->subtotal, 2) }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="separator"></div>

    <table class="totals text-right">
        <tr>
            <td width="60%">SUBTOTAL MZN:</td>
            <td width="40%" class="bold">{{ number_format($venda->total_amount, 2) }}</td>
        </tr>
        <tr>
            <td>TOTAL PAGO MZN:</td>
            <td class="bold text-green-600">{{ number_format($totalPago, 2) }}</td>
        </tr>
    </table>

    <div class="total-final uppercase">
        Saldo Devedor: MZN {{ number_format($falta, 2) }}
    </div>

    <!-- <div class="text-center qr-code">

        @php
            $qrData = "Fatura:" . $venda->invoice_number . "|Valor:" . $venda->total_amount . "|Cliente:" . $venda->cliente->name;
            $qrUrl = "https://chart.googleapis.com/chart?chs=100x100&cht=qr&chl=" . urlencode($qrData);
        @endphp
        <img src="{{ $qrUrl }}" width="80" height="80">
        <div style="font-size: 6pt; margin-top: 5px;">{{ $venda->invoice_number }}</div>
    </div> -->

    <div class="separator"></div>

    <div class="footer text-center">
        Obrigado pela sua preferência!<br>
        <strong>Conserve este talão como prova de compra.</strong><br>
        Processado por MY-ERP Software
    </div>

</body>
</html>