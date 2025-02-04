<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SaleRequest;
use App\Http\Requests\OrderRequest;
use App\Models\Sale;
use App\Models\User;
use App\Models\Type;
use App\Models\Cut;
use App\Models\Product;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Auth;

class SaleController extends Controller
{
    public function index()
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('create.quotations'), 403);

        $actual_month = Carbon::now()->month;
        $actual_year  = Carbon::now()->year;

        $search = \Request('search');
        $month = \Request('month') ?? $actual_month;
        $year  = \Request('year') ?? $actual_year;
        
        $years = collect([]);
        for ($año = 2025; $año <= $actual_year; $año++) {
            $years[$año] = $año;
        }
        $months = collect([
            '1' => 'Enero', 
            '2' => 'Febrero', 
            '3' => 'Marzo', 
            '4' => 'Abril',
            '5' => 'Mayo', 
            '6' => 'Junio', 
            '7' => 'Julio', 
            '8' => 'Agosto',
            '9' => 'Septiembre', 
            '10' => 'Octubre', 
            '11' => 'Noviembre', 
            '12' => 'Diciembre'
        ]);
        

        $statusLabels = [
            'quoted'   => 'Cotizaciones',
            'ordered'  => 'Ordenes pendientes',
            'accepted' => 'Ordenes aceptadas',
            'paid'     => 'Ordenes pagadas',
        ];

        $salesByStatus = collect([]);
        foreach ($statusLabels as $status => $label) {
            $query = Sale::with('product.type', 'user', 'cut')
                ->where('status', $status)
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->orderBy('created_at', 'DESC');

            if (Auth::user()->isCustomer()) {
                $query->where('user_id', Auth::user()->id);
            }
            if ($search) {
                $query->where('product_name', 'LIKE', '%' . $search . '%');
            }
            $paginatedSales = $query->paginate(10);
            $salesByStatus[$status] = [
                'items' => collect($paginatedSales->items()),
                'links' => $paginatedSales->links('layout.pagination') 
            ];
        }

        return view('admin.ventas.index',compact('years', 'months', 'actual_month', 'actual_year', 'salesByStatus','statusLabels'));
    }

   public function order($id) 
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('edit.quotations'), 403);
        
        $sale = Sale::with('product.type')->find($id);

        if (Auth::user()->isCustomer()) 
        {
            return redirect('admin/ventas/');
        }

        $status = collect([
            'quoted'   => 'Cotizada',
            'ordered'  => 'Ordenada',
            'accepted' => 'Aceptada',
            'paid'     => 'Pagada', 
        ]);

        $paid = collect([
            '1' => 'Pagado',
            '0' => 'No pagado', 
        ]);       

        return view('admin.ventas.orden', compact('sale','status', 'paid'));
    }
    public function orderupdate(OrderRequest $request, $id)
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('edit.quotations'), 403);

        $sale = Sale::find($id);
        $sale->status = $request->status;
        $sale->comment= $request->comment;
        $sale->save();

        alert('Se ha actualizado una orden.');

        return response('', 204, [
            'Redirect-To' => url('admin/ventas/')
        ]);
    }


    public function create()
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('create.quotations'), 403);
        $status = collect([
            'quoted'   => 'Cotización',
            'ordered'  => 'Orden',
            'accepted' => 'Aceptar',
            'paid'     => 'Pagar', 
        ]);

        $paid = collect([
            '1' => 'Pagado',
            '0' => 'No pagado', 
        ]);

        if (Auth::user()->isCustomer()) {
            $users = User::where('id', Auth::user()->id)
            ->selectRaw("CONCAT(name, ' ', last_name) as full_name, id")
            ->pluck('full_name', 'id');
        } else {
            $users = User::selectRaw("CONCAT(name, ' ', last_name) as full_name, id")
            ->pluck('full_name', 'id');        
        }        
        $products = Product::all();
        $types = Type::pluck('name','id');
        $cuts = Cut::pluck('name','id');

        return view('admin.ventas.crear', compact('users', 'products', 'types', 'cuts', 'status', 'paid'));   
    }

    public function save(SaleRequest $request)
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('edit.quotations'), 403);
        
        $sale = new Sale;
        $sale->user_id = $request->user_id;
        $sale->product_name = $request->product_name;
        $sale->product_id = $request->product_id;
        $sale->cut_id = $request->cut_id;
        $sale->width = $request->width;
        $sale->height = $request->height;
        $sale->base_price = $request->base_price;
        $sale->profit_percentage = $request->profit_percentage;
        $sale->save();

        alert('Se ha agregado una cotización.');

        return response('', 204, [
            'Redirect-To' => url('admin/ventas/')
        ]);
    }

    public function edit($id)
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('edit.quotations'), 403);
        $sale = Sale::with('product.type')->find($id);

        if ($sale->status != 'quoted' || (Auth::user()->isCustomer() && $sale->user_id != Auth::user()->id)) 
        {
            return redirect('admin/ventas/');
        }
        
        $status = collect([
            'quoted'   => 'Cotización',
            'ordered'  => 'Orden',
            'accepted' => 'Aceptar',
            'paid'     => 'Pagar', 
        ]);

        $paid = collect([
            '1' => 'Pagado',
            '0' => 'No pagado', 
        ]);

        if (!Auth::user()->isSuperAdmin()) {
            $users = User::where('id', Auth::user()->id)
            ->selectRaw("CONCAT(name, ' ', last_name) as full_name, id")
            ->pluck('full_name', 'id');
        } else {
            $users = User::selectRaw("CONCAT(name, ' ', last_name) as full_name, id")
            ->pluck('full_name', 'id');        
        }        
        $products = Product::all();
        $types = Type::pluck('name','id');
        $cuts = Cut::pluck('name','id');


        return view('admin.ventas.editar', compact('sale','users', 'products', 'types', 'cuts', 'status', 'paid'));
    }


    public function update(SaleRequest $request, $id)
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('edit.quotations'), 403);

        $sale = Sale::find($id);
        $sale->user_id = $request->user_id;
        $sale->product_name = $request->product_name;
        $sale->product_id = $request->product_id;
        $sale->cut_id = $request->cut_id;
        $sale->width = $request->width;
        $sale->height = $request->height;
        $sale->base_price = $request->base_price;
        $sale->profit_percentage = $request->profit_percentage;
        $sale->save();

        alert('Se ha actualizado una cotización.');

        return response('', 204, [
            'Redirect-To' => url('admin/ventas/')
        ]);
    }

    public function delete($id)
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('create.quotations'), 403);

        $quote = Sale::find($id);
        $quote->delete();
        
        return response('', 204);

    }
}
