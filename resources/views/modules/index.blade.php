@extends('layouts.master')

@section('page-css')
    
    <link rel="stylesheet" type="text/css" href="{{asset('assets/js/style.css')}}">  
    <style>
        .error{
            color:red;
        }
    </style>
@endsection

@section('main-content')
    <div class="breadcrumb">
        <h1>Modules</h1>
        <!-- <ul>
            <li><a href="">UI Kits</a></li>
            <li>Datatables</li>
        </ul> -->
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                <div class="card-header text-right bg-transparent">
                   
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6" style="padding: 30px 0;border: 0;border-top: 2px solid #ddd;    border-bottom: 2px solid #ddd;">
                            <div id="error_div"></div>
                            <div class="form-group col-lg-12">
                            <label for="menu_title" class="col-sm-3 control-label" style="padding-right:0px;">Modules Name:<span style="color:red;">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="menu_title" placeholder="Enter Menu Name" name="menu_title">
                            </div>
                            </div>
                            
                            <div class="form-group col-lg-12" >
                                <label for="module_slug" class="col-sm-3 control-label">Slug:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="module_slug" placeholder="Enter Slug" name="module_slug">
                                </div>
                            </div>

                            <div class="form-group col-lg-12" >
                                <label for="module_link" class="col-sm-3 control-label">Link:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="module_link" placeholder="Enter Link" name="module_link">
                                </div>
                            </div>

                            <div class="form-group col-lg-12" >
                                <label for="module_icon" class="col-sm-3 control-label">Icon:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="module_icon" placeholder="Enter Icon" name="module_icon">
                                </div>
                            </div>

                            <div class="form-group col-lg-12" >
                                <label for="is_module" class="col-sm-3 control-label">Is Modules:</label>
                                <div class="col-sm-9">
                                    <select class="form-control" id="is_module" name="is_module">
                                        <option value="">Select</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <button id="submit" class="btn btn-primary btn-flat">Submit</button>
                                    <button id="reset" class="btn btn-warning btn-flat">Reset</button>
                                </div>
                            </div>
                        </div>
                        <!-- col-lg-6 -->
                        <div class="col-lg-6" style="height:500px;overflow: auto;">
                            <input type="hidden" id="id">
                            <div class="cf nestable-lists">
                            <div class="dd" id="nestable">
                                <?php
                                $ref   = [];
                                $items = [];
                                foreach($modules as $data) {
                                    $thisRef = &$ref[$data->id];
                                    $thisRef['parent_menu_id'] = $data->parent_menu_id;
                                    $thisRef['menu_title'] = $data->menu_title;
                                    $thisRef['module_slug'] = $data->module_slug;
                                    $thisRef['module_icon'] = $data->module_icon;
                                    $thisRef['module_link'] = $data->module_link;
                                    $thisRef['is_module'] = $data->is_module;
                                    $thisRef['id'] = $data->id;
                                    if($data->parent_menu_id == 0) {
                                        $items[$data->id] = &$thisRef;
                                    } else {
                                        $ref[$data->parent_menu_id]['child'][$data->id] = &$thisRef;
                                    }
                                }
                                function get_menu($items,$class = 'dd-list') {
                                    $html = "<ol class=\"".$class."\" id=\"menu-id\">";
                                    foreach($items as $key=>$value) {
                                    $html.= '<li class="dd-item dd3-item" data-id="'.$value['id'].'" >
                                        <div class="dd-handle dd3-handle">&nbsp;</div>
                                        <div class="dd3-content"><span id="label_show'.$value['id'].'">'.$value['menu_title'].'</span> 
                                            <span class="span-right" id="span-right'.$value['id'].'">
                                                <a class="edit-button btn btn-primary btn-sm btn-flat dlbtn'.$value['id'].'" id="'.$value['id'].'" is_module="'.$value['is_module'].'" module_link="'.$value['module_link'].'" module_icon="'.$value['module_icon'].'" menu_title="'.$value['menu_title'].'" module_slug="'.$value['module_slug'].'" style="padding: 1px 7px;"><i class="fas fa-pencil-alt"></i></a>  <a class="del-button btn btn-danger btn-sm btn-flat" id="'.$value['id'].'" style="padding: 1px 7px;"><i class="fa fa-trash"></i></a>
                                            </span> 
                                        </div>';
                                        if(array_key_exists('child',$value)) {
                                            $html .= get_menu($value['child'],'dd-list');
                                        }
                                    $html .= "</li>";
                                    }
                                    $html .= "</ol>";
                                    return $html;
                                }
                                print get_menu($items);
                                ?>
                            </div>
                            </div>
                            <p></p>
                            <input type="hidden" id="nestable-output">
                            <button id="save" class="btn btn-primary btn-flat">Save</button>
                        </div>
                        <!-- col-lg-6 -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="MyModal" data-backdrop="static">
    </div>
    <!-- end of col -->
@endsection

@section('page-js')
<script src="{{asset('assets/js/jquery.nestable.js')}}"></script>
<script>
$(document).ready(function()
{
    var updateOutput = function(e)
    {
        var list   = e.length ? e : $(e.target),
            output = list.data('output');
        if (window.JSON) {
            output.val(window.JSON.stringify(list.nestable('serialize')));//, null, 2));
        } else {
            output.val('JSON browser support required for this demo.');
        }
    };
    // activate Nestable for list 1
    $('#nestable').nestable({
        group: 1
    })
    .on('change', updateOutput);
    // output initial serialised data
    updateOutput($('#nestable').data('output', $('#nestable-output')));
    $('#nestable-menu').on('click', function(e)
    {
        var target = $(e.target),
            action = target.data('action');
        if (action === 'expand-all') {
            $('.dd').nestable('expandAll');
        }
        if (action === 'collapse-all') {
            $('.dd').nestable('collapseAll');
        }
    });
});
</script>
<script>
  $(document).ready(function(){
    $("#load").hide();
    $("#submit").click(function(){
        if($("#menu_title").val() == ''){
            alert('Menu Name required');
            return false;
        }
        $("#load").show();
        var dataString = { 
            menu_title : $("#menu_title").val(),
            module_slug : $("#module_slug").val(),
            module_link : $("#module_link").val(),
            module_icon : $("#module_icon").val(),
            is_module : $("#is_module").val(),
            id : $("#id").val()
        };
        $.ajax({
            type: "POST",
            url: "<?php echo route('modules.store');?>",
            data: dataString,
            dataType: "json",
            cache : false,
            success: function(data){
                if(data.type == 'add'){
                    $("#menu-id").append(data.menu);
                } else if(data.type == 'edit'){
                    location. reload(true);
                    $('#label_show'+data.id).html(data.menu_title);
                }else if(data.type == 'error'){
                    $('#error_div').html(data.error);
                }
                $('#menu_title').val('');
                $('#module_slug').val('');
                $('#module_link').val('');
                $('#module_icon').val('');
                $('#is_module').val('');
                $('#id').val('');
                $("#load").hide();
            },error: function(xhr, status, error) {
                alert(error);
            },
        });
    });

    $('.dd').on('change', function() {
        $("#load").show();
        var dataString = { 
          data : $("#nestable-output").val(),
        };
        $.ajax({
          type: "POST",
          url: "{{route('modules.menu_save')}}",
          data: dataString,
          cache : false,
          success: function(data){
            $("#load").hide();
          } ,error: function(xhr, status, error) {
            alert(error);
          },
        });
    });

    $("#save").click(function(){
        $("#load").show();
        var dataString = { 
          data : $("#nestable-output").val(),
        };
        $.ajax({
          type: "POST",
          url: "<?php echo route('modules.menu_save');?>",
          data: dataString,
          cache : false,
          success: function(data){
            $("#load").hide();
            alert('Data has been saved');
        
          } ,error: function(xhr, status, error) {
            alert(error);
          },
        });
    });
 
    $(document).on("click",".del-button",function() {
        var x = confirm('Delete this menu?');
        var id = $(this).attr('id');
        if(x){
          $("#load").show();
          $.ajax({
            type: "POST",
            url: "<?php echo route('modules.menu_delete');?>",
            data: { id : id },
            cache : false,
            success: function(data){
              $("#load").hide();
              $("li[data-id='" + id +"']").remove();
            } ,error: function(xhr, status, error) {
              alert(error);
            },
          });
        }
    });
	
    $(document).on("click",".edit-button",function() {
        var id = $(this).attr('id');
        var menu_title = $(this).attr('menu_title');
        var module_slug = $(this).attr('module_slug');
        var module_icon = $(this).attr('module_icon');
        var module_link = $(this).attr('module_link');
        var is_module = $(this).attr('is_module');
		 
        $("#id").val(id);
        $("#module_slug").val(module_slug);
        $("#menu_title").val(menu_title);
        $("#is_module").val(is_module);
        $("#module_link").val(module_link);
        $("#module_icon").val(module_icon);
    });


    $(document).on("click","#reset",function() {
        $('#module_slug').val('');
        $('#menu_title').val('');
	    $('#id').val('');
        $('#module_link').val('');
        $('#module_icon').val('');
        $('#is_module').val('');
    });

  });

</script>
@endsection