<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryRequest;
use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class InventoryController extends Controller
{
    public function index()
    {
        abort_unless(Gate::allows('view.inventories') || Gate::allows('create.inventories'), 403);

        $inventories = Inventory::with('product')->paginate(10);

        $inventoriesItems = collect($inventories->items());
        return view('admin.inventario.index', compact('inventories', 'inventoriesItems'));   
    }

    public function create()
    {
        abort_unless(Gate::allows('view.inventories') || Gate::allows('edit.inventories'), 403);
        $products = Product::pluck('name', 'id');

        return view('admin.inventario.crear', compact('products'));
    }

    public function save(InventoryRequest $request)
    {
        abort_unless(Gate::allows('view.inventories') || Gate::allows('edit.inventories'), 403);
        $product = Product::find($request->product_id);
        $total = $product->costo_venta * $request->quantity;
        $inventory = new Inventory;
        $inventory->product_id = $request->product_id;
        $inventory->quantity   = $request->quantity;
        $inventory->total      = $total;
        $inventory->save();

        alert('Se ha agregado un elemento al inventario.');

        return response('', 204, [
            'Redirect-To' => url('admin/inventario/')
        ]);
    }

    public function edit($id)
    {
        abort_unless(Gate::allows('view.inventories') || Gate::allows('edit.inventories'), 403);
        $inventory = Inventory::find($id);
        $products = Product::pluck('name', 'id');
        return view('admin.inventario.editar', compact('inventory', 'products'));
    }


    public function update(InventoryRequest $request, $id)
    {
        abort_unless(Gate::allows('view.inventories') || Gate::allows('edit.inventories'), 403);
        $product = Product::find($request->product_id);
        $total = $product->costo_venta * $request->quantity;
        $inventory = Inventory::find($id);
        $inventory->product_id = $request->product_id;
        $inventory->quantity   = $request->quantity;
        $inventory->total      = $total;
        $inventory->save();

        alert('Se ha actualizado un elemento en el inventario.');

        return response('', 204, [
            'Redirect-To' => url('admin/inventario/')
        ]);
    }

    public function delete($id)
    {
        abort_unless(Gate::allows('view.inventories') || Gate::allows('create.inventories'), 403);

        $inventory = Inventory::find($id);
        $inventory->delete();
        
        return response('', 204);

    }
}
