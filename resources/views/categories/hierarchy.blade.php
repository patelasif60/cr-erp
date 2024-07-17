<div class="card text-left">
    <div class="card-header bg-transparent">
        <h6 class="card-title task-title m-0">Categories Hierarchy</h6>
    </div>
    <div class="card-body">
        <div id="jstree">
            <!-- in this example the tree is populated from inline HTML -->
            <ul>
            @if($result)
                @foreach($result as $row)
                    @if($row->product_category != '')
                        <li id="maincat_{{ $row->id  }}" @if($type=='cat' && $row->id == $cat) class="jstree-open" @endif>{{ $row->product_category }}
                        
                        <ul>
                        @if(isset($row->node))
                            @foreach($row->node as $row_sub_category)
                                @if($row_sub_category->sub_category_1 != '')
                                    <li id="subcat1_{{ $row_sub_category->id }}" @if($type1=='sub_cat1' && $row_sub_category->id == $sub_cat1) class="jstree-open" @endif>{{ $row_sub_category->sub_category_1 }}
                                        @if(isset($row_sub_category->node))
                                            <ul>
                                                @foreach($row_sub_category->node as $row_sub_category_2)
                                                    @if($row_sub_category_2->sub_category_2 != '')
                                                        <li  id="subcat2_{{$row_sub_category_2->id}}" @if($type2=='sub_cat2' && $row_sub_category_2->id == $sub_cat2) class="jstree-open" @endif>{{ $row_sub_category_2->sub_category_2 }}
                                                            @if(isset($row_sub_category_2->node))
                                                                <ul>
                                                                    @foreach($row_sub_category_2->node as $row_sub_category_3)
                                                                    @if($row_sub_category_3->sub_category_3 != '')
                                                                    <li id="subcat3_{{$row_sub_category_3->id}}">{{ $row_sub_category_3->sub_category_3 }}</li>
                                                                    @endif
                                                                    @endforeach
                                                                    <li onClick="OpenModel('{{ route('categories.sub_category_3',$row_sub_category_3->id)  }}')" data-jstree='{"icon":"i-Add"}'>New Sub Category 3</li>
                                                                </ul>
                                                            @endif
                                                        </li>
                                                    @endif
                                                @endforeach
                                                <li onClick="OpenModel('{{ route('categories.sub_category_2',$row_sub_category->id)  }}')" data-jstree='{"icon":"i-Add"}'>New Sub Category 2</li>
                                            </ul>
                                        @endif
                                    </li>
                                @endif
                            @endforeach
                        @endif
                        <li onClick="OpenModel('{{ route('categories.sub_category_1',$row->id)  }}')" data-jstree='{"icon":"i-Add"}'>New Sub Category 1</li>
                        </ul>
                        @else
                      
                        

                        </li>
                    @endif
                @endforeach
                <li onClick="OpenModel('{{ route('categories.create')  }}')" data-jstree='{"icon":"i-Add"}'>New Category</li>
            @endif
            <!-- <li>
                Root node 1
                <ul>
                <li >Child node 1</li>
                <li>Child node 2</li>
                </ul>
            </li>
            <li>Root node 2</li>  -->
            </ul>
        </div>
    </div>
</div>

    <script>
  $(function () {
    // 6 create an instance when the DOM is ready
    $('#jstree').jstree();
    $('#jstree').on("select_node.jstree", function (e, data) { 
        let node  = data.node.id.split('_'); 
        type = node[0];
        id=node[1];
        if(type === 'subcat3'){
            OpenModel('{{ url('categories/sub_category_3_edit')  }}/'+id);
        }

        if(type === 'subcat2'){
            OpenModel('{{ url('categories/sub_category_2_edit')  }}/'+id);
        }

        if(type === 'subcat1'){
            OpenModel('{{ url('categories/sub_category_1_edit')  }}/'+id);
        }

        if(type === 'maincat'){
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