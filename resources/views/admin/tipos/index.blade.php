@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Tipos')
@section('tab_title', 'Tipos de impresión | ' . config('app.name'))
@section('description', 'Lista de tipos de impresión')
@section('css_classes', 'dashboard')

@section('content')
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            tipos
        </h1>

        <p class="dashboard-heading__caption">
            Hay {{ $types->count() }} tipos de impresión registrados.
        </p>
    </div>

    <div class="fluid-container mb-16">
        @include('components.alert')
        <section class="db-panel">
            <h3 class="db-panel__title">
                Lista de tipos de impresión
            </h3>

            @if (! $types->count())
                <p class="text-center py-1">
                    Por el momento no hay tipos de impresión registrados.
                </p>
            @else

                <resource-table :breakpoint="800" :model="{{ $types }}" inline-template>
                    <table class="table size-caption mx-auto mb-16 md:table--responsive">
                        <thead>
                            <tr class="table-resource__headings">
                                <th>Nombre</th>
                                <th class="pr-4">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="typeItem in resourceList" class="table-resource__row" :key="typeItem.id">
                                <td data-label="Nombre:">
                                    @{{ typeItem.name }}
                                </td>
                                

                                <td class="table-resource__actions" data-label="Acciones:">
                                    <a class="btn btn-nowrap btn--sm btn--blue table-resource__button mr-2" :href="$root.path + '/admin/tipos/' + typeItem.id + '/editar' ">
                                        <img class="svg-icon" src="{{ url('img/svg/edit.svg')}}">
                                        Editar
                                    </a>
                                    <delete-button class="btn--danger table-resource__button" :url="$root.path + '/admin/tipos/eliminar/' + typeItem.id"
                                        :resource-id="typeItem.id"
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

            @endif

        </section>
    </div>
@endsection
