<div class="card text-left">
    <div class="card-header bg-transparent">
        <h6 class="card-title task-title m-0">Categories Hierarchy</h6>
    </div>
    <div class="card-body">
        <div id="jstree_1">
            <!-- in this example the tree is populated from inline HTML -->
            <?php
                
                function GetWholeHirarchy($array,$id,$level,$parent){
                    $level++;
                    echo '<ul>';
                        foreach($array as  $row_sub_array_side){
                            
                            echo'<li id="main_'.$row_sub_array_side['id'].'" class="'.(in_array($row_sub_array_side['id'],$id) ? 'jstree-open' : '').'">
                            '.$row_sub_array_side['name'].'';
                                if(!empty($row_sub_array_side['nodes']))
                                {
                                    GetWholeHirarchy($row_sub_array_side['nodes'],$id,$level,$row_sub_array_side['id']);
                                }else{
                                    echo'<ul>';
                                    ?>
                                    <li onClick="OpenModel('{{ route('categories.add_category',[$row_sub_array_side['id'],$level])  }}')" data-jstree='{"icon":"i-Add"}'>New Category</li> 
                                    <?php
                                    echo'</ul>';
                                }
                            echo'</li>';
                            
                            ?>
                            
                            <?php
                            echo '';
                        }
                        ?>

                          <li onClick="OpenModel('{{ route('categories.add_category',[$parent,$level])  }}')" data-jstree='{"icon":"i-Add"}'>New Category</li>  
                            <?php
                    echo'</ul>';
                }

                GetWholeHirarchy($result,$id,-1,0);
            ?>
            <!-- <ul>
            @if($result)
                @foreach($result as $row_1)
                    <li id="cat_{{ $row_1['id'] }}">{{ $row_1['name'] }}
                        @if($row_1['nodes'])
                            <ul>
                                @foreach($row_1['nodes'] as $row_2)
                                    <li id="cat_{{ $row_2['id'] }}">{{ $row_2['name'] }}
                                    </li>
                                @endforeach
                                <li onClick="OpenModel('{{ route('categories.create')  }}')" data-jstree='{"icon":"i-Add"}'>New Category</li>
                            </ul>
                        @endif
                    </li>
                @endforeach
            @endif
            <li onClick="OpenModel('{{ route('categories.create')  }}')" data-jstree='{"icon":"i-Add"}'>New Category</li>
            </ul>
             -->
        </div>
    </div>
</div>

    <script>
  $(function () {
    // 6 create an instance when the DOM is ready
    $('#jstree_1').jstree();
    $('#jstree_1').on("select_node.jstree", function (e, data) { 
        let node  = data.node.id.split('_'); 
        type = node[0];
        id=node[1];
        if(type == 'main'){
            OpenModel('{{ url('categories/')  }}/'+id+'/edit');
        }
        
        
    });
    // // 7 bind to events triggered on the tree
    // $('#jstree').on("changed.jstree", function (e, data) {
    //   console.log(data.selected);
    // });
    // 8 interact with the tree - either way is OK
    // $('button').on('click', function () {
    //   $('#jstree').jstree(true).select_node('child_node_1');
    //   $('#jstree').jstree('select_node', 'child_node_1');
    //   $.jstree.reference('#jstree').select_node('child_node_1');
    // });
  });

 
  </script>