<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Beneficiary;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\BeneficiaryRequest;
use App\DataTables\BeneficiaryDataTable;

class BeneficiaryController extends Controller
{

    public function index(BeneficiaryDataTable $dataTable)
    {
        return $dataTable->render('dashboard.beneficiaries.index');
    }


    public function create()
    {
        return view('dashboard.beneficiaries.create');
    }


    public function store(BeneficiaryRequest $request)
    {
        $beneficiary = Beneficiary::create($request->getSanitized());
        session()->flash('message', 'Beneficiary Created Successfully!');
        session()->flash('type', 'success');
        return redirect()->route('dashboard.beneficiaries.edit', $beneficiary);
    }


    public function show(Beneficiary $beneficiary)
    {
        //
    }


    public function edit(Beneficiary $beneficiary)
    {
        return view('dashboard.beneficiaries.edit', compact('beneficiary'));
    }


    public function update(BeneficiaryRequest $request, Beneficiary $beneficiary)
    {
        $beneficiary->update($request->getSanitized());
        session()->flash('message', 'Beneficiary Updated Successfully!');
        session()->flash('type', 'success');
        return back();
    }


    public function destroy(Beneficiary $beneficiary)
    {
        $beneficiary->delete();
        return response()->json([
            'message' => 'Beneficiary Deleted Successfully!'
        ]);
    }
}
