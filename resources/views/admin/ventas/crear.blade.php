@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Agregar cotización')
@section('tab_title', 'Agregar cotización | ' . config('app.name'))
@section('description', 'Agregar cotización.')
@section('css_classes', 'dashboard')

@section('content')

<section class="mb-16">
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Agregar venta
        </h1>
    </div>

    <div class="fluid-container mb-16">
        <p class="mb-12">
            @include('components.alert')
            <span class="color-link">«</span>
            <a href="{{ url('admin/ventas/') }}">Ver todas las ventas</a>
        </p>

        <product-registration-form
            action="{{ url('admin/ventas/crear') }}"
            :sale="{{ json_encode((object)[]) }}"
            :users="{{ $users }}"
            :cuts="{{ $cuts }}"
            :types="{{ $types }}"
            :product="30"
            :min-product="1"
            :products="{{ $products }}"
            :assigned-products="[]"
            :product-data="[]"
        >
        </product-registration-form>
    
    </div>
</section>

@endsection
