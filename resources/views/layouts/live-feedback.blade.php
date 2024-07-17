<style>
/* .modal-dialog {
    height: 50%;
    width: 50%;
    margin: auto
} */

.modal-header {
    color: white;
    background-color: #007bff
}

.textarea-feedback {
    border: none;
    box-shadow: none !important;
    -webkit-appearance: none;
    outline: 0px !important
    width:90%;
    margin:25px;
}

.openmodal {
    margin-left: 35%;
    width: 25%;
    margin-top: 25%
}

.icon1 {
    color: #007bff
}

a {
    margin: auto
}

input {
    color: #007bff
}
.h3{
    color:#fff;
}
</style>

<!--Modal Launch Button-->

<!--Division for Modal-->
<div id="myModal" class="modal fade" role="dialog">
    <!--Modal-->
    <div class="modal-dialog">
        <!--Modal Content-->
        <div class="modal-content">
            <!-- Modal Header-->
            <div class="modal-header">
                <h3 class="h3">Feedback Request</h3>
                <!--Close/Cross Button--> <button type="button" class="close reset-text" data-dismiss="modal" style="color: white;">&times;</button>
            </div> <!-- Modal Body-->
            <div class="modal-body text-center"> <i class="far fa-file-alt fa-4x mb-3 animated rotateIn icon1"></i>
                <h3>Your opinion matters</h3>
                <h5>Help us improve our product? <strong>Give us your feedback.</strong></h5>
                <hr>
                <h6>Your Rating</h6>
            </div> <!-- Radio Buttons for Rating-->
            <div class="form-check mb-4"> <input name="feedback" value="Very good" type="radio"> <label class="ml-3">Very good</label> </div>
            <div class="form-check mb-4"> <input name="feedback" value="Good" type="radio"> <label class="ml-3">Good</label> </div>
            <div class="form-check mb-4"> <input name="feedback" value="Mediocre" type="radio"> <label class="ml-3">Mediocre</label> </div>
            <div class="form-check mb-4"> <input name="feedback" value="Bad" type="radio"> <label class="ml-3">Bad</label> </div>
            <div class="form-check mb-4"> <input name="feedback" value="Very Bad" type="radio"> <label class="ml-3">Very Bad</label> </div>
            <!--Text Message-->
            <div class="text-center">
                <h4>What could we improve?</h4>
            </div> <textarea type="textarea" class="textarea-feedback" id='textarea-feedback' placeholder="Your Message" rows="3" name="yourmessage"></textarea> <!-- Modal Footer-->
            <div class="modal-footer"> 
                <button class="btn btn-primary" id="send-feedback">Send <i class="fa fa-paper-plane"></i> </button> 
                <a href="" class="btn btn-outline-primary reset-text" data-dismiss="modal">Cancel</a> 
            </div>
        </div>
    </div>
</div>

<script>
    $("#send-feedback").click(function(){
        var feedback = $("input[name=feedback]:checked").val();
        var message = $("#textarea-feedback").val();
        $.ajax({
            url:'/submit_feedback',
            type:'POST',
            data:{feedback:feedback, message:message},
            success:function(result){
                if(result)
                    $("#send-feedback").text('Feedback Submitted Successfully');
                else
                    $("#send-feedback").text('Error Submitting Feedback');
            }
        });
    });
    $('.reset-text').click(function(){
        $("#send-feedback").text('Send');
    });
</script>