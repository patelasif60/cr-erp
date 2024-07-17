<div class="modal-dialog modal-xl">
    <!--Modal Content-->
    <div class="modal-content">
        <!-- Modal Header-->
        <div class="modal-header" style="background-color:#fff;">
            <h3>Product History Details</h3>
            <button type="button" class="close reset-text" data-dismiss="modal" style="color: black;">&times;</button>
        </div>
        
        <div class="modal-body">
            <div class="row">
            @if($history_response)
                <?php $i = 0;?>
                @foreach($history_response as $key=>$value)
                        <?php $i = $i+1; ?>
                        <div class="col-md-6">
                            <div class="font-weight-bold">{{$key}}</div>
                            <div class="">{{$value}}</div>
                        </div>
                        @if($i%2 == 0)
                        <div class="col-xl-12 mb-2 mt-2" style="border-top:1px solid rgba(0, 0, 0, .1);"></div>
                        @endif
                @endforeach
            @endif
            </div>
        </div> 
        <!-- <div class="modal-footer">  -->
            <a href="" class="btn btn-outline-primary reset-text mb-2" data-dismiss="modal">Cancel</a> 
        <!-- </div> -->
    </div>
</div>