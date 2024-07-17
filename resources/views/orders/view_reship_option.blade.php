     
<div class="modal-dialog modal-lg">
    <!--Modal Content-->
    <div class="modal-content">
        <!-- Modal Header-->
        <div class="modal-header" style="background-color:#fff;">
            <h3>Sub-Order {{ $key }} for Reship</h3>
            <!--Close/Cross Button-->
             <button type="button" class="close reset-text" data-dismiss="modal" style="color: black;">&times;</button>
        </div> 
        <div class="modal-body tab-pane">
            <form>
                @csrf
                <table class="table">
                    <thead>
                        <tr>
                            <th colspan="10" class="text-center" style="background:{{rand_color()}};">
                                Reship: #{{$key}}
                            </th>
                        </tr>
                    </thead>
                    <tbody>												
                        <tr>
                            <td scope="row" data-placement="top" title="Fault">Fault</td>
                            <td scope="row" data-placement="top">
                                <select class="select2 col-md-6" id="fault_{{ $key }}">
                                    <option value="-1">Select</option>
                                    @foreach ($fault_codes as $fc)
                                        <option value="{{ $fc->id }}">{{ $fc->fault }}</option>
                                    @endforeach										
                                </select>
                            </td>
                        </tr>
                        <tr>
                            ̊<td scope="row" data-placement="top" title="Re-Ship Reason">Re-Ship Reason</td>
                            <td scope="row" data-placement="top">
                                <select class="select2 col-md-6" id="reship_reason_{{ $key }}">
                                    <option value="">Select</option>
                                    @foreach ($reship_codes as $rc)
                                        <option value="{{ $rc->id }}">{{ $rc->reason }}</option>
                                    @endforeach															
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td scope="row" data-placement="top" title="Carrier Type">Carrier Type</td>
                            <td scope="row" data-placement="top">
                                <select class="select2 col-md-6" id="rs_carrier_type" onchange="changeReshipShipmentTypeInSubOrder(this)">
                                    <option value="-1">Select</option>
                                    @foreach ($carr as $car)
                                        <option value="{{ $car->company_name }}" <?php if($prev_carr_id == $car->id) echo 'selected'; ?> >{{ $car->company_name }}</option>
                                    @endforeach										
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td scope="row" data-placement="top" title="Shipment Type">Shipment Type</td>
                            <td scope="row" data-placement="top">
                                <select class="select2 col-md-6" id="rs_shipment_type">
                                    <option value="">Select</option>
                                    @if (strtolower($prev_carr_name) === 'ups')
                                        @foreach ($ups_st as $ups)
                                            <option value="{{ $ups->id }}" <?php if ($prev_ship_type == $ups->id) echo 'selected'; ?>>{{ $ups->service_name }}</option>
                                        @endforeach											
                                    @elseif (strtolower($prev_carr_name) === 'fedex')
                                        @foreach ($fedex_st as $fedex)
                                            <option value="{{ $fedex->id }}" <?php if ($prev_ship_type == $fedex->id) echo 'selected'; ?>>{{ $fedex->service_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
            <button type="button" class="btn btn-primary float-right" onclick="reShipOrders()">Re-Ship</button>
        </div>
    </div>
</div>

<script>
function reShipOrders() {

    var selectElem = document.getElementById('fault_' + '{{ $key }}');
    var faultId = selectElem.options[selectElem.selectedIndex].value;

    selectElem = document.getElementById('reship_reason_' + '{{ $key }}');
    var reShipReasonId = selectElem.options[selectElem.selectedIndex].value;

    selectElem = document.getElementById('rs_carrier_type');
    var carrierId = selectElem.options[selectElem.selectedIndex].value;

    selectElem = document.getElementById('rs_shipment_type');
    var shipmentType = selectElem.options[selectElem.selectedIndex].value;

    if (faultId === '' || reShipReasonId === '' || carrierId === '' || shipmentType === '') {
        toastr.error('Fault/Reship-Reason/Shipment/Carrier must be selected.');
        return;
    }

    var form = new FormData();
    form.append('order_number', {{ $on }});
    form.append('sub_order_number', '{{ $key }}');
    form.append('ids', '{{ $ids }}');
    form.append('fault_id', faultId);
    form.append('reship_reason_id', reShipReasonId);
    form.append('carrier_id', carrierId);
    form.append('shipment_type', shipmentType);

    $.ajax({
        url: '{{route('orders.reship_order')}}',
        method: 'POST',
        data: form,
        processData: false,
        contentType: false,
        success: function(res) {
            if(res.error === false) {
                toastr.success(res.msg);
                setTimeout(function(){
                    location.reload();
                }, 2000);
            } else {
                toastr.error(res.msg);
            }
        }			
    });
}

function changeReshipShipmentTypeInSubOrder(type) {
    var toAppend = 'Hello'
    if (type.value.toLowerCase() === 'fedex') {
        toAppend = @json($fedex_st);
    } else if (type.value.toLowerCase() === 'ups') {
        toAppend = @json($ups_st);
    } else {
        toAppend = [];
    }

    var select_elem = document.getElementById('rs_shipment_type');
    var options = select_elem.getElementsByTagName('option');
    for (var i = options.length; i--;) {	
        select_elem.removeChild(options[i]);
    }

    let opt = document.createElement("option");
    opt.value = ''; 
    opt.innerHTML = 'Select'; 
    select_elem.append(opt);

    for (var key in toAppend) {
        let opt = document.createElement("option");
        opt.value = toAppend[key].id; 
        opt.innerHTML = toAppend[key].service_name; 
        select_elem.append(opt); 
    }
}
</script>