@component('mail::message')
# Hello Admin

<x-mail::panel>
    You have a new tour booking.
</x-mail::panel>

<table border="0" cellpadding="0" cellspacing="0" align="left" style="width: 100%;margin-bottom: 50px;">
    <tbody>
    <tr align="left" >
        <td style="border-bottom: 1px solid #0003; padding: 8px">Client Name</td>
        <td style="border-bottom: 1px solid #0003; padding: 8px;">{{ $booking->name }}</td>
    </tr>
    <tr align="left" >
        <td style="border-bottom: 1px solid #0003; padding: 8px">Email</td>
        <td style="border-bottom: 1px solid #0003; padding: 8px;">{{ $booking->email }}</td>
    </tr>
    <tr align="left" >
        <td style="border-bottom: 1px solid #0003; padding: 8px">Phone</td>
        <td style="border-bottom: 1px solid #0003; padding: 8px;">{{ $booking->phone }}</td>
    </tr>
    <tr align="left" >
        <td style="border-bottom: 1px solid #0003; padding: 8px">Tour</td>
        <td style="border-bottom: 1px solid #0003; padding: 8px;">{{ $booking->tour?->title }}</td>
    </tr>
    <tr align="left" >
        <td style="border-bottom: 1px solid #0003; padding: 8px">Date</td>
        <td style="border-bottom: 1px solid #0003; padding: 8px;">{{ optional($booking->date)->format('d/m/Y') }}</td>
    </tr>
    <tr align="left" >
        <td style="border-bottom: 1px solid #0003; padding: 8px">Nationality</td>
        <td style="border-bottom: 1px solid #0003; padding: 8px;">{{ $booking->nationality }}</td>
    </tr>
    <tr align="left" >
        <td style="border-bottom: 1px solid #0003; padding: 8px">Adults</td>
        <td style="border-bottom: 1px solid #0003; padding: 8px;">({{ $booking->adult_number }}) x {{ number_format($booking->tour->adultPrice($booking->adult_number)).'$' }}</td>
    </tr>
    <tr align="left" >
        <td style="border-bottom: 1px solid #0003; padding: 8px">Children</td>
        <td style="border-bottom: 1px solid #0003; padding: 8px;">({{ $booking->child_number }}) x {{ number_format($booking->tour->child_price).'$' }}</td>
    </tr>
    <tr align="left" >
        <td style="border-bottom: 1px solid #0003; padding: 8px">Infants</td>
        <td style="border-bottom: 1px solid #0003; padding: 8px;">{{ $booking->infants_number }}</td>
    </tr>
    <tr align="left" >
        <td style="border-bottom: 1px solid #0003; padding: 8px">Total Price</td>
        <td style="border-bottom: 1px solid #0003; padding: 8px;">{{ number_format($booking->price) . '$' }}</td>
    </tr>
    <tr align="left" >
        <td style="border-bottom: 1px solid #0003; padding: 8px">Additional Notes</td>
        <td style="border-bottom: 1px solid #0003; padding: 8px;">{{ $booking->message }}</td>
    </tr>
    </tbody>
</table>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
