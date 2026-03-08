<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\School;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\SchoolRequest;
use App\DataTables\SchoolDataTable;
use App\Models\Election;

class SchoolController extends Controller
{

    public function index(SchoolDataTable $dataTable)
    {
        return $dataTable->render('dashboard.schools.index');
    }


    public function create()
    {
        $relations = [
            'elections' => Election::all(),
        ];

        return view('dashboard.schools.create', compact('relations'));
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
        $relations = [
            'elections' => Election::all(),
        ];

        return view('dashboard.schools.edit', compact('school', 'relations'));
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
