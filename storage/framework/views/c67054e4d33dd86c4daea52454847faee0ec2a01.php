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
    <div id="myHelpModal" class="modal fade" role="dialog">
        <!--Modal-->
        <div class="modal-dialog">
            <!--Modal Content-->
            <div class="modal-content">
                <!-- Modal Header-->
                <div class="modal-header">
                    <h3 class="h3">Help Request</h3>
                    <!--Close/Cross Button--> <button type="button" class="close reset-text" data-dismiss="modal" style="color: white;">&times;</button>
                </div> <!-- Modal Body-->
                <div class="modal-body"> 
                    <div class="flex">
                        <div>
                            <i class="far fa-file-alt fa-4x mb-3 animated rotateIn icon1"></i>
                        </div>
                        <div>
                            <h3>Your opinion matters</h3>
                            <h5>Help us improve our product?</h5>
                        </div>                        
                    </div>                    
                    <hr>
                    <h5>Type of Help Request:</h5>
                    <select id="help_type" class="form-control" required>
                        <option value=''> -- Select a value  -- </option>
                        <option value='cranium_error_code'>Cranium Error Code</option>
                        <option value='product_not_found'>Product not Found</option>
                        <option value='training_question'>Training Question</option>
                        <option value='general_help'>General Help</option>
                    </select>
                    <br>
                    <h5>Urgent Level:</h5>
                    <select id="help_urgent_level" class="form-control" required>
                        <option value=''> -- Select a value  -- </option>
                        <option value='urgent'>Urgent</option>
                        <option value='important'>Important</option>
                        <option value='not_urgent'>Not Urgent</option>
                    </select>
                    <br>
                    <h5>More Information:</h5>
                    <textarea 
                        type="textarea" 
                        class="form-control" 
                        id='help_message'
                        rows="3">
                    </textarea> 
                    <br>
                    <h5>Attach Image:</h5>
                    <input type="file" class="form-control" id="help_image_file">            
                </div>
                
                <!-- Modal Footer-->
                <div class="modal-footer"> 
                    <button class="btn btn-primary" id="submit_help">Send <i class="fa fa-paper-plane"></i> </button> 
                    <a href="" class="btn btn-outline-primary reset-text" data-dismiss="modal">Cancel</a> 
                </div>
            </div>
        </div>
    </div>
    
    <script>

        let imgTypes = ["jpg", "jpeg", "bmp", "gif", "png"];

        $("#submit_help").click(function(){
            var type = $("#help_type").val();
            var urgent_level = $("#help_urgent_level").val();
            var textarea_help = $("#help_message").val();
            var file = $('#help_image_file')[0].files[0];
            var extension = file ? file.name.substring(file.name.lastIndexOf('.') + 1) : "";
            
            if (!type || type === '' || !urgent_level || urgent_level === '') {
                toastr.error('Help Type/Level are required.');
                return;
            }

            if (file && !imgTypes.includes(extension)) {
                toastr.error('Illegal File format. Only [' + imgTypes + "] are allowed");
                return; 
            }
    
            let form = new FormData();
            form.append('urgent_level', urgent_level);
            form.append('type', type);
            if (textarea_help && textarea_help !== '') form.append('textarea_help', textarea_help);
            if (file) form.append('file', file);
    
            $.ajax({
                type:'POST',
                url:'/submit_help',
                data: form,
                processData: false,
                contentType: false,
                success: function(response){
                    if(response.error == false){
                        toastr.success(response.msg);
                        setTimeout(function(){
                            $('#myHelpModal').modal('hide');
                        }, 2000);
                    } else {
                        toastr.error(response.msg);
                    }                
                }
            });
        });
        $('.reset-text').click(function(){
            $("#send-feedback").text('Send');
        });
    </script><?php /**PATH C:\wamp64\www\cranium_new\resources\views/layouts/help.blade.php ENDPATH**/ ?>