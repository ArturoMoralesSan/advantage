<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RaceRegistrationRequest;
use Illuminate\Http\Request;
use App\Models\RaceRegistration;
use App\Models\Race;
use App\Models\Branch;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Auth;

class RaceRegistrationController extends Controller
{
    public function statics()
    {
        abort_unless(Gate::allows('view.races') || Gate::allows('create.races'), 403);
        $registers = RaceRegistration::with('branch','race')
        ->orderBy('created_at','DESC');

        if (!Auth::user()->isSuperAdmin()) {
            $registers = $registers
            ->where('branch_id', Auth::user()->branch_id);
        } 
        $amountByRegisters = $registers->sum('cost');
        $ordersAll = $registers->count();

        $totalPerType = $registers->get()
        ->groupBy('type')->map(function ($register) {
            return $register->count();
        });

        $totalPerSize = $registers->get()
        ->groupBy('size')->map(function ($register) {
            return $register->count();
        });
        $totalPerSex = $registers->get()
        ->groupBy('sex')->map(function ($register) {
            return $register->count();
        });

        $registersall = $registers->get()
        ->groupBy('branch_id')
        ->map(function ($group, $branch_id) {
            $total_amount = $group->sum('amount');
            $total_registers = $group->count();
            $branch = $group->first()->branch;
            $branch_name = $branch ? $branch->name : 'Unknown Branch';
    
            return [
                'id' => $branch_id,
                'branch_name' => $branch_name,
                'total_amount' => $total_amount,
                'total_registers' => $total_registers,
            ];
        });
    
        return view('admin.estadisticas.carrera', compact('ordersAll', 'amountByRegisters', 'totalPerType', 'totalPerSize', 'totalPerSex', 'registersall'));   
    }
    public function index()
    {
        abort_unless(Gate::allows('view.races') || Gate::allows('create.races'), 403);
        $registers = RaceRegistration::with('branch','race')->orderBy('created_at','DESC');

        if (!Auth::user()->isSuperAdmin()) {
            $registers = $registers->where('branch_id', Auth::user()->branch_id);
        } 

        $registers = $registers->paginate(20);
        $registers->getCollection()->transform(function ($register) {
            $register->branch->setAppends([]);
            return $register;
        });
        $registersItems = Collect($registers->items());
        return view('admin.registro.index', compact('registers', 'registersItems'));   
    }

    public function create()
    {
        abort_unless(Gate::allows('view.races') || Gate::allows('create.races'), 403);
        $races = Race::pluck('name','id');

        if (!Auth::user()->isSuperAdmin()) {
            $branches = Branch::where('id', Auth::user()->branch_id)->pluck('name','id');
        } else {
            $branches = Branch::pluck('name','id');
        }      
        return view('admin.registro.crear', compact('races', 'branches'));   
    }

    public function save(RaceRegistrationRequest $request)
    {
        abort_unless(Gate::allows('view.races') || Gate::allows('edit.races'), 403);
        
        $registration = new RaceRegistration;
        $registration->race_id = $request->race_id;
        $registration->name = $request->name;
        $registration->age = $request->age;
        $registration->sex = $request->sex;
        $registration->size = $request->size;
        $registration->cellphone = $request->cellphone;
        $registration->type = $request->type;
        $registration->cost = $request->cost;
        $registration->branch_id = $request->branch_id;
        $registration->save();

        alert('Se ha agregado un participante.');

        return response('', 204, [
            'Redirect-To' => url('admin/carrera-participantes/')
        ]);
    }

    public function edit($id)
    {
        abort_unless(Gate::allows('view.races') || Gate::allows('edit.races'), 403);
        $register = RaceRegistration::find($id);
        $races = Race::pluck('name','id');

        if (!Auth::user()->isSuperAdmin()) {
            $branches = Branch::where('id', Auth::user()->branch_id)->pluck('name','id');
        } else {
            $branches = Branch::pluck('name','id');
        }   
        return view('admin.registro.editar', compact('register', 'races', 'branches'));
    }


    public function update(RaceRegistrationRequest $request, $id)
    {
        abort_unless(Gate::allows('view.races') || Gate::allows('edit.races'), 403);

        $registration = RaceRegistration::find($id);
        $registration->race_id = $request->race_id;
        $registration->name = $request->name;
        $registration->age = $request->age;
        $registration->sex = $request->sex;
        $registration->size = $request->size;
        $registration->cellphone = $request->cellphone;
        $registration->type = $request->type;
        $registration->cost = $request->cost;
        $registration->branch_id = $request->branch_id;
        $registration->save();
        

        alert('Se ha actualizado un participante.');

        return response('', 204, [
            'Redirect-To' => url('admin/carrera-participantes/')
        ]);
    }

    public function delete($id)
    {
        abort_unless(Gate::allows('view.races') || Gate::allows('create.races'), 403);

        $race = RaceRegistration::find($id);
        $race->delete();
        
        return response('', 204);

    }
}
