<?php $chanel_ids = explode(',',$chanel_ids); ?>
<div class="row">
	<div class="col-md-12">
		<label for="chanel_ids_ns" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Client Chanels"><h5>Client Chanels Not Assigned</h5></label>
		<div class="custom_one_line_cards_container ChanelDrop border">
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
</div>
<div class="row">
	<div class="col-md-12">
		<label for="chanel_ids_sl" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Client Chanels"><h5>Client Chanels Assigned</h5></label>
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
</div>
<script>
    $('.ChanelDrop ').on('click','.chanel_cards',function(e){
		var dropped_lobs = $(this).attr('id');
		var lobs_assigned = $("#chanel_ids_bl").val();
		var lobs_array = [];
		if(lobs_assigned == ''){
			lobs_array.push(dropped_lobs);
			$("#chanel_ids_bl").val(lobs_array.join(','));
		}else{
			lobs_array = lobs_assigned.split(',');
			lobs_array.push(dropped_lobs);
			$("#chanel_ids_bl").val(lobs_array.join(','));
		}

		$('.ChanelDropAssigned').append(this);
	});

    $(".ChanelDropAssigned").on('click','.chanel_cards',function(e){
		var dropped_lobs = $(this).attr('id');
		var lobs_assigned = $("#chanel_ids_bl").val();
		var lobs_array = [];
		if(lobs_assigned == ''){

		}else{
			lobs_array = lobs_assigned.split(',');
			lobs_array.splice($.inArray(dropped_lobs, lobs_array), 1);
			$("#chanel_ids_bl").val(lobs_array.join(','));
		}
		$('.ChanelDrop').append(this);
	});
</script>