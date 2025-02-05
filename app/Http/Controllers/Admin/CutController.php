<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CutRequest;
use Illuminate\Http\Request;
use App\Models\Cut;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class CutController extends Controller
{
    public function index()
    {
        abort_unless(Gate::allows('view.roles') || Gate::allows('create.roles'), 403);
        $cuts = Cut::all();
        return view('admin.cortes.index', compact('cuts'));   
    }

    public function save(CutRequest $request)
    {
        abort_unless(Gate::allows('view.roles') || Gate::allows('edit.roles'), 403);
        
        $cut = new Cut;
        $cut->name = $request->name;
        $cut->key_name = Str::slug($request->name);
        $cut->save();

        alert('Se ha agregado un corte o acabado.');

        return response('', 204, [
            'Redirect-To' => url('admin/cortes/')
        ]);
    }

    public function edit($id)
    {
        abort_unless(Gate::allows('view.roles') || Gate::allows('edit.roles'), 403);
        $cut = Cut::find($id);

        return view('admin.cortes.editar', compact('cut'));
    }


    public function update(CutRequest $request, $id)
    {
        abort_unless(Gate::allows('view.roles') || Gate::allows('edit.roles'), 403);

        $cut = Cut::find($id);
        $cut->name = $request->name;
        $cut->save();

        alert('Se ha actualizado un corte o acabado.');

        return response('', 204, [
            'Redirect-To' => url('admin/cortes/')
        ]);
    }

    public function delete($id)
    {
        abort_unless(Gate::allows('view.roles') || Gate::allows('create.roles'), 403);

        $cut = Cut::find($id);
        $cut->delete();
        
        return response('', 204);

    }
}
