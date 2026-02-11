@forelse ($voters as $i=>$voter )
<tr class="border-bottom border-dark px-1">
 <input type="hidden" name="" id="voter_id" value="{{$voter->id}}">
 <td  class="px-1" rowspan="{{($voter->contractors->count())+1}}">

    {{
            ($currentPage - 1) * $perPage + ($i + 1) 

}}</td>
 <td rowspan="{{($voter->contractors->count())+1}}" class="modalButton" data-bs-toggle="modal" data-bs-target="#madameen">{{$voter->name}}
 @if($voter->contractors->count() > 1)
 <br>
 <span>
     المتعهدين : {{$voter->contractors->count()}}
 </span>
 @endif
 </td>

</tr>
@forelse ($voter->contractors as $c=>$con )
<tr class="border-bottom border-dark"    style="color: #000 !important">
 <td class="d-none" id="user_id">{{$con->id}}</td>
 <input type="hidden" id="conUrl" data-url="{{ route('con-profile', $con->token) }}">
 <td class="bg-warning bg-opacity-25 w30">{{$voter->phone1}}</td>
 <td class="bg-warning bg-opacity-25">{{$con->trust}}%</td>
 <td class="bg-warning bg-opacity-25" data-bs-toggle="modal"  data-bs-target="#mota3ahdeenName">{{$con->user->name}}</td>
 <td class="bg-warning bg-opacity-25 w30">{{$con->voters->count()}}</td>
 <td class="bg-warning bg-opacity-25">{{$con->pivot->percentage}}%</td>
 <td class="bg-warning bg-opacity-25">{{$con->user->phone}}</td>
 @if ($c == 0)
 <td rowspan="{{($voter->contractors->count())}}">
     @if ($voter->status == true)
     <i class="fa fa-check-square text-success"></i>
     <span>تم التصويت</span>
     <span class="currentTime">{{$voter->updated_at}}</span>

     @else
     <span class="currentTime">{{$voter->committee ? $voter->committee->name : " لايوجد  " }}</span>

     @endif
 </td>

 @endif
</tr>
@empty

@endforelse
@empty

@endforelse
