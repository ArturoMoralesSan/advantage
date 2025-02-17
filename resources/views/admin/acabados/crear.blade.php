@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Agregar acabados')
@section('tab_title', 'Agregar acabados | ' . config('app.name'))
@section('description', 'Agregar acabados.')
@section('css_classes', 'dashboard')

@section('content')

<section class="mb-16">
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Agregar Acabado
        </h1>
    </div>

    <div class="fluid-container mb-16">
        <p class="mb-12">
            @include('components.alert')
            <span class="color-link">«</span>
            <a href="{{ url('admin/acabados/') }}">Ver todos los acabados</a>
        </p>

            <base-form action="{{ url('admin/acabados/crear') }}"
                enctype="multipart/form-data"
                inline-template
                v-cloak
            >
                <form>
                    <section class="db-panel">
                        <h3 class="db-panel__title">
                            Datos del acabado
                        </h3>

                        <div class="md:row">
                            <div class="md:col-2/3">
                                {{-- nombres --}}
                                <div class="form-control">
                                    <label for="name">Nombre</label>
                                    <text-field name="name" v-model="fields.name" maxlength="80" initial=""></text-field>
                                    <field-errors name="name"></field-errors>

                                </div>
                            </div>
                            <div class="md:col-1/3">
                                {{-- nombres --}}
                                <div class="form-control">
                                    <label for="cost">Costo</label>
                                    <text-field name="cost" v-model="fields.cost" maxlength="80" initial=""></text-field>
                                    <field-errors name="cost"></field-errors>

                                </div>
                            </div>

                        </div>
                    </section>
                    <div class="text-center">
                        <form-button class="btn--success btn--wide">
                            Crear
                        </form-button>
                    </div>
                </form>
            </base-form>
    </div>
</section>

@endsection
