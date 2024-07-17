     
<div class="modal-dialog mt-5 modal-lg" style="min-width: 50%">
    <!--Modal Content-->
    <div class="modal-content">
        <!-- Modal Header-->
        <div class="modal-header bg-light">
            <h5>WH Zip/TD Record for State | ZIP: <strong>[{{ $zip_zone_wh->state }} | {{ $zip_zone_wh->zip_3 }}]</strong></h5>
            <!--Close/Cross Button-->
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div> 
        <div class="modal-body">
            <input type="hidden" id="zip_wh_id" name="zip_wh_id" value="{{ $zip_zone_wh->id }}"/>
            <table class="table table-bordered text-center" id="order_details">
                <thead class="thead-dark">
                    <tr>
                        <th colspan="2">Record</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>State</td>
                        <td>{{ $zip_zone_wh->state }}</td>                        
                    </tr>
                    <tr>                                     
                        <td>Zip 3</td>
                        <td>{{ $zip_zone_wh->zip_3 }}</td>
                    </tr>
                    <tr> 
                        <td>Zone WI</td>
                        <td>{{ $zip_zone_wh->zone_WI }}</td>
                    </tr>
                    <tr> 
                        <td>Transit Day WI</td>                                          
                        <td><input type="number" id="td_wi" name="td_wi" value="{{ $zip_zone_wh->transit_days_WI }}" class="form-control" /></td>
                    </tr>
                    <tr>                                                             
                        <td>Zone PA</td>
                        <td>{{ $zip_zone_wh->zone_PA }}</td>
                    </tr>
                    <tr>                                     
                        <td>Transit Day PA</td>         
                        <td><input type="number" id="td_pa" name="td_pa" value="{{ $zip_zone_wh->transit_days_PA }}" class="form-control" /></td>                                 
                    </tr>
                    <tr>                                     
                        <td>Zone NV</td>
                        <td>{{ $zip_zone_wh->zone_NV }}</td>
                    </tr>
                    <tr>                                     
                        <td>Transit Day NV</td>  
                        <td><input type="number" id="td_nv" name="td_nv" value="{{ $zip_zone_wh->transit_days_NV }}" class="form-control" /></td>                                        
                    </tr>
                    <tr>                                     
                        <td>Zone OKC</td>
                        <td>{{ $zip_zone_wh->zone_OKC }}</td>
                    </tr>
                    <tr>                                     
                        <td>Transit Day OKC</td>    
                        <td><input type="number" id="td_okc" name="td_okc" value="{{ $zip_zone_wh->transit_days_OKC }}" class="form-control" /></td>                                      
                    </tr>
                </tbody>
            </table>
            <button type="button" class="btn btn-success float-right" onclick="updateTransitDays()">Save</button>
        </div>
    </div>
</div>
