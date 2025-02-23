<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lista de Productos</title>
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
        .container {
            width: 100%;
            margin-top: 150px; /* Aumenta el margen superior */
        }

        h2 {
            color: #2d4f88;
            text-align: center;
            font-size: 16px;
            margin-top: 100px;
        }
        .info {
            font-size: 11px;
            margin-bottom: 10px;
            padding: 8px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            background: rgba(255, 255, 255, 0.9);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: center;
        }
        th {
            background: #436eb3;
            color: white;
        }
        .total {
            text-align: right;
            font-weight: bold;
            margin-top: 10px;
            padding: 6px;
            background: rgba(255, 255, 255, 0.9);
        }

        /* Tabla de Productos */
        .products {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
            font-size: 12px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            overflow: hidden;
        }

        /* Contenedor de la tabla de Totales */
        .totals-container {
            width: 50%;
            float: right; /* Se coloca a la derecha */
            margin-top: 20px; /* Espacio entre las tablas */
        }

        /* Tabla de Totales */
        .totals {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            overflow: hidden;
        }

        /* Estilos de los encabezados */
        .products th, .totals th {
            background: #2d4f88; /* Azul oscuro */
            color: white;
            padding: 8px;
            text-align: left;
        }

        /* Estilos de las celdas */
        .products td, .totals td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
            font-weight: bold;
        }

        /* Filas alternas */
        .products tr:nth-child(even), .totals tr:nth-child(even) {
            background-color: #f0f4fa;
        }

        .products tr:nth-child(odd), .totals tr:nth-child(odd) {
            background-color: #ffffff;
        }




    </style>
</head>
<body>
    <div class="container">
        <h2>Cotización de productos</h2>

        <!-- Información de la Venta -->
        <div class="info">
            <p><strong>Fecha:</strong> {{ $sale->created_at->format('d/m/Y') }}</p>
            <p><strong>Cliente:</strong> {{ $sale->user->name }} {{ $sale->user->last_name }}</p>
        </div>

        <!-- Tabla de Productos -->
        <!-- Tabla de Productos -->
        <table class="products">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Acabado</th>
                    <th>Ancho</th>
                    <th>Largo</th>
                    <th>Total por producto</th>
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
                        <td>${{ number_format($product->pivot->sale_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Tabla de Totales (A la derecha) -->
        <div class="totals-container">
            <table class="totals">
                
                <tr>
                    <th>Total</th>
                    <td><strong>${{ number_format($sale->total_with_iva, 2) }}</strong></td>
                </tr>
            </table>
        </div>



    </div>
</body>
</html>
