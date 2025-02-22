<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Type;
use App\Models\Measure;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        
        abort_unless(Gate::allows('view.products') || Gate::allows('create.products'), 403);
        $search = \Request('search');

        $query = Product::with('type', 'measure')->orderBy('created_at', 'desc');
        if ($search) {
            $query->where('name', 'LIKE', '%' . $search . '%')->orderBy('created_at', 'desc');
        }
        $query->get()->each->setAppends([]);
        $paginatedProducts = $query->paginate(10);
        $ProductsItems = Collect($paginatedProducts->items());
        $links = $paginatedProducts->links('layout.pagination');

        return view('admin.productos.index', compact('ProductsItems', 'links'));   
    }

    public function create()
    {
        abort_unless(Gate::allows('view.products') || Gate::allows('create.products'), 403);
        $types = Type::pluck('name','id');
        $measures = Measure::pluck('name','id');

        return view('admin.productos.crear', compact('types', 'measures'));   
    }

    public function save(ProductRequest $request)
    {
        abort_unless(Gate::allows('view.products') || Gate::allows('edit.products'), 403);
        
        $product = new Product;
        $product->name           = $request->name;
        $product->type_id        = $request->type_id;
        $product->measure_id     = $request->measure_id;
        $product->description    = $request->description;
        $product->vinil_cost     = $request->vinil_cost;
        $product->impresion_cost = $request->impresion_cost;
        $product->indirect_cost  = $request->indirect_cost;
        $product->subtotal       = $request->subtotal;
        $product->iva            = $request->iva;
        $product->costo_total    = $request->costo_total;
        $product->utility        = $request->utility;
        $product->costo_venta    = $request->costo_venta;
        $product->save();

        alert('Se ha agregado un producto.');

        return response('', 204, [
            'Redirect-To' => url('admin/productos/')
        ]);
    }

    public function cloneProduct($id)
    {
        $originalProduct = Product::find($id);
        

        $newProduct = $originalProduct->replicate();
        $newProduct->save();


        // Obtener la nueva venta con los productos ya clonados para enviarlos al frontend
        $newProduct->load('type', 'measure');

        return response()->json([
            'success' => true,
            'newItem' => $newProduct
        ]);
    }

    public function edit($id)
    {
        abort_unless(Gate::allows('view.products') || Gate::allows('edit.products'), 403);
        $product = Product::find($id);
        $types = Type::pluck('name','id');
        $measures = Measure::pluck('name','id');
        return view('admin.productos.editar', compact('product', 'types', 'measures'));
    }


    public function update(ProductRequest $request, $id)
    {
        abort_unless(Gate::allows('view.products') || Gate::allows('edit.products'), 403);

        $product = Product::find($id);
        $product->name           = $request->name;
        $product->type_id        = $request->type_id;
        $product->measure_id     = $request->measure_id;
        $product->description    = $request->description;
        $product->vinil_cost     = $request->vinil_cost;
        $product->impresion_cost = $request->impresion_cost;
        $product->indirect_cost  = $request->indirect_cost;
        $product->subtotal       = $request->subtotal;
        $product->iva            = $request->iva;
        $product->costo_total    = $request->costo_total;
        $product->utility        = $request->utility;
        $product->costo_venta    = $request->costo_venta;
        $product->save();

        alert('Se ha actualizado un producto.');

        return response('', 204, [
            'Redirect-To' => url('admin/productos/')
        ]);
    }

    public function delete($id)
    {
        abort_unless(Gate::allows('view.products') || Gate::allows('create.products'), 403);

        $product = Product::find($id);
        $product->delete();

        return response('', 204);

    }
}
