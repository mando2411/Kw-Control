@forelse ($voters as $index=>$voter )
<tr>
    <td>{{ $index + 1 }}</td>
    <td>{{$voter->name}}</td>
    <td>{{$voter->status ? 'حضر' : " لم يحضر"}}</td>
    <td>

        <form action="{{ route('dashboard.voters.change-status', $voter->id) }}" method="POST" style="display:inline;">
            @csrf
            <input type="hidden" name="status" value="1">
            <input type="hidden" name="committee" value="{{request('committee')}}">
            <button type="submit" class="button attend_btn  button-arrived btn mb-1  {{ $voter->status ? "btn-danger" : 'btn-success' }}" {{ $voter->status ? 'disabled' : '' }} data-message="{{'تاكيد تحضير '.$voter->name}}" >تحضير</button>
        </form>
                <form action="{{ route('dashboard.voters.change-status', $voter->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="status" value="0">
                    <input type="hidden" name="committee" value="{{request('committee')}}">
                    <button type="submit" class="button attend_btn button-not-arrived btn mb-1   {{ !$voter->status ? 'btn-danger' : 'btn-primary' }}" {{ !$voter->status ? 'disabled' : '' }} data-message="{{'تاكيد عدم تحضير '.$voter->name}}">عدم تحضير</button>
                </form>


    </td>
</tr>
@empty

@endforelse
