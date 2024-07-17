<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use App\Categories;
use App\SubCategory;
use App\MasterProduct;

class CategoriesController extends Controller
{
    public function __construct(Category $Category, SubCategory $SubCategory,Categories $Categories)
	{
        $this->Category = $Category;
        $this->SubCategory = $SubCategory;
        $this->Categories = $Categories;
	}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(moduleacess('AllSubMenusSelectionfunctions') == false){
            return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
        }
        return view('categories.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(ReadWriteAccess('AllSubMenusSelectionfunctions') == false){
            return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
        }
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories'
        ]);

        $Category = new Categories();
        $Category->name = $request->name;
        $Category->sa_code = $request->sa_code;
        $Category->parent_id = $request->parent_id;
        $Category->level = $request->level;
        $result = $Category->save();
        return response()->json([
            'error' => false,
            'msg' => 'Success',
            'data' => $Category
        ]);

    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'product_category' => 'required|unique:product_category'
    //     ]);

    //     $Category = new Category;
    //     $Category->product_category = $request->product_category;
    //     $Category->sa_code = $request->sa_code;
    //     $Category->save();
    //     return response()->json([
    //         'error' => false,
    //         'msg' => 'Success'
    //     ]);

    // }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $row = $this->Categories::find($id);
        return view('categories.edit',compact('row'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if($request->delete != 1){
            $request->validate([
                'name' => 'required|unique:categories,name,'.$id,
            ]);
    
            $Category = Categories::find($id);
            $Category->name = $request->name;
            $Category->sa_code = $request->sa_code;
            $Category->save();
            return response()->json([
                'error' => false,
                'msg' => 'Success',
                'data' => $Category
            ]);
        }else{
            $Category = Categories::find($id);
            $pro_count = MasterProduct::where('product_category',$id)->count();
            if($pro_count > 0){
                return response()->json([
                    'error' => true,
                    'msg' => 'This is used in Master Product, so we can not delete this'
                ]);
            }
            $Category->delete();
            return response()->json([
                'error' => false,
                'msg' => 'Success'
            ]);
        }
       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    function GetCategoryHeirarchy(Request $request){
        $result = $this->Category->GetCategoryHierarchy();
        $type = $request->type;
        $cat = $request->cat;
        $type1 = $request->type1;
        $sub_cat1 = $request->sub_cat1;
        $type2 = $request->type2;
        $sub_cat2 = $request->sub_cat2;
        return view('categories.hierarchy',compact('result','type','cat','type1','sub_cat1','type2','sub_cat2'));
    }

    function CategoryFromTopToBottom(Request $request){
        $result = $this->Categories->CategoryFromTopToBottom(0);
        $cat = $request->id;
        $id = [];
            
        if($cat != ''){
            $parent_tree = $this->Categories->GetCategoryParentHirarchy($cat);
            if($parent_tree){
                foreach($parent_tree as $row){
                    $id[] = $row['id'];
                }
            }
        }
        
        return view('categories.CategoryFromTopToBottom',compact('result','id'));
    }

    function sub_category_1($id){
        $row = $this->Category::find($id);
        return view('categories.sub_category_1',compact('row'));
    }

    function sub_category_1_store(Request $request){
        $request->validate([
            'sub_category_1' => 'required'
        ]);

        $SubCategory = new SubCategory;
        $SubCategory->product_category_id = $request->product_category_id;
        $SubCategory->sub_category_1 = $request->sub_category_1;
        $SubCategory->sc1_sa_code = $request->sc1_sa_code;
        $SubCategory->save();
        return response()->json([
            'error' => false,
            'msg' => 'Success',
            'result' => $SubCategory
        ]);
    }

    function sub_category_1_edit($id){
        $row = $this->SubCategory::find($id);
        return view('categories.sub_category_1_edit',compact('row'));
    }

    function sub_category_1_update(Request $request){
        if($request->delete != 1){
            $request->validate([
                'sub_category_1' => 'required'
            ]);

            $result = SubCategory::where('product_category_id', $request->product_category_id)->where('sub_category_1',$request->sub_category_1_original)->update([
                'sub_category_1' => $request->sub_category_1,
                'sc1_sa_code' => $request->sc1_sa_code
            ]);
            $SubCategory = SubCategory::find($request->id);
            if($result){
                return response()->json([
                    'error' => false,
                    'msg' => 'Success',
                    'result' => $SubCategory
                ]);
            }else{
                return response()->json([
                    'error' => true,
                    'msg' => 'Something went wrong'
                ]);
            }
        }else{
            $result = SubCategory::where('product_category_id', $request->product_category_id)->where('sub_category_1',$request->sub_category_1_original)->delete();
            $SubCategory = SubCategory::find($request->id);
            if($result){
                return response()->json([
                    'error' => false,
                    'msg' => 'Success',
                    'result' => $SubCategory
                ]);
            }else{
                return response()->json([
                    'error' => true,
                    'msg' => 'Something went wrong'
                ]);
            }
        }
    }



    function sub_category_2($id){
        $row = $this->SubCategory::find($id);
        return view('categories.sub_category_2',compact('row'));
    }

    function sub_category_2_store(Request $request){
        $request->validate([
            'sub_category_2' => 'required'
        ]);

        $result = SubCategory::where('product_category_id', $request->product_category_id)->where('sub_category_1',$request->sub_category_1)->whereNull('sub_category_2')->first();
        if($result){
            $SubCategory = SubCategory::find($result->id);
            $SubCategory->sub_category_2 = $request->sub_category_2;
            $SubCategory->sc2_sa_code = $request->sc2_sa_code;
            $SubCategory->save();
            return response()->json([
                'error' => false,
                'msg' => 'Success',
                'result' => $SubCategory
            ]);
        }else{
            $SubCategory = new SubCategory;
            $SubCategory->product_category_id = $request->product_category_id;
            $SubCategory->sub_category_1 = $request->sub_category_1;
            $SubCategory->sc1_sa_code = $request->sc1_sa_code;
            $SubCategory->sub_category_2 = $request->sub_category_2;
            $SubCategory->sc2_sa_code = $request->sc2_sa_code;
            $SubCategory->save();
            return response()->json([
                'error' => false,
                'msg' => 'Success',
                'result' => $SubCategory
            ]);
        }


    }

    function sub_category_2_edit($id){
        $row = $this->SubCategory::find($id);
        return view('categories.sub_category_2_edit',compact('row'));
    }

    function sub_category_2_update(Request $request){
        if($request->delete == 0){
            $request->validate([
                'sub_category_2' => 'required'
            ]);

            $result = SubCategory::where('product_category_id', $request->product_category_id)->where('sub_category_1',$request->sub_category_1)->where('sub_category_2',$request->sub_category_2_original)->update([
                'sub_category_2' => $request->sub_category_2,
                'sc2_sa_code' => $request->sc2_sa_code
            ]);
            $SubCategory = SubCategory::find($request->id);
            if($result){
                return response()->json([
                    'error' => false,
                    'msg' => 'Success',
                    'result' => $SubCategory
                ]);
            }else{
                return response()->json([
                    'error' => true,
                    'msg' => 'Something went wrong'
                ]);
            }
        }else{
            $result = SubCategory::where('product_category_id', $request->product_category_id)->where('sub_category_1',$request->sub_category_1)->where('sub_category_2',$request->sub_category_2_original)->update([
                'sub_category_2' => NULL,
                'sc2_sa_code' => NULL
            ]);
            $SubCategory = SubCategory::find($request->id);
            if($result){
                return response()->json([
                    'error' => false,
                    'msg' => 'Success',
                    'result' => $SubCategory
                ]);
            }else{
                return response()->json([
                    'error' => true,
                    'msg' => 'Something went wrong'
                ]);
            }
        }
    }

    function sub_category_3($id){
        $row = $this->SubCategory::find($id);
        return view('categories.sub_category_3',compact('row'));
    }

    function sub_category_3_store(Request $request){
        $request->validate([
            'sub_category_3' => 'required'
        ]);

        $SubCategory = new SubCategory;
        $SubCategory->product_category_id = $request->product_category_id;
        $SubCategory->sub_category_1 = $request->sub_category_1;
        $SubCategory->sc1_sa_code = $request->sc1_sa_code;
        $SubCategory->sub_category_2 = $request->sub_category_2;
        $SubCategory->sc2_sa_code = $request->sc2_sa_code;
        $SubCategory->sub_category_3 = $request->sub_category_3;
        $SubCategory->sc3_sa_code = $request->sc3_sa_code;
        $SubCategory->save();
        $parent = SubCategory::where('product_category_id', $request->product_category_id)->where('sub_category_1',$request->sub_category_1)->whereNull('sub_category_3')->first();
        return response()->json([
            'error' => false,
            'msg' => 'Success',
            'result' => $parent
        ]);
    }
    
    public function sub_category_3_edit($id){
        $row = $this->SubCategory::find($id);
        return view('categories.sub_category_3_edit',compact('row'));
    }

    public function sub_category_3_update(Request $request){
        if($request->delete == 0){
            $request->validate([
                'sub_category_3' => 'required'
            ]);
    
            $SubCategory = SubCategory::find($request->id);
            $SubCategory->product_category_id = $request->product_category_id;
            $SubCategory->sub_category_1 = $request->sub_category_1;
            $SubCategory->sc1_sa_code = $request->sc1_sa_code;
            $SubCategory->sub_category_2 = $request->sub_category_2;
            $SubCategory->sc2_sa_code = $request->sc2_sa_code;
            $SubCategory->sub_category_3 = $request->sub_category_3;
            $SubCategory->sc3_sa_code = $request->sc3_sa_code;
            $SubCategory->save();
            $parent = SubCategory::where('product_category_id', $request->product_category_id)->where('sub_category_1',$request->sub_category_1)->whereNull('sub_category_3')->first();
            return response()->json([
                'error' => false,
                'msg' => 'Success',
                'result' => $parent
            ]);
        }else{
            $SubCategory = SubCategory::find($request->id);
            $SubCategory->delete();
            $parent = SubCategory::where('product_category_id', $request->product_category_id)->where('sub_category_1',$request->sub_category_1)->whereNull('sub_category_3')->first();
            return response()->json([
                'error' => false,
                'msg' => 'Success',
                'result' => $parent
            ]);
        }
        
    }

    public function add_category($id = null,$level = 0){
        return view('categories.add_category',compact('id','level'));
    }
}
