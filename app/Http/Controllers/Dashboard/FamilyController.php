<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Family;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\FamilyRequest;
use App\DataTables\FamilyDataTable;

class FamilyController extends Controller
{

    public function index(FamilyDataTable $dataTable)
    {
        return $dataTable->render('dashboard.families.index');
    }


    public function create()
    {
        return view('dashboard.families.create');
    }


    public function store(FamilyRequest $request)
    {
        $family = Family::create($request->getSanitized());
        session()->flash('message', 'Family Created Successfully!');
        session()->flash('type', 'success');
        return redirect()->route('dashboard.families.edit', $family);
    }


    public function show(Family $family)
    {
        //
    }


    public function edit(Family $family)
    {
        return view('dashboard.families.edit', compact('family'));
    }


    public function update(FamilyRequest $request, Family $family)
    {
        $family->update($request->getSanitized());
        session()->flash('message', 'Family Updated Successfully!');
        session()->flash('type', 'success');
        return back();
    }


    public function destroy(Family $family)
    {
        $family->delete();
        return response()->json([
            'message' => 'Family Deleted Successfully!'
        ]);
    }
}
