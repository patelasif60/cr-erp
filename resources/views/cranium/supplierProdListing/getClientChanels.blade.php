<div class="form-group col-md-6">
    <label for="chanel_ids" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Client Chanels">Client Chanels Not Assigned</label>
    <div class="custom_one_line_cards_container ChanelDrop border">
        <?php  $chanel_ids = explode(',',$chanel_ids); ?>
        @if ($chanels)
            @foreach($chanels as $key=>$row_chanel)
                @if(!in_array($row_chanel->id,$chanel_ids))
                    <div class="chanel_cards custom_one_line_cards" id="{{ $row_chanel->id }}">
                        {{ $row_chanel->channel }}
                    </div>
                @endif
            @endforeach
        @endif
    </div>
</div>
<div class="form-group col-md-6">
    <label for="chanel_ids" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Client Chanels">Client Chanels Assigned</label>
    <div class="custom_one_line_cards_container ChanelDropAssigned border">
        @if ($chanels)
            @foreach($chanels as $key=>$row_chanel)
                @if(in_array($row_chanel->id,$chanel_ids))
                    <div class="chanel_cards custom_one_line_cards" id="{{ $row_chanel->id }}">{{ $row_chanel->channel }}</div>
                @endif
                
            @endforeach
        @endif
    </div>
</div>
<script>
    $('.ChanelDrop ').on('click','.chanel_cards',function(e){
		var dropped_lobs = $(this).attr('id');
		console.log(dropped_lobs);
		var lobs_assigned = $("#chanel_ids").val();
		var lobs_array = [];
		if(lobs_assigned == ''){
			lobs_array.push(dropped_lobs);
			$("#chanel_ids").val(lobs_array.join(','));
		}else{
			lobs_array = lobs_assigned.split(',');
			lobs_array.push(dropped_lobs);
			$("#chanel_ids").val(lobs_array.join(','));
		}

		$('.ChanelDropAssigned').append(this);
	});

    $(".ChanelDropAssigned").on('click','.chanel_cards',function(e){
		var dropped_lobs = $(this).attr('id');
		console.log(dropped_lobs);
		var lobs_assigned = $("#chanel_ids").val();
		var lobs_array = [];
		if(lobs_assigned == ''){

		}else{
			lobs_array = lobs_assigned.split(',');
			lobs_array.splice($.inArray(dropped_lobs, lobs_array), 1);
			$("#chanel_ids").val(lobs_array.join(','));
		}
		$('.ChanelDrop').append(this);
	});
</script>