<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Election;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\ElectionRequest;
use App\DataTables\ElectionDataTable;

class ElectionController extends Controller
{

    public function index(ElectionDataTable $dataTable)
    {
        return $dataTable->render('dashboard.elections.index');
    }


    public function create()
    {
        return view('dashboard.elections.create');
    }


    public function store(ElectionRequest $request)
    {
        $election = Election::create($request->getSanitized());
        session()->flash('message', 'تم إنشاء الانتخابات بنجاح');
        session()->flash('type', 'success');
        return redirect()->route('dashboard.elections.edit', $election);
    }


    public function show(Election $election)
    {
        //
    }


    public function edit(Election $election)
    {
        return view('dashboard.elections.edit', compact('election'));
    }


    public function update(ElectionRequest $request, Election $election)
    {
        $election->update($request->getSanitized());
        session()->flash('message', 'تم تحديث الانتخابات بنجاح');
        session()->flash('type', 'success');
        return back();
    }


    public function destroy(Election $election)
    {
        $election->delete();
        return response()->json([
            'message' => 'تم حذف الانتخابات بنجاح'
        ]);
    }
}
