     <div class="modal-dialog">
         <!--Modal Content-->
         <div class="modal-content">
             <!-- Modal Header-->
             <div class="modal-header" style="background-color:#fff;">
                 <h3>Columns for Report Type: {{ $report_type }}</h3>
                 <!--Close/Cross Button-->
                 <button type="button" class="close reset-text" data-dismiss="modal" style="color: black;">&times;</button>
             </div>
             <div class="modal-body">
                 <div class="modal-body">
                        {{-- <div class="row">
                            <div class="col-md-1">
                                <input type="checkbox" style="zoom:1.3" name="select_unselect_all_columns" id="select_unselect_all_columns">
                            </div>
                            <div class="col-md-6">
                                <label for="select_unselect_all_columns">Select/Unselect All</label>
                            </div>
                        </div> --}}
                        <form action="javascript:void(0);" method='POST' id="column_visibility_form">
                            @csrf
                            <div class="row">
                                <ul class="" id="column_visibility" style="list-style-type:none;">
                                    {{-- @if($product_listing_filter)
                                        @foreach($product_listing_filter as $key => $row_product_listing_filter)
                                            <li class="m-2">
                                                <label for="hide_show_column_{{ $row_product_listing_filter->id }}">
                                                <input id="hide_show_column_{{ $row_product_listing_filter->id }}" class="listing-filter-columns" type="checkbox" name='columns[]' value="{{ $row_product_listing_filter->sorting_order }}" <?php ?>><span class="font-weight-bold ml-2">{{ $row_product_listing_filter->label_name }}</span>
                                                </label>
                                            </li>
                                        @endforeach
                                    @endif --}}
                                    @if($filters)
                                        @foreach($filters as $filter)
                                            <li class="m-2">
                                                <label for="column_{{ $filter->id }}">
                                                    <input id="column_{{ $filter->id }}" class="report-columns" type="checkbox" name='columns' value="{{ $filter->id }}" <?php if(isset($col_ids) && in_array($filter->id, $col_ids)) { echo 'checked'; } ?>><span class="font-weight-bold ml-2">{{ $filter->ui_name }}</span>
                                                </label>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                            <div class="modal-footer">                             
                                <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="SaveFilters()">Save</button>
                            </div>
                        </form>
                 </div>
             </div>
         </div>
     </div>