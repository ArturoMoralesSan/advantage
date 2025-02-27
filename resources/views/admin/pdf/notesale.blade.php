<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cotización</title>
    <style>
    @page {
            size: letter; /* Tamaño carta */
            margin: 0;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            background-image: url("{{ public_path('img/atvantage_note.jpg') }}");
            background-size: contain;
            background-position: top center;
            background-repeat: no-repeat;
            margin: 50px 40px;
        }
        .info {
            margin-top:130px;
            display:block;
        }
        .id_cotización {
            float:right;
            text-align:right;
        }
        .header, .footer {
            width: 100%;
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 10px;
        }
        .header td, .footer td {
            padding: 5px;
        }
        .title {
            font-weight: bold;
            text-align: center;
            font-size: 14px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .table th, .table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
        .table th {
            background:rgb(77, 77, 77);
            color: #fff;
        }
        .totals {
            width: 40%;
            float: right;
            margin-top: 10px;
        }
        .totals td {
            padding: 8px;
            border: 1px solid #000;
        }
    </style>
</head>
<body>
    
    <div class="info">
        <table width="100%">
            <tr>
                <td><strong>Fecha:</strong> {{ $sale->created_at->format('d/m/Y') }}</td>
                <td style="text-align: right;"><strong>ID de cotización:</strong> {{ $sale->id }}</td>
            </tr>
        </table>
    </div>

    
    <table class="header">
        <tr>
            <td><strong>Razón social:</strong> {{ $sale->user->customer->business_name }}</td>
            <td><strong>Nombre comercial:</strong> {{ $sale->user->customer->trade_name }} </td>
            <td><strong>RFC:</strong> {{ $sale->user->customer->rfc }}</td>

        </tr>
        <tr>
            <td><strong>Régimen Fiscal:</strong> {{ $sale->user->customer->tax_regime }}</td>
            <td><strong>Contacto:</strong> {{ $sale->user->customer->contact }}</td>
            <td><strong>Teléfono:</strong> {{ $sale->user->customer->phone }}</td>

        </tr>
        <tr>
            <td><strong>Email:</strong> {{ $sale->user->customer->email }}</td>
        </tr>
        <tr>
            <td><strong>Calle:</strong> {{ $sale->user->customer->street }}</td>
            <td><strong>Número Ext.:</strong> {{ $sale->user->customer->ext_number }}</td>
            <td><strong>Número Int.:</strong> {{ $sale->user->customer->int_number }}</td>
        </tr>
        <tr>
            <td colspan="3"><strong>Entre Calles:</strong> {{ $sale->user->customer->between_streets }} y {{ $sale->user->customer->and_street }}</td>
        </tr>
        <tr>
            <td><strong>Colonia:</strong> {{ $sale->user->customer->colony }}</td>
            <td><strong>Código Postal:</strong> {{ $sale->user->customer->postal_code }}</td>
            <td><strong>Municipio:</strong> {{ $sale->user->customer->municipality }}</td>
        </tr>
        <tr>
            <td><strong>Población:</strong> {{ $sale->user->customer->population }}</td>
            <td><strong>Estado:</strong> {{ $sale->user->customer->state }}</td>
            <td><strong>País:</strong> {{ $sale->user->customer->country }}</td>
        </tr>
    </table>
    
    <div class="title">Cotización de Productos</div>
    
    <table class="table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Producto</th>
                <th>Categoría</th>
                <th>Acabado</th>
                <th>Ancho</th>
                <th>Largo</th>
                <th>Cantidad de producto utilizado</th>
                <th>Importe</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->products as $product)
                <tr>
                    <td>{{ $product->pivot->product_name }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->type->name }}</td>
                    <td>{{ $product->cut_name }}</td>
                    <td>{{ $product->pivot->width }} cm</td>
                    <td>{{ $product->pivot->height }} cm</td>
                    <td>{{ $product->pivot->quantity_product }}</td>
                    <td>${{ number_format($product->pivot->sale_price , 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <table class="totals">
        <tr>
            <td>Subtotal</td>
            <td>${{ number_format($sale->total_sale_price, 2) }}</td>
        </tr>
        <tr>
            <td>IVA</td>
            <td>${{ number_format($sale->iva, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Total</strong></td>
            <td><strong>${{ number_format($sale->total_with_iva, 2) }}</strong></td>
        </tr>
    </table>
</body>
</html>
