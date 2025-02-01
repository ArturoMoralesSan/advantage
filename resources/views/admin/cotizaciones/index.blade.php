@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'cotizaciones')
@section('tab_title', 'cotizaciones | ' . config('app.name'))
@section('description', 'Lista de cotizaciones.')
@section('css_classes', 'dashboard')

@section('content')
    <div class="dashboard-heading">
        <div class="md:row justify-between">
            <div class="md:col-1/2">
                <h1 class="dashboard-heading__title">
                    Cotizaciones
                </h1>
                <p class="dashboard-heading__caption">
                    Hay {{ $quotes->count() }} cotizaciones registrados.
                </p>
            </div>
            <div class="md:col-1/2 d-flex items-center">
                <div class="row">
                    <div class="md:col-1/2">
                        <label for="month">Mes</label>
                        <select-filter
                            name="month"
                            selected="{{ app('request')->input('month') ? app('request')->input('month') : $actual_month }}"
                            :options="{{ $months }}"
                        >
                        </select-filter>
                    </div>
                    <div class="md:col-1/2">
                        <label for="year">Año</label>
                        <select-filter
                            name="year"
                            selected="{{ app('request')->input('year') ? app('request')->input('year'): $actual_year }}"
                            :options="{{ $years }}"
                        >
                        </select-filter>
                    </div>
                </div>
            </div>
        </div>
        
    </div>

    <div class="fluid-container mb-16">
        @include('components.alert')

        
        <form-search 
            selected="{{ app('request')->input('search') }}"
        >
        <template slot="svg-search">
            <img class="search-form_icon" src="{{ url('img/svg/search.svg') }}" alt="">
        </template>
        </form-search>
           
        <section class="db-panel">
            <h3 class="db-panel__title">
                Lista de cotizaciones
            </h3>

            @if (! $quotes->count())
                <p class="text-center py-1">
                    Por el momento no hay cotizaciones registradas.
                </p>
            @else

                <resource-table :breakpoint="800" :model="{{ $quotesItems }}" inline-template>
                    <table class="table size-caption mx-auto mb-16 md:table--responsive">
                        <thead>
                            <tr class="table-resource__headings">
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Cliente</th>
                                <th>Producto</th>
                                <th>Tipo</th>
                                <th>Características</th>
                                <th>Costo</th>
                                <th>Utilidad</th>
                                <th>Precio de venta</th>
                                <th class="pr-4">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="quoteItem in resourceList" class="table-resource__row" :key="quoteItem.id">
                                <td data-label="Fecha:">
                                    @{{ quoteItem.formated_date }}
                                </td>
                                <td data-label="Hora:">
                                    @{{ quoteItem.hour }}
                                </td>
                                <td data-label="Cliente:">
                                    @{{ quoteItem.user.name }}
                                </td>
                                <td data-label="Producto:">
                                    @{{ quoteItem.product_name }}
                                </td>
                                <td data-label="tipo:">
                                    @{{ quoteItem.product.type.name }}
                                </td>
                                <td data-label="Características:">
                                    Ancho: @{{ quoteItem.width }} <br>
                                    Largo: @{{ quoteItem.height }} <br>
                                    Material: @{{ quoteItem.product.name }} <br>
                                </td>
                                <td data-label="Costo:">
                                    $@{{ quoteItem.base_price }}
                                </td>
                                <td data-label="Porcentaje de utilidad:">
                                    @{{ quoteItem.profit_percentage }} %
                                </td>
                                <td data-label="Precio de venta:">
                                    $ @{{ quoteItem.sale_price }}
                                </td>
                                <td data-label="Nota:">
                                    <a class="btn btn-nowrap btn--sm btn--primary table-resource__button mr-2" :href="$root.path + '/notas/' + quoteItem.id">
                                        PDF
                                    </a>
                                </td>

                                <td class="table-resource__actions" data-label="Acciones:">
                                    <a class="btn btn-nowrap btn--sm btn--blue table-resource__button mr-2" :href="$root.path + '/admin/cotizaciones/' + quoteItem.id + '/editar' ">
                                        <img class="svg-icon" src="{{ url('img/svg/edit.svg')}}">
                                        Editar
                                    </a>
                                    <delete-button class="btn--danger table-resource__button" :url="$root.path + '/admin/cotizaciones/eliminar/' + quoteItem.id"
                                        :resource-id="quoteItem.id"
                                        :options="{ onDelete: onResourceDelete }"
                                    >
                                        <img class="svg-icon" src="{{ url('img/svg/trash.svg')}}">
                                        Eliminar
                                    </delete-button>
                                </td>
                            </tr>
                        </tbody>

                    </table>

                </resource-table>

                {{ $quotes->links('layout.pagination')}}
                

            @endif

        </section>
    </div>
@endsection
