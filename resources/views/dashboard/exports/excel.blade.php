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
    if (in_array('elmadany', $columns)) {
        $headers[] = 'elmadany';
    }
    if (in_array('alsndok', $columns)) {
        $headers[] = 'alsndok';
    }
    $headers = isset($mode) && $mode == 'pdf' ? array_reverse($headers) : $headers;
    $rows = [];
    foreach($voters as $i => $voter) {
        $row= ['#' => $i+1, 'name' => $voter->name];
        if (in_array('family', $columns)) {
            $row['family'] = $voter->family->name;
        }
        if (in_array('committee', $columns)) {
            $row['committee'] = $voter->committee ? $voter->committee->name : 'لايوجد';
        }
        if (in_array('type', $columns)) {
            $row['type'] = $voter->type;
        }
        if (in_array('status', $columns)) {
            $row['status'] = $voter->status == 1 ? "تم التصويت في".$voter->updated_at->format('Y/m/d') : "لم يتم التصويت"  ;
        }
        if (in_array('age', $columns)) {
            $row['age'] = $voter->age;
        }
        if (in_array('phone', $columns)) {
            $row['phone'] = $voter->phone1;
        }
        if (in_array('alsndok', $columns)) {
            $row['alsndok'] = $voter->alsndok;
        }
        if (in_array('restricted', $columns)) {
            $row['restricted'] = $voter->restricted;
        }
        if (in_array('created_at', $columns)) {
            $row['created_at'] = $voter->created_at;
        }
        if (in_array('elmadany', $columns)) {
            $row['elmadany'] = $voter->alrkm_almd_yn;
        }
        if (in_array('region', $columns)) {
            $row['region'] = $voter->alktaa . ' القطعه';
        }
        if (in_array('madrasa', $columns)) {
            $row['madrasa'] = $voter->committee ? ($voter->committee->school ? $voter->committee->school->name : 'لايوجد مرسه') : 'لا يوجد لجنه';
        }
        $rows[]= $row;
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