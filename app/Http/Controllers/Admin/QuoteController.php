<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\QuoteRequest;
use App\Models\Quote;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Auth;

class QuoteController extends Controller
{
    public function index()
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('create.quotations'), 403);
        $actual_month = Carbon::now()->month;
        $actual_year  = Carbon::now()->year;

        $years = collect([]);

        $año_actual = Carbon::now()->year;

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

        $search = \Request('search');

        $month = \Request('month') != null ? \Request('month') : $actual_month;
        $year  = \Request('year') != null ? \Request('year') : $actual_year;

        $quotes = Quote::with('product.type', 'user')
        ->whereMonth('created_at', $month)
        ->whereYear('created_at', $year)
        ->orderBy('created_at','DESC');
        

        if (!Auth::user()->isSuperAdmin()) {
            $quotes = $quotes->where('user_id', Auth::user()->id);
        }
        
        if($search) {
            $quotes = $quotes->where('product_name', 'LIKE', '%'.$search.'%');
        }

        $quotes = $quotes->paginate(20);

        
        $quotesItems = Collect($quotes->items());

        return view('admin.cotizaciones.index', compact('quotes', 'quotesItems', 'years', 'months', 'actual_month', 'actual_year'));   
    }

    public function create()
    {
        abort_unless(Gate::allows('view.services') || Gate::allows('create.services'), 403);
        if (!Auth::user()->isSuperAdmin()) {
            $users = User::where('id', Auth::user()->id)
            ->selectRaw("CONCAT(name, ' ', last_name) as full_name, id")
            ->pluck('full_name', 'id');
        } else {
            $users = User::selectRaw("CONCAT(name, ' ', last_name) as full_name, id")
            ->pluck('full_name', 'id');        
        }        
        $product = Product::pluck('name','id');
        return view('admin.cotizaciones.crear', compact('users', 'product'));   
    }

    public function save(RoleRequest $request)
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('edit.quotations'), 403);
        
        $role = new Quote;
        $role->name = $request->name;
        $role->key_name = Str::slug($request->name);
        $role->save();

        alert('Se ha agregado una cotización.');

        return response('', 204, [
            'Redirect-To' => url('admin/cotizaciones/')
        ]);
    }

    public function edit($id)
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('edit.quotations'), 403);
        $quote = Quote::find($id);

        return view('admin.cotizaciones.editar', compact('quote'));
    }


    public function update(QuoteRequest $request, $id)
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('edit.quotations'), 403);

        $role = Quote::find($id);
        $role->name = $request->name;
        $role->save();

        alert('Se ha actualizado un permiso.');

        return response('', 204, [
            'Redirect-To' => url('admin/cotizaciones/')
        ]);
    }

    public function delete($id)
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('create.quotations'), 403);

        $quote = Quote::find($id);
        $quote->delete();
        
        return response('', 204);

    }
}
