<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        abort_unless(Gate::allows('view.products') || Gate::allows('create.products'), 403);
        $products = Product::all()->each->setAppends([]);
        return view('admin.productos.index', compact('products'));   
    }

    public function save(ProductRequest $request)
    {
        abort_unless(Gate::allows('view.products') || Gate::allows('edit.products'), 403);
        
        $product = new Product;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->save();

        alert('Se ha agregado un nuevo producto.');

        return response('', 204, [
            'Redirect-To' => url('admin/productos/')
        ]);
    }

    public function edit($id)
    {
        abort_unless(Gate::allows('view.products') || Gate::allows('edit.products'), 403);
        $product = Product::find($id);
        return view('admin.productos.editar', compact('product'));
    }


    public function update(ProductRequest $request, $id)
    {
        abort_unless(Gate::allows('view.products') || Gate::allows('edit.products'), 403);

        $product = Product::find($id);
        $product->name = $request->name;
        $product->description = $request->description;;
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
