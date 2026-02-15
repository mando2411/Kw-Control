<!DOCTYPE html>
<html dir="rtl">

<head>
    <title>Arabic Invoice </title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        * {
            font-family: DejaVu Sans !important;
        }

        body {
            font-size: 16px;
            font-family: 'DejaVu Sans', 'Roboto', 'Montserrat', 'Open Sans', sans-serif;
            padding: 10px;
            margin: 10px;

            color: #777;
        }


        body {
            color: #777;
            text-align: right;
        }

        body h1 {

            margin-bottom: 0px;
            padding-bottom: 0px;
            color: #000;
        }

        body h3 {

            margin-top: 10px;
            margin-bottom: 20px;
            color: #555;
        }

        body a {
            color: #06f;
        }

        @page {
            size: a4;
            margin: 0;
            padding: 0;
        }

        .invoice-box table {
            direction: ltr;
            width: 100%;
            text-align: right;
            border: 1px solid;
            font-family: 'DejaVu Sans', 'Roboto', 'Montserrat', 'Open Sans', sans-serif;
        }


        .row {
            display: block;
            padding-left: 24;
            padding-right: 24;
            page-break-before: avoid;
            page-break-after: avoid;
        }

        .column {
            display: block;
            page-break-before: avoid;
            page-break-after: avoid;
        }
    </style>
</head>

@php
    $user=App\Models\Voter::select('id','name')->limit(10)->get()
@endphp
<body>

    <div class="row">
        <div class="column">
            <p class="text-darky">بيانات باللغة العربية </p>
        </div>
    </div>
    <h1>ان هذا عبارة عن قالب عادي </h1>
    <h3>ومن هنا قمنا بعرض قالب اعتيادي فقط كمثال توضيحي .</h3>

    <div class="invoice-box">
        <table>
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">

                            </td>

                            <td>
                                فاتورة #: 123<br />
                                الانشاء : يناير 1, 2015<br />
                                تاريخ : فبراير 1, 2015
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            @foreach ($user as $i )
            <span>{{$i->id}}</span>
            <span>{{$i->name}}</span>

            @endforeach


            <tr class="heading">
                <td>العنصر </td>

                <td>السعر </td>
            </tr>

            <tr class="item">
                <td>تصميم موقع </td>

                <td>$300.00</td>
            </tr>

            <tr class="item">
                <td>استضافة (3 أشهر )</td>

                <td>$75.00</td>
            </tr>

            <tr class="item ">
                <td>نطاق (1 عام )</td>

                <td>$10.00</td>
            </tr>

            <tr class="total last">
                <td>الإجمالي : </td>

                <td>$385.00 </td>
            </tr>
        </table>
        <div>
            <p lang="ar">
                انما نحن هنا نقوم بكتابة نصوص عربية فلما لا يكون هناك اخرى مما قد يحتم علينا أن نكتب أكثر.<br>
                وعليه فإن السطر هذا يعتبر سطر جديد
            </p>
        </div>
    </div>
</body>

</html>
