@foreach($shippingEligibility as $shippingEligibilityKey=>$shippingEligibilityVal)
	<tr>
        <td class="table_border">
            <select onchange="updateShipping(this)" name="shipp[{{$shippingEligibilityVal->id}}][monday]" class="form-control select2">
               <option></option>
               @foreach($result as $key => $val)
               		<option {{$key == $shippingEligibilityVal->monday?'selected':''}} value="{{$key}}">{{ $val }}</option>
               @endforeach
            </select>
        </td>
        <td class="table_border">
        	<select onchange="updateShipping(this)" name="shipp[{{$shippingEligibilityVal->id}}][tuesday]" class="form-control">
               <option></option>
               @foreach($result as $key => $val)
                <option {{$key == $shippingEligibilityVal->tuesday?'selected':''}} value="{{$key}}">{{ $val }}</option>
               @endforeach
            </select>
        </td>
        <td class="table_border">
            <select onchange="updateShipping(this)" name="shipp[{{$shippingEligibilityVal->id}}][wednesday]" class="form-control">
               <option></option>
               @foreach($result as $key => $val)
                <option {{$key == $shippingEligibilityVal->wednesday?'selected':''}} value="{{$key}}">{{ $val }}</option>
               @endforeach
            </select>
        </td>
        <td class="table_border">
            <select onchange="updateShipping(this)" name="shipp[{{$shippingEligibilityVal->id}}][thursday]" class="form-control">
               <option></option>
               @foreach($result as $key => $val)
                <option {{$key == $shippingEligibilityVal->thursday?'selected':''}} value="{{$key}}">{{ $val }}</option>
               @endforeach
            </select>
        </td>
        <td class="table_border">
            <select onchange="updateShipping(this)" name="shipp[{{$shippingEligibilityVal->id}}][friday]" class="form-control">
               <option></option>
               @foreach($result as $key => $val)
                <option {{$key == $shippingEligibilityVal->friday?'selected':''}} value="{{$key}}">{{ $val }}</option>
               @endforeach
            </select>
        </td>
    </tr>
@endforeach
