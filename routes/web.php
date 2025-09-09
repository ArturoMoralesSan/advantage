<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\CutController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\TypeController;
use App\Http\Controllers\Admin\MeasureController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Admin\PdfController;
use App\Http\Controllers\Admin\RaceController;
use App\Http\Controllers\Admin\RaceRegistrationController;


use Illuminate\Support\Facades\Auth;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm']);
Auth::routes(['register' => false]);
Route::get('login/{provider}', [LoginController::class, 'redirectToProvider']);
Route::get('{provider}/callback', [LoginController::class,'handleProviderCallback']);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('notas/{id}', [PdfController::class, 'pdfSale']);

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'noCache']], function() {
    Route::get('/', [DashboardController::class, 'index']);

    Route::get('pdf/{id}', [PdfController::class, 'pdf']);

    Route::get('pdf-carrera/{id}', [PdfController::class, 'pdfRace']);

    Route::get('pdf-egreso/{id}', [PdfController::class, 'pdfEgreso']);
    Route::get('pdf-gasto/{id}', [PdfController::class, 'pdfGasto']);
        
    Route::post('ultimo_rx/{id}', [ServiceController::class, 'getrx']);

    //perfil
    Route::view('perfil/editar','admin.editar-perfil');
    Route::put('perfil/editar', [ProfileController::class, 'update']);

    //Password
    Route::view('perfil/cambiar-contrasena', 'admin.cambiar-contrasena');
    Route::post('perfil/cambiar-contrasena', [PasswordController::class, 'update']);

    // usuarios
    Route::get('usuarios', [UserController::class, 'index']);
    Route::get('agregar-usuario', [UserController::class, 'create']);
    Route::post('usuarios/crear', [UserController::class, 'save']);
    Route::get('usuarios/{id}/editar',[UserController::class, 'edit']);
    Route::put('usuarios/{id}/actualizar',[UserController::class, 'update']);
    Route::delete('usuarios/eliminar/{id}',[UserController::class, 'destroy']);


    // clientes
    Route::get('clientes', [CustomerController::class, 'index']);
    Route::get('agregar-cliente', [CustomerController::class, 'create']);
    Route::post('clientes/crear', [CustomerController::class, 'save']);
    Route::get('clientes/{id}/editar',[CustomerController::class, 'edit']);
    Route::put('clientes/{id}/actualizar',[CustomerController::class, 'update']);
    Route::delete('clientes/eliminar/{id}',[CustomerController::class, 'destroy']);


    //permisos
    Route::get('permisos', [PermissionController::class, 'index']);
    Route::view('agregar-permisos', 'admin.permisos.crear');
    Route::post('permiso/crear', [PermissionController::class, 'save']);
    Route::get('permiso/{id}/editar', [PermissionController::class, 'edit']);
    Route::put('permiso/{id}/actualizar', [PermissionController::class, 'update']);
    Route::delete('permiso/eliminar/{id}', [PermissionController::class, 'delete']);

    //roles
    Route::get('roles', [RoleController::class, 'index']);
    Route::get('agregar-roles', [RoleController::class, 'create']);
    Route::post('roles/crear', [RoleController::class, 'save']);
    Route::get('roles/{id}/editar', [RoleController::class, 'edit']);
    Route::put('roles/{id}/actualizar', [RoleController::class, 'update']);
    Route::delete('roles/eliminar/{id}', [RoleController::class, 'delete']);

    //Categorias
    Route::get('categorias', [TypeController::class, 'index']);
    Route::view('agregar-categoria', 'admin.categorias.crear');
    Route::post('categorias/crear', [TypeController::class, 'save']);
    Route::get('categorias/{id}/editar', [TypeController::class, 'edit']);
    Route::put('categorias/{id}/actualizar', [TypeController::class, 'update']);
    Route::delete('categorias/eliminar/{id}', [TypeController::class, 'delete']);

    //Medidas
    Route::get('medidas', [MeasureController::class, 'index']);
    Route::view('agregar-medidas', 'admin.medidas.crear');
    Route::post('medidas/crear', [MeasureController::class, 'save']);
    Route::get('medidas/{id}/editar', [MeasureController::class, 'edit']);
    Route::put('medidas/{id}/actualizar', [MeasureController::class, 'update']);
    Route::delete('medidas/eliminar/{id}', [MeasureController::class, 'delete']);

    //Productos
    Route::get('productos', [ProductController::class, 'index']);
    Route::get('agregar-productos', [ProductController::class, 'create']);
    Route::post('productos/clonar/{id}', [ProductController::class, 'cloneProduct']);
    Route::post('productos/crear', [ProductController::class, 'save']);
    Route::get('productos/{id}/editar', [ProductController::class, 'edit']);
    Route::put('productos/{id}/actualizar', [ProductController::class, 'update']);
    Route::delete('productos/eliminar/{id}', [ProductController::class, 'delete']);

    //inventario
    Route::get('inventario', [InventoryController::class, 'index']);
    Route::get('agregar-inventario', [InventoryController::class, 'create']);
   // Route::view('agregar-inventario', 'admin.inventario.crear');
    Route::post('inventario/crear', [InventoryController::class, 'save']);
    Route::get('inventario/{id}/editar', [InventoryController::class, 'edit']);
    Route::put('inventario/{id}/actualizar', [InventoryController::class, 'update']);
    Route::delete('inventario/eliminar/{id}', [InventoryController::class, 'delete']);

    //Ventas
    Route::get('ventas', [SaleController::class, 'index']);
    Route::get('ventas/orden/{id}', [SaleController::class, 'order']);
    Route::put('ventas/orden/{id}/actualizar', [SaleController::class, 'orderupdate']);
    Route::post('ventas/clonar/{id}', [SaleController::class, 'cloneSale']);

    Route::get('agregar-venta', [SaleController::class, 'create']);
    Route::post('ventas/crear', [SaleController::class, 'save']);
    Route::get('ventas/{id}/editar', [SaleController::class, 'edit']);
    Route::put('ventas/{id}/actualizar', [SaleController::class, 'update']);
    Route::delete('ventas/eliminar/{id}', [SaleController::class, 'delete']);

    //Acabados
    Route::get('acabados', [CutController::class, 'index']);
    Route::view('agregar-acabado', 'admin.acabados.crear');
    Route::post('acabados/crear', [CutController::class, 'save']);
    Route::get('acabados/{id}/editar', [CutController::class, 'edit']);
    Route::put('acabados/{id}/actualizar', [CutController::class, 'update']);
    Route::delete('acabados/eliminar/{id}', [CutController::class, 'delete']);

    //Tipo de pago
    Route::get('tipos-pagos', [PaymentController::class, 'index']);
    Route::view('agregar-tipo-pago', 'admin.pagos.crear');
    Route::post('tipos-pagos/crear', [PaymentController::class, 'save']);
    Route::get('tipos-pagos/{id}/editar', [PaymentController::class, 'edit']);
    Route::put('tipos-pagos/{id}/actualizar', [PaymentController::class, 'update']);
    Route::delete('tipos-pagos/eliminar/{id}', [PaymentController::class, 'delete']);

    //Tipo de gastos
    Route::get('tipos-gastos', [TypeExpenseController::class, 'index']);
    Route::view('agregar-tipos-gastos', 'admin.tipogastos.crear');
    Route::post('tipos-gastos/crear', [TypeExpenseController::class, 'save']);
    Route::get('tipos-gastos/{id}/editar', [TypeExpenseController::class, 'edit']);
    Route::put('tipos-gastos/{id}/actualizar', [TypeExpenseController::class, 'update']);
    Route::delete('tipos-gastos/eliminar/{id}', [TypeExpenseController::class, 'delete']);

    //Estudios
    Route::get('estudios', [StudyController::class, 'index']);
    Route::view('agregar-estudios', 'admin.estudios.crear');
    Route::post('estudios/crear', [StudyController::class, 'save']);
    Route::get('estudios/{id}/editar', [StudyController::class, 'edit']);
    Route::put('estudios/{id}/actualizar', [StudyController::class, 'update']);
    Route::delete('estudios/eliminar/{id}', [StudyController::class, 'delete']);
    
    // Estadísticas
    Route::get('estadisticas-servicios', [StatisticsController::class, 'index']);

    Route::get('estadisticas-gastos', [StatisticsController::class, 'gastos']);

    //Usuarios
    Route::get('usuarios', [UserController::class, 'index']);

    //Password
    Route::view('cambiar-contrasena', 'principal.cambiar-contrasena');
    Route::post('cambiar-contrasena', 'Auth\PasswordController@update');

    //Carreras
    Route::get('carreras', [RaceController::class, 'index']);
    Route::view('carreras/crear', 'admin.carreras.crear');
    Route::post('carreras/crear', [RaceController::class, 'save']);
    Route::get('carreras/{id}/editar', [RaceController::class, 'edit']);
    Route::put('carreras/{id}/actualizar', [RaceController::class, 'update']);
    Route::delete('carreras/eliminar/{id}', [RaceController::class, 'delete']);

    //Participantes
    Route::get('carrera-participantes', [RaceRegistrationController::class, 'index']);
    Route::get('carrera-participantes/crear', [RaceRegistrationController::class, 'create']);
    Route::post('carrera-participantes/crear', [RaceRegistrationController::class, 'save']);
    Route::get('carrera-participantes/{id}/editar', [RaceRegistrationController::class, 'edit']);
    Route::put('carrera-participantes/{id}/actualizar', [RaceRegistrationController::class, 'update']);
    Route::delete('carrera-participantes/eliminar/{id}', [RaceRegistrationController::class, 'delete']);

    Route::get('carrera-estadistica', [RaceRegistrationController::class, 'statics']);

});