<!--Modal-->
<div class="modal-dialog mt-5 modal-lg">
    <!--Modal Content-->
    <div class="modal-content">
        <!-- Modal Header-->
        <div class="modal-header bg-primary">
            <h3 class="modal-title text-white">Help Request Details | {{ $help['name'] }} | {{ $help['date'] }}</h3>
            <!--Close/Cross Button--> 
            <button type="button" class="close reset-text" data-dismiss="modal" style="color: white;">&times;</button>
        </div> <!-- Modal Body-->
        <div class="modal-body"> 
            <h5>Requester:</h5>                
            <input class="form-control" type="text" value="{{ $help['name'] }}" disabled/>
            <br>
            <h5>Type of Help Request:</h5>                
            <input class="form-control" type="text" value="{{ $help['type'] }}" disabled/>
            <br>
            <h5>Urgent Level:</h5>
            <input class="form-control" type="text" value="{{ $help['urgent_level'] }}" disabled/>
            <br>
            <h5>More Information:</h5>
            <textarea rows="3" class="form-control" type="text" disabled>{{ $help['desc'] }}</textarea>
            <br>
            <h5>Attachment:</h5>
            @if (isset($help['image_url']) && $help['image_url'] !== '')
                <a class="btn btn-primary" href="{{ url($help['image_url']) }}" data-toggle="tooltip" data-placement="top" title="Download">
                    Download Attachment
                </a>
            @else
                No Attachments
            @endif
        </div>
        
        <!-- Modal Footer-->
        <div class="modal-footer">                 
            <a href="" class="btn btn-danger reset-text" data-dismiss="modal">Cancel</a> 
            <a href="" class="btn btn-success" onclick="resolveTicket({{ $help['id'] }})">Resolve</a> 
        </div>
    </div>
</div>