<div class="col-sm-12">
    <div class="row">
        <div class="col-sm-11">
            <div class="col-sm-12">
                <h3>Subject : {{$ticket_details->subject}}</h3>
            </div>
            
            <div class="col-sm-12">
                <p><strong>Description</strong> : {{$ticket_details->description}}</p>
            </div>
        </div>
        <div class="col-sm-1">
            <div class="col-sm-12">
                <button type="button" class="btn btn-danger" id="btn_msg_send" onClick="BackToList()">Back</button>  
            </div>
        </div>
    </div>
</div>

<div class="col-sm-12">
    <div class="row border pb-3">
        <div class="col-sm-12 mb-3 overflow-auto" id="div_message" style="min-height:50px; max-height:300px; overflow-y: scroll;">
            @if($messages)
                @foreach($messages as $msg)
                <div class="row mb-2">
                    <?php if(Auth::user()->id == $msg->send_by) {$float = "float-right"; $style="margin-right:2%"; $bg_color="";} else {$float = "float-left"; $style="margin-left:1%; ";  $bg_color="background-color:#c5c3cc";}?>
                    <div class="col-sm-12 text-right ">
                        <div class="custom-message-card text-left rounded-lg mr-3 {{$float}}" style="{{$bg_color}}" id="msg_{{$msg->id}}">
                            {{ $msg->message }}
                        </div>
                    </div>
                    @if(Auth::user()->id == $msg->send_by)
                    <div class="col-sm-12 ">
                        <span class="{{$float}}" style="font-size:11px; {{$style}}">{{$msg->created_at}}</span>
                    </div>
                    @else
                    <div class="col-sm-12 ">
                        <span class="{{$float}}" style="font-size:11px; {{$style}}">Sent by {{$msg->name}} at {{$msg->created_at}}</span>
                    </div>
                    @endif
                </div>
                @endforeach
            @endif
        </div>

        @if($ticket_details->status == 1)
        <div class="col-sm-12">
            <div class="row">
                <div class="col-sm-11">
                    <input type="text" class="form-control" id="ticket_message" placeholder="Add message">
                </div>
                <div class="col-sm-1">
                    <button type="button" class="btn btn-warning" id="btn_msg_send" onClick="SendMessage()">Send</button>
                </div>
            </div>
        </div>
        @endif

        <div class="col-sm-12 text-center mt-3">
        </div>
    </div>
</div>

<script>
var objDiv = document.getElementById("div_message");
objDiv.scrollTop = objDiv.scrollHeight;
function SendMessage(){
    msg = $('#ticket_message').val();
    ticket_id = <?php echo $ticket_details->id?>;
    if(msg == ''){
        return false;
    }

    html = '<div class="row msg_row" id="msg_row"> <div class="col-sm-4"></div> <div class="col-sm-8 text-right "> <div class="custom-message-card float-right mr-3" id="">'+msg+'</div> </div> </div>';
    $('#div_message').append(html);
    var objDiv = document.getElementById("div_message");
    objDiv.scrollTop = objDiv.scrollHeight;
    $('#ticket_message').val(''); 
 
    $.ajax({
        type: "POST",
        url: '{{route('ticket.message.store')}}',
        data: {
            msg:msg,
            ticket_id:ticket_id,
        }, 
        success: function( response ) {
            html2 = '<div class="col-sm-12"> <span class="float-right" style="font-size:11px; margin-right:2%">'+response.time+'</span></div>';
            $('.msg_row').last().append(html2);
            var objDiv = document.getElementById("div_message");
            objDiv.scrollTop = objDiv.scrollHeight;
        },
    })
}

function BackToList(){
    $('#div_ticket_list').show();
    $('#div_chatbox').hide();
}
   
</script>