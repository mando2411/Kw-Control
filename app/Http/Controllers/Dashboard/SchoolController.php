<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\School;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\SchoolRequest;
use App\DataTables\SchoolDataTable;

class SchoolController extends Controller
{

    public function index(SchoolDataTable $dataTable)
    {
        return $dataTable->render('dashboard.schools.index');
    }


    public function create()
    {
        return view('dashboard.schools.create');
    }


    public function store(SchoolRequest $request)
    {
        $school = School::create($request->getSanitized());
        session()->flash('message', 'School Created Successfully!');
        session()->flash('type', 'success');
        return redirect()->route('dashboard.schools.edit', $school);
    }


    public function show(School $school)
    {
        //
    }


    public function edit(School $school)
    {
        return view('dashboard.schools.edit', compact('school'));
    }


    public function update(SchoolRequest $request, School $school)
    {
        $school->update($request->getSanitized());
        session()->flash('message', 'School Updated Successfully!');
        session()->flash('type', 'success');
        return back();
    }


    public function destroy(School $school)
    {
        $school->delete();
        return response()->json([
            'message' => 'School Deleted Successfully!'
        ]);
    }
}
