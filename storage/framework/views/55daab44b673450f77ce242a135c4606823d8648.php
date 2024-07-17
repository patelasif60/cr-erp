
<textarea name="chat_input" id="chat_input" class="form-control" rows="6" placeholder="Type @ to mention and notify someone."></textarea>
<input type="hidden" value="" id="selected_users">
<button type="button" class="btn btn-primary mt-3" onClick="SaveChat()">Save</button>
<button type="button" class="btn btn-primary mt-3" onClick="ViewAll()">ViewAll</button>
<style>
    .chat_avtar{
        width: 50px;
        height: 50px;
        background: #013243;
        color: #fff;
        border-radius: 50%;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bolder;
        font-size: large;
    }
</style>
<div class="row mt-5">
    <div class="col-12" id="chat_container">
        
    </div>
</div>
<script>
    function SaveChat(){
        var type_id = '<?php echo e($type_id); ?>';
        var type = '<?php echo e($type); ?>';
        var chat = $("#chat_input").val();
        var selected_users = $("#selected_users").val();
        $.ajax({
            method:'POST',
            url:'<?php echo e(route('SaveChat')); ?>',
            dataType:'JSON',
            data:{type_id:type_id, type: type, chat:chat,selected_users:selected_users },
            success:function(response){
                if(response.error === false){
                    GetChat();
                    $("#chat_input").val('');
                }
            }
        })
    }


    function GetChat(){
        var type_id = '<?php echo e($type_id); ?>';
        var type = '<?php echo e($type); ?>';
        $.ajax({
            method:'POST',
            url:'<?php echo e(route('GetChat')); ?>',
            dataType:'html',
            data:{type_id:type_id, type: type },
            success:function(res){
                $("#chat_container").html(res);
            }
        })
    }

    function EditChat(id){
        var id = '<?php echo e($type); ?>';
        var chat = $("#chat_input").val();
        $.ajax({
            method:'POST',
            url:'<?php echo e(route('SaveChat')); ?>',
            dataType:'JSON',
            data:{type_id:type_id, type: type, chat:chat },
            success:function(response){
                if(response.error === false){
                    GetChat();
                    $("#chat_input").val('');
                }
            }
        })
    }
    
    $(function(){
        GetChat();
    })
</script>

<script>
    
    var typingTimer;  
    var doneTypingInterval = 1000;  //time in ms, 5 seconds for example
    var $input = $('#chat_input');

  $( function() {
    var availableTags = <?php echo json_encode(AllUsers()); ?>;
    $input.autocomplete({
      source: availableTags,
      minLength: 0,
        select: function (a, b) {
            a.preventDefault();
            let selecte_item = b.item.value;
            let chat_text = $input.val();
            let splited_chat = chat_text.split(' ');
            splited_chat[splited_chat.length - 1] = '@'+selecte_item;
            let final_text = splited_chat.join(' ');
            $input.val(final_text);

            let selected_user = $("#selected_users").val();
            if(selected_users === ""){
                $("#selected_users").val(selecte_item);
            }else{
                let splited_selecte_item = selected_user.split(',');
                splited_selecte_item.push(selecte_item);
                $("#selected_users").val(splited_selecte_item.join(','));
            }
        },
        focus: function( event, ui ) {
            event.preventDefault();
        }
    });
  } );



    $input.on('keypress',function(e){
        let chat_text = this.value;
        let splited_chat = chat_text.split(' ');
        let last_char = splited_chat[splited_chat.length - 1];
        let check_if_exist = last_char.includes('@');
        if(check_if_exist){
            let remove_separator = last_char.replace('@','');
            clearTimeout(typingTimer);
            typingTimer = setTimeout(OpenAutoComplete, doneTypingInterval);
        }
    });

    function OpenAutoComplete(){
        let chat_text = $input.val();
        let splited_chat = chat_text.split(' ');
        let last_char = splited_chat[splited_chat.length - 1];
        let check_if_exist = last_char.includes('@');
        if(check_if_exist){
            let remove_separator = last_char.replace('@','');
            $input.autocomplete( "search", remove_separator );
        }
    }
  </script>
<?php /**PATH C:\wamp64\www\cranium_new\resources\views/cranium/chat/chat.blade.php ENDPATH**/ ?>