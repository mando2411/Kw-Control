<?php

namespace App\Services;

use App\Models\Election;
use App\Models\Voter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use ArPHP\Arabic;
use Illuminate\Support\Facades\File;

use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;


class ReportService
{
    public function getReports(Request $request)
    {
        $voters = $this->getVoters($request);
        if($request->type == "Excel"){
            return $voters;
        }

// Generate a unique filename
        $token = bin2hex(random_bytes(16));
        $VoterFile = $token . ".pdf";
        $VoterPath = storage_path("app/public/Reports/" . $VoterFile); // Correct storage path

// Ensure the Reports directory exists
        if (!File::exists(storage_path('app/public/Reports'))) {
            File::makeDirectory(storage_path('app/public/Reports'), 0755, true);
        }

// Generate the PDF
        $html = view('dashboard.exports.report', [
            'voters' => $voters,
            'mode' => 'pdf',
            'columns' => request("columns")
        ])->toArabicHTML();

        $pdf = Pdf::loadHTML($html)
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true)
            ->setPaper([0, 0, 841.89, 1190.55], 'portrait');

// Save the PDF to the storage path
        $pdf->save($VoterPath);

// Generate a public URL for the file
        $VoterUrl = asset("storage/Reports/" . $VoterFile); // URL to access the file via the public/storage link

// Return data with the downloadable URL
        $data = [
            'report' => $pdf,
            'pdf_url' => $VoterPath, // Absolute path for server use
            'pdf' => $VoterUrl, // Public URL for download
            'creator_id' => auth()->user()->id
        ];

        return $data;

    }

    public function getVoters(Request $request)
    {
        $election = Election::find($request->election);

        if ($election) {
            $voters = $election->voters();
            if($request->voterType){
                $voters = $voters->where('type', $request->get('voterType'));
            }

            if($request->status){
                if($request->get('status')== 0 ){
                    $voters = $voters->where('status', 0);
                }else{
                    $voters = $voters->where('status', 1);
                    if ($request->has('AllCom')) {
                        $voters->whereNotNull('committee_id');
                    }else{
                        $voters->where('committee_id',$request->committee);
                    }
                }
            }
           
            if ($request->has('AllCon')) {
                $voters = $voters->whereHas('contractors')
                ->with(['contractors' => function ($query) {
                    $query->orderBy('name'); // Order contractors by name
                }, 'attend'])
                ->get()
                ->groupBy(function ($voter) {
                    return $voter->contractors->first()->id ?? null; // Group by the first contractor ID
                })
                ->map(function ($groupedVoters, $contractorId) {
                    return $groupedVoters
                        ->sortBy('name') // Order voters in each group by name
                        ->map(function ($voter) use ($contractorId) {
                            // Attach only the relevant contractor for this group
                            $voter->contractors = $voter->contractors->filter(function ($contractor) use ($contractorId) {
                                return $contractor->id == $contractorId;
                            });
                            return $voter;
                        });
                })
                ->sortBy(function ($groupedVoters, $contractorId) {
                    // Sort groups by contractor name
                    return $groupedVoters->first()->contractors->first()->name ?? ''; 
                });
            
            } else {
                $voters = $voters->whereHas('contractors', function ($q) use ($request) {
                    $q->where('contractor_id', $request->contractor);
                })
                    ->with(['contractors' => function ($query) {
                        $query->orderBy('name'); // Order contractors by name
                    }, 'attend'])
                    ->get()
                    ->groupBy(function ($voter) {
                        return $voter->contractors->first()->id ?? null; // Group by the first contractor ID
                    })
                    ->map(function ($groupedVoters, $contractorId) {
                        return $groupedVoters
                            ->sortBy('name') // Order voters in each group by name
                            ->map(function ($voter) use ($contractorId) {
                                // Attach only the relevant contractor for this group
                                $voter->contractors = $voter->contractors->filter(function ($contractor) use ($contractorId) {
                                    return $contractor->id == $contractorId;
                                });
                                return $voter;
                            });
                    });
            }
            

        }
        return $voters;
    }

}
