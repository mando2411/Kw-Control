<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تصدير الجدول</title>
    <style>
          * {
            font-family: DejaVu Sans !important;
        }
        body{
            font-family: 'DejaVu Sans', 'Roboto', 'Montserrat', 'Open Sans', sans-serif;

        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 18px;
            text-align: right; /* Change to right for RTL */
            font-family: 'DejaVu Sans', 'Roboto', 'Montserrat', 'Open Sans', sans-serif;

        }
        thead tr {
            background-color: #009879;
            color: #ffffff;
            text-align: right; /* Change to right for RTL */
            font-weight: bold;
        }
        th, td {
            padding: 12px 15px;
        }
        tbody tr {
            border-bottom: 1px solid #dddddd;
        }
        tbody tr:nth-of-type(even) {
            background-color: #f3f3f3;
        }
        tbody tr:last-of-type {
            border-bottom: 2px solid #009879;
        }
        tbody tr:hover {
            background-color: #f1f1f1;
        }
        .banner{
            width: 95%;
            height: 200px;
            border: 5px double #000;
            margin: 25px auto;
        }
        @media screen and (max-width: 600px) {
   .banner{
    flex-direction: column;
    height: auto;
   }

}
    </style>
</head>
<body>
    @php
    $headers = ['#', 'name'];
    if (in_array('family', $columns)) {
        $headers[] = 'family';
    }
 
    if (in_array('committee', $columns)) {
        $headers[] = 'committee';
    }

    if (in_array('alrkm_almd_yn', $columns)) {
        $headers[] = 'alrkm_almd_yn';
    }
    
    if (in_array('type', $columns)) {
        $headers[] = 'type';
    }
    if (in_array('age', $columns)) {
        $headers[] = 'age';
    }
    if (in_array('phone', $columns)) {
        $headers[] = 'phone';
    }
    if (in_array('region', $columns)) {
        $headers[] = 'region';
    }
    if (in_array('status', $columns)) {
        $headers[] = 'status';
    }
    if (in_array('madrasa', $columns)) {
        $headers[] = 'madrasa';
    }
    if (in_array('restricted', $columns)) {
        $headers[] = 'restricted';
    }
    if (in_array('created_at', $columns)) {
        $headers[] = 'created_at';
    }
    if (in_array('alsndok', $columns)) {
        $headers[] = 'alsndok';
    }
     if (in_array('contractor', $columns)) {
        $headers[] = 'contractor_name';
        $headers[] = 'contractor_phone';
     }
      if (isset($columns['attendCom'])) {
        $headers[] = 'time';
    }
    
    
    $headers = isset($mode) && $mode == 'pdf' ? array_reverse($headers) : $headers;
$x = 0; // Initialize the counter outside the loop
$rows = [];

$hasColumns = [
    'family' => in_array('family', $columns),
    'committee' => in_array('committee', $columns),
    'type' => in_array('type', $columns),
    'status' => in_array('status', $columns),
    'age' => in_array('age', $columns),
    'phone' => in_array('phone', $columns),
    'alrkm_almd_yn' => in_array('alrkm_almd_yn', $columns),
    'alsndok' => in_array('alsndok', $columns),
    'restricted' => in_array('restricted', $columns),
    'created_at' => in_array('created_at', $columns),
    'region' => in_array('region', $columns),
    'madrasa' => in_array('madrasa', $columns),
    'attendCom' => isset($columns['attendCom']),
    'contractor' => in_array('contractor',$columns),
];
foreach ($voters as $j => $con) {
    foreach ($con as $i => $voter) {
        $row = ['#' => ++$x, 'name' => $voter->name]; // Increment counter and assign it

        if ($hasColumns['family']) {
            $row['family'] = $voter->family->name ?? 'لايوجد';
        }
        if ($hasColumns['committee']) {
            $row['committee'] = $voter->committee?->name ?? 'لايوجد';
        }
        if ($hasColumns['type']) {
            $row['type'] = $voter->type ?? 'غير محدد';
        }
        if ($hasColumns['status']) {
            $row['status'] = $voter->status == 1
                ? "تم التصويت في " . $voter->updated_at->format('Y/m/d')
                : "لم يتم التصويت";
        }
        if ($hasColumns['age']) {
            $row['age'] = $voter->age ?? 'غير متوفر';
        }
        if ($hasColumns['alrkm_almd_yn']) {
            $row['alrkm_almd_yn'] = $voter->alrkm_almd_yn ?? 'غير متوفر';
        }
        
        if ($hasColumns['phone']) {
            $row['phone'] = $voter->phone2 ?? 'غير متوفر';
        }
        if ($hasColumns['alsndok']) {
            $row['alsndok'] = $voter->alsndok ?? 'غير متوفر';
        }
        if ($hasColumns['restricted']) {
            $row['restricted'] = $voter->restricted ?? 'غير متوفر';
        }
        if ($hasColumns['created_at']) {
            $row['created_at'] = $voter->created_at?->format('Y/m/d') ?? 'غير متوفر';
        }
        if ($hasColumns['region']) {
            $row['region'] = $voter->alktaa ? $voter->alktaa . ' القطعه' : 'غير متوفر';
        }
        if ($hasColumns['madrasa']) {
            $row['madrasa'] = $voter->committee
                ? ($voter->committee->school->name ?? 'لايوجد مدرسه')
                : 'لا يوجد لجنه';
        }

        if ($hasColumns['attendCom']) {
            $details = '';

            if (!$voter->attend) {
                $row['time'] = 'لم يحضر';
            } else {
            if (in_array('date', $columns['attendCom'])) {
                $row['time'] = $voter->updated_at->format('Y/m/d');
            }
            }
        }
        if ($hasColumns['contractor']) {
            $name= $voter->contractors[0]->name;
            $phone=$voter->contractors[0]->phone;
            $row['contractor_name'] = $name;
            $row['contractor_phone'] = $phone;
            

        }
        $rows[] = $row;
    }
 }
@endphp

<table>
    <thead>
    <tr>
        @foreach($headers as $header)
        <th>{{ __('main.'.$header) }}</th>
    @endforeach
        <th></th>
    </tr>
    </thead>
    <tbody>
        @forelse($rows as $row)
        <tr>
            @foreach($headers as $header)
                <td>{{ $row[$header] }}</td>
            @endforeach
        </tr>
    @empty
            <tr>
                <td colspan="{{ count($headers) }}">لا يوجد بيانات</td>
            </tr>
    @endforelse
    </tbody>
</table>

</body>
</html>
