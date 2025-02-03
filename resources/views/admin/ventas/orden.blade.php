@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Orden de')
@section('tab_title', 'Orden de | ' . config('app.name'))
@section('description', 'Orden de.')
@section('css_classes', 'dashboard')

@section('content')

<section class="mb-16">
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Orden de trabajo
        </h1>
    </div>

    <div class="fluid-container mb-16">
        <p class="mb-12">
            @include('components.alert')
            <span class="color-link">«</span>
            <a href="{{ url('admin/ventas/') }}">Ver todas las ventas</a>
        </p>

        <section class="db-panel">
            <h3 class="db-panel__title">
                Detalles de la orden de trabajo
            </h3>

            
            <div class="order-table">

                <div class="order-table__body">
                    <table class="order-table__details">
                        <tbody>
                            <tr class="order-table__row">
                                <th class="order-table__cell--heading">ID de Venta:</th>
                                <td class="order-table__cell">{{ $sale->id }}</td>
                            </tr>
                            <tr class="order-table__row">
                                <th class="order-table__cell--heading">Cliente:</th>
                                <td class="order-table__cell">{{ $sale->user->name }} {{ $sale->user->last_name }}</td>
                            </tr>
                            <tr class="order-table__row">
                                <th class="order-table__cell--heading">Producto:</th>
                                <td class="order-table__cell">{{ $sale->product_name }}</td>
                            </tr>
                            <tr class="order-table__row">
                                <th class="order-table__cell--heading">Tipo de Producto:</th>
                                <td class="order-table__cell">{{ $sale->product->type->name }}</td>
                            </tr>
                            <tr class="order-table__row">
                                <th class="order-table__cell--heading">Fecha de Creación:</th>
                                <td class="order-table__cell">{{ $sale->created_at->format('d/m/Y') }}</td>
                            </tr>
                            <tr class="order-table__row">
                                <th class="order-table__cell--heading">Estado:</th>
                                <td class="order-table__cell">
                                    <span class="badge badge-status badge-{{ $sale->status }}">
                                        {{ $status[$sale->status] ?? ucfirst($sale->status) }}
                                    </span>
                                </td>
                            </tr>
                            <tr class="order-table__row">
                                <th class="order-table__cell--heading">Precio de Venta:</th>
                                <td class="order-table__cell">$ {{ number_format($sale->sale_price, 2) }}</td>
                            </tr>
                            <tr class="order-table__row">
                                <th class="order-table__cell--heading">Detalles:</th>
                                <td class="order-table__cell">
                                    <ul class="order-table__details-list">
                                        <li><strong>Ancho:</strong> {{ $sale->width }} cm</li>
                                        <li><strong>Largo:</strong> {{ $sale->height }} cm</li>
                                        <li><strong>Material:</strong> {{ $sale->product->name }}</li>
                                        <li><strong>Acabado/Corte:</strong> {{ $sale->cut->name }}</li>
                                    </ul>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </section>


        <base-form action="{{ url('admin/ventas/orden/'. $sale->id .'/actualizar') }}"
            method="PUT"
            enctype="multipart/form-data"
            inline-template
            v-cloak
        >
            <form>
                <section class="db-panel">
                    <h3 class="db-panel__title">
                        Ajustes adicionales
                    </h3>

                    <div class="md:row mb-2">
                        <div class="md:col">
                            {{-- nombres --}}
                            <div class="form-control">
                                <label for="status">Estado</label>
                                <select-field
                                    name="status"
                                    v-model="fields.status"
                                    initial="{{ $sale->status }}"
                                    :options="{{ $status }}"
                                >
                                </select-field>                                
                                <field-errors name="name"></field-errors>

                            </div>
                        </div>
                    </div>
                    <div class="md:row">
                        <div class="md:col">
                                {{-- nombres --}}
                            <div class="form-control">
                                <label for="comment">Comentarios adicionales</label>
                                <text-area name="comment" rows="10" cols="50" v-model="fields.comment" maxlength="2000">{{ $sale->comment }}</text-area>
                                <field-errors name="comment"></field-errors>

                            </div>
                        </div>
                    
                    </div>
                </section>
                <div class="text-center">
                    <form-button class="btn--blue--dashboard btn--wide">
                        Actualizar
                    </form-button>
                </div>
            </form>
        </base-form>
    </div>
</section>

@endsection
