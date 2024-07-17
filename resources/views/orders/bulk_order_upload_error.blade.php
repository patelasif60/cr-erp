     
<div class="modal-dialog modal-lg" style="min-width: 70%">
    <!--Modal Content-->
    <div class="modal-content">
        <!-- Modal Header-->
        <div class="modal-header" style="background-color:#fff;">
            <h3 style="color: black;">File Processed With Errors</h3>
            <!--Close/Cross Button-->
             <button type="button" class="close reset-text" data-dismiss="modal" style="color: black;" onclick="window.location.reload();">&times;</button>
        </div> 
        <div class="modal-body">
            <table class="table" id="upload_error_details">
                <thead>
                    <tr>
                        <th>Errors</th>                                            
                    </tr>
                </thead>
                <tbody>
                    @if($errors)
                        @foreach($errors as $res)
                            <tr>
                                <td style="color: red;">&bull; {{ $res }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
