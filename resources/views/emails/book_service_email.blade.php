@component('mail::message')
# Hello Admin

<x-mail::panel>
    You have a new custom service booking.
</x-mail::panel>

<table border="0" cellpadding="0" cellspacing="0" align="left" style="width: 100%;margin-bottom: 50px;">
    <tbody>
    <tr align="left" >
        <td style="border-bottom: 1px solid #0003; padding: 8px">Client Name</td>
        <td style="border-bottom: 1px solid #0003; padding: 8px;">{{ $name }}</td>
    </tr>
    <tr align="left" >
        <td style="border-bottom: 1px solid #0003; padding: 8px">Nationality</td>
        <td style="border-bottom: 1px solid #0003; padding: 8px;">{{ $nationality }}</td>
    </tr>
    <tr align="left" >
        <td style="border-bottom: 1px solid #0003; padding: 8px">Email</td>
        <td style="border-bottom: 1px solid #0003; padding: 8px;">{{ $email }}</td>
    </tr>
    <tr align="left" >
        <td style="border-bottom: 1px solid #0003; padding: 8px">Phone</td>
        <td style="border-bottom: 1px solid #0003; padding: 8px;">{{ $phone }}</td>
    </tr>
    <tr align="left" >
        <td style="border-bottom: 1px solid #0003; padding: 8px">Services</td>
        <td style="border-bottom: 1px solid #0003; padding: 8px;">{{ $services }}</td>
    </tr>
    <tr align="left" >
        <td style="border-bottom: 1px solid #0003; padding: 8px">Additional Notes</td>
        <td style="border-bottom: 1px solid #0003; padding: 8px;">{{ $notes }}</td>
    </tr>
    </tbody>
</table>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
