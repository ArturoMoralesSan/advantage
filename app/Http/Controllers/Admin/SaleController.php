<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SaleRequest;
use App\Http\Requests\OrderRequest;
use App\Models\Sale;
use App\Models\User;
use App\Models\Customer;
use App\Models\Type;
use App\Models\Cut;
use App\Models\Product;
use App\Models\Payment;
use App\Models\Inventory;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Auth;
use PDF;
use App\Mail\SaleMail;
use Illuminate\Support\Facades\Mail;

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
            $query = Sale::with('products', 'user')
                ->where('status', $status)
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->orderBy('created_at', 'DESC');

            if (Auth::user()->isCustomer()) {
                $query->where('user_id', Auth::user()->id)->orderBy('created_at', 'desc');
            }
            if ($search) {
                $query->where('product_name', 'LIKE', '%' . $search . '%')->orderBy('created_at', 'desc');
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
        abort_unless(Gate::allows('view.quotations') || Gate::allows('create.quotations'), 403);
        
        $sale = Sale::with(['products.type', 'user'])->find($id);
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
        $assigned_payments = $sale->payments()->get();
        $payments = Payment::pluck('name','id');      

        return view('admin.ventas.orden', compact('sale','status', 'paid', 'payments','assigned_payments'));
    }
    public function orderupdate(OrderRequest $request, $id)
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('create.quotations'), 403);

        $sale = Sale::find($id);


        if($request->status == 'ordered' && $sale->status == 'quoted')
        { 
            foreach($sale->products as $product)
            {
                $inventory = Inventory::with('product')->where('product_id', $product->id)->first();
                $updateQuantity = $inventory->quantity - $product->pivot->quantity_product;
                $inventory->quantity = $updateQuantity;
                $inventory->total = $inventory->product->costo_venta * $updateQuantity;
                $inventory->save();
                Inventory::checkStock($inventory);
            }
            
            $sale->status = $request->status;
            $sale->comment = $request->comment;

            $sale->load('products', 'user');

            $this->sendSaleMail($sale);
        }
        
        if($request->status == 'paid')
        {
            $dateNow     = Carbon::now();
            $dateFormat  = $dateNow->format('Y-m-d');

            $sale->is_paid = true;
            $sale->finish_date = $dateFormat;

        }
        $sale->save();
        $sale->payments()->detach();
        for($i = 1; $i <= $request->payments_count; $i++){
            $sale->payments()->attach($request['payment'.$i.'_pago'], ['cost'=> $request['payment'.$i.'_cost']]);
        }

        alert('Se ha actualizado una orden.');

        return response('', 204, [
            'Redirect-To' => url('admin/ventas/')
        ]);
    }


    public function cloneSale($id)
    {
        $originalSale = Sale::find($id);
        

        $newSale = $originalSale->replicate();
        $newSale->save();

        foreach ($originalSale->products as $product) {
            $newSale->products()->attach($product->id, [
                'product_name'      => $product->pivot->product_name,
                'cut_id'            => $product->pivot->cut_id, 
                'width'             => $product->pivot->width, 
                'height'            => $product->pivot->height, 
                'quantity_product'  => $product->pivot->quantity_product, 
                'base_price'        => $product->pivot->base_price,  
                'profit_percentage' => $product->pivot->profit_percentage
            ]);
        }

        // Obtener la nueva venta con los productos ya clonados para enviarlos al frontend
        $newSale->load('products', 'user');

        return response()->json([
            'success' => true,
            'newItem' => $newSale
        ]);
    }


    public function create()
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('create.quotations'), 403);

        if (Auth::user()->isCustomer()) {
            $users = Customer::where('user_id', Auth::user()->id)
            ->selectRaw("CONCAT(trade_name, ' (',business_name,')') as full_name, user_id")
            ->pluck('full_name', 'user_id');
        } else {
            $users = Customer::selectRaw("CONCAT(trade_name, ' (', business_name,')') as full_name, user_id")
            ->pluck('full_name', 'user_id');       
        }        
        $products = Product::all();
        $types = Type::pluck('name','id');
        $cuts = Cut::all();

        return view('admin.ventas.crear', compact('users', 'products', 'types', 'cuts'));   
    }

    public function save(SaleRequest $request)
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('create.quotations'), 403);
        
        if ($request->sale_id == null) {
            $sale = New Sale;

        } else {
            $sale = Sale::find($request->sale_id);
        }

        $sale->user_id = $request->user_id;
        $sale->comment = $request->comment;
        $sale->save();

        $sale->products()->detach();
        for($i = 1; $i <= $request->products_count; $i++) {
            $sale->products()->attach([
                $request['product'.$i.'_product_id'] => [
                    'product_name'      => $request['product'.$i.'_product_name'],
                    'cut_id'            => $request['product'.$i.'_cut_id'], 
                    'width'             => $request['product'.$i.'_width'], 
                    'height'            => $request['product'.$i.'_height'], 
                    'quantity_product'  => $request['product'.$i.'_quantity_product'], 
                    'base_price'        => $request['product'.$i.'_base_price'],  
                    'profit_percentage' => $request['product'.$i.'_profit_percentage']
                ]
            ]);
            
        }
        $totalSalePrice = $sale->products()->sum('sale_price');
        $totaliva = $sale->products()->sum('iva');
        $subtotal = $totalSalePrice - $totaliva;
        $sale->total_sale_price = $subtotal;
        $sale->iva = $totaliva;
        $sale->total_with_iva = $totalSalePrice;
        $sale->save(); 

        
        if ($request->sale_id == null) {
            alert('Se ha agregado un elemento.');
        } else {
            alert('Se ha editado un elemento.');
        }
        

        return response('', 204, [
            'Redirect-To' => url('admin/ventas/')
        ]);  
    }

    /* public function save(SaleRequest $request)
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
    } */

    public function edit($id)
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('create.quotations'), 403);
        $sale = Sale::with('products.type')->find($id);

        if ($sale->status != 'quoted' || (Auth::user()->isCustomer() && $sale->user_id != Auth::user()->id)) 
        {
            return redirect('admin/ventas/');
        }
        
        if (Auth::user()->isCustomer()) {
            $users = Customer::where('user_id', Auth::user()->id)
            ->selectRaw("CONCAT(trade_name, ' (',business_name,')') as full_name, user_id")
            ->pluck('full_name', 'user_id');
        } else {
            $users = Customer::selectRaw("CONCAT(trade_name, ' (', business_name,')') as full_name, user_id")
            ->pluck('full_name', 'user_id');       
        }           
        $products = Product::all();
        $types = Type::pluck('name','id');
        $cuts = Cut::all();

        $assigned_products = $sale->products()->get();

        return view('admin.ventas.editar', compact('sale','users', 'products', 'types', 'cuts', 'assigned_products'));
    }

    public function delete($id)
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('create.quotations'), 403);

        $quote = Sale::find($id);
        $quote->delete();
        
        return response('', 204);

    }

    public function sendSaleMail($sale)
    {

        $pdf = Pdf::loadView('admin.pdf.notesale', compact('sale'));
        $pdfPath = storage_path("app/public/cotizacion_{$sale->id}.pdf");
        $pdf->save($pdfPath);

        $admins = User::where('role_id', '1')->pluck('email')->toArray();

        if (!empty($sale->user->email)) {
            Mail::to($sale->user->email)
                ->bcc($admins)
                ->send(new SaleMail($sale, $pdfPath));
        } else {
            Mail::bcc($admins)
                ->send(new SaleMail($sale, $pdfPath));
        }

        return back()->with('success', 'Correo enviado con éxito.');
    }
}
