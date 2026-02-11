<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Appointment;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\AppointmentRequest;
use App\DataTables\AppointmentDataTable;

class AppointmentController extends Controller
{

    public function index(AppointmentDataTable $dataTable)
    {
        return $dataTable->render('dashboard.appointments.index');
    }


    public function create()
    {
        return view('dashboard.appointments.create');
    }


    public function store(AppointmentRequest $request)
    {
        $appointment = Appointment::create($request->getSanitized());
        session()->flash('message', 'Appointment Created Successfully!');
        session()->flash('type', 'success');
        return redirect()->route('dashboard.appointments.edit', $appointment);
    }


    public function show(Appointment $appointment)
    {
        //
    }


    public function edit(Appointment $appointment)
    {
        return view('dashboard.appointments.edit', compact('appointment'));
    }


    public function update(AppointmentRequest $request, Appointment $appointment)
    {
        $appointment->update($request->getSanitized());
        session()->flash('message', 'Appointment Updated Successfully!');
        session()->flash('type', 'success');
        return back();
    }


    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return response()->json([
            'message' => 'Appointment Deleted Successfully!'
        ]);
    }
}
