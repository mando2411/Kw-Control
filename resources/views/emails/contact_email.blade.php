@component('mail::message')

# Hello Admin

<x-mail::panel>
    You have a new contact message.
</x-mail::panel>


@endcomponent
<div>

    <table  cellpadding="0" cellspacing="0"  style="width: 100%;margin-bottom: 50px;">
        <tbody>
        <tr  >
            <td style="border-bottom: 1px solid #0003; padding: 8px">Client Name</td>
            <td style="border-bottom: 1px solid #0003; padding: 8px;">{{ $name }}</td>
        </tr>

        <tr  >
            <td style="border-bottom: 1px solid #0003; padding: 8px">Email</td>
            <td style="border-bottom: 1px solid #0003; padding: 8px;">{{ $email }}</td>
        </tr>
        <tr  >
            <td style="border-bottom: 1px solid #0003; padding: 8px">Phone</td>
            <td style="border-bottom: 1px solid #0003; padding: 8px;">{{ $phone }}</td>
        </tr>
        <tr  >
            <td style="border-bottom: 1px solid #0003; padding: 8px">Subject</td>
            <td style="border-bottom: 1px solid #0003; padding: 8px;">{{ $subject }}</td>
        </tr>
        <tr  >
            <td style="border-bottom: 1px solid #0003; padding: 8px">Message</td>
            <td style="border-bottom: 1px solid #0003; padding: 8px;">{{ $message }}</td>
        </tr>
        </tbody>
    </table>


</div>
Thanks,<br>
{{ config('app.name') }}
