<?php

namespace App\Http\Controllers;
use Auth;
use File;
use View;
use Excel;
use App\User;
use App\Client;
use App\Contact;
use App\CsvData;
use App\Supplier;
use App\TimeZone;
use App\CsvHeader;
use App\WareHouse;
use Carbon\Carbon;
use App\SupplierDot;
use App\MaterialType;
use App\SupplierKehe;
use App\SupplierMars;
use App\MasterProduct;
use App\UploadHistory;
use App\SupplierDryers;
use App\SupplierHershey;
use App\SupplierDocument;
use App\ProductTemperature;
use App\SupplierAccountNote;
use Illuminate\Http\Request;
use App\SupplierDocumentsLink;
use App\SupplierMiscellaneous;
use Yajra\DataTables\DataTables;
use App\Services\SupplierService;
use Illuminate\Support\Facades\DB;
use App\ClientChannelConfiguration;
use App\Imports\SupplierProductImport;
use App\Http\Requests\CsvImportRequest;
use App\Http\Requests\SuppliersRequest;
use App\MaterialWarehouseTdCount;
use Maatwebsite\Excel\HeadingRowImport;
use Illuminate\Support\Facades\Response;

class SupplierController extends Controller
{
    public function __construct(SupplierService $service)
	{
        $this->middleware('admin_and_manager');
        $this->service = $service;
	}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(moduleacess('Suppliers') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}
        $result = Supplier::select(['id','name','email','phone','address','status'])->get();
        return view('suppliers.index',compact('result'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(ReadWriteAccess('AddNewSupplier') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}
        $warehouse = DB::table('warehouses')->orderBy('warehouses','ASC')->pluck('warehouses')->toArray();
        $managers = User::pluck('name','id')->toArray();
        $time_zones = TimeZone::pluck('name','id')->toArray();
        $productPackageType = config('caranium.PRODUCT_PACKAGE');
        return view('suppliers.create',compact('warehouse','time_zones','managers','productPackageType'));
    }

    public function store(SuppliersRequest $request){
        if(ReadWriteAccess('AddNewSupplier') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}
        $Supplier = $this->service->create($request->all());
        return response()->json([
            'error' => false,
            'msg' => 'Success',
            'url' => route('suppliers.edit',$Supplier->id)
        ]);
    }

    public function listPackagingMatirial(){
        return view('suppliers.packaging_material.list');
    }

    public function edit($id){
        if(ReadWriteAccess('EditSupplier') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}
        $row = Supplier::find($id);
        $row->csvHeader = $row->csvHeader()->where('map_type','!=','packaging_materials')->first();
        if($row->supplier_product_package_type != 'Product');
        {
            $row->csvHeader = $row->csvHeader()->where('map_type','packaging_materials')->first();    
        }
        $users = User::pluck('name')->toArray();
        $warehouses = DB::table('warehouses')->orderBy('warehouses','ASC')->pluck('warehouses')->toArray();
        $managers = User::pluck('name','id')->toArray();
        $time_zones = TimeZone::pluck('name','id')->toArray();
        $productPackageType = config('caranium.PRODUCT_PACKAGE');

        $ps = DB::table('purchasing_summaries')
            ->join('warehouses', 'warehouses.id', '=', 'purchasing_summaries.warehouse_id')
            ->where('purchasing_summaries.supplier_id', $row->id)
            ->select(['purchasing_summaries.*', 'warehouses.warehouses'])
            ->get();
        $result = [];
    	if ($ps && count($ps) > 0) {
    		foreach($ps as $p) {
                array_push($result, [
                    'id' => $p->id,
                    'warehouse' => $p->warehouses,
                    'order' => $p->order,
                    'order_date' => $p->purchasing_asn_date,
                    'delivery_date' => $p->delivery_date,
                    'po_status' => $p->po_status,
                    'report_path' => $p->report_path,
                    'invoice' => $p->invoice
                ]);
            }
    	}

        return view('suppliers.edit',compact('row','warehouses','time_zones','managers','users','productPackageType','result'));
    }

    public function update(SuppliersRequest $request,$id){
        if(ReadWriteAccess('EditSupplier') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}
        $this->service->update($request,$id);
        return response()->json([
            'error' => false,
            'msg' => 'Success',
            'url' => route('suppliers.index')
        ]);
    }

    public function updateSupplierConfig(Request $request,$id){
        if(ReadWriteAccess('EditSupplier') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}

        $Supplier = Supplier::find($id);
        $Supplier->order_schedule = $request->order_schedule;
        $Supplier->order_deadlines = $request->order_deadlines;
        $Supplier->cuttoff_time = $request->cuttoff_time;
        $Supplier->delivery_day = $request->delivery_day;
        $Supplier->owner = $request->owner;

        $Supplier->e_trailer_account_number = $request->account_number;
        $Supplier->minimums = $request->minimums;
        $Supplier->order_restriction_details = $request->order_restriction_details;
        $Supplier->lead_time_overview_notes = $request->lead_time_overview_notes;
        $Supplier->order_method = $request->order_method;
        $Supplier->order_portal_url = $request->order_portal_url;
        $Supplier->order_portal_username = $request->order_portal_username;
        $Supplier->order_portal_password = $request->order_portal_password;
        $Supplier->save();
        return response()->json([
            'error' => false,
            'msg' => 'Success',
            'url' => route('suppliers.edit',$id)
        ]);
    }

    public function map_supplier_product_file($id){
        $csvHeader = CsvHeader::where('supplier_id', $id)->first();
        return view('cranium.map_supplier_product_file',['supplier_id' => $id,'csvHeader' => $csvHeader]);
    }

     public function MapSupplierProduct(CsvImportRequest $request){
        try{
            $user = auth()->user();
            $mimes = array('csv');
            $extension = pathinfo($request->file('csv_file')->getClientOriginalName(), PATHINFO_EXTENSION);
            $name = $request->input('name');
            $supplier_id = $request->supplier_id;
            $csv_format = $request->csv_formate ? $request->csv_formate :'packaging_materials';
            if (in_array($extension, $mimes)) {
                $path = $request->file('csv_file')->getRealPath();
                $data = (new HeadingRowImport)->toArray(request()->file('csv_file'));
                if (count($data[0]) > 0) {

                    $csv_header_fields = [];
                    foreach ($data[0] as $key => $value) {
                        $csv_header_fields[] = $key;
                    }

                    //$csv_data = $data[0];
                    $csv_data = array_slice($data[0], 0, 1);
                    $header = $data[0][0];
                    if (isset($header[0])) {
                        $csv_data_file = CsvData::create([
                            'map_type' => $csv_format,
                            'supplier_id' => $supplier_id,
                            'csv_filename' => $request->file('csv_file')->getClientOriginalName(),
                            'csv_header' => json_encode($header),
                            'csv_data' => json_encode($data[0])
                        ]);
                        $lastInsertedId = $csv_data_file->id;
                    } else {
                        return response()->json([
                            'error' => true,
                            'msg' => 'First Column of your CSV file is Blank, Unable to Map your Headers'
                        ]);
                    }

                } else {
                    return response()->json([
                        'error' => true,
                        'msg' => 'Something Went Wrong'
                    ]);
                }
                 //$csv_format = config('mapping.PACKAGE_MATIRIAL');
                $view = (string)View::make('cranium.supplier_import_fields', compact('csv_header_fields', 'csv_data', 'csv_data_file', 'lastInsertedId', 'supplier_id','csv_format'));
                return response()->json([
                    'error' => false,
                    'msg' => 'Success',
                    'result' => $view
                ]);

            } else {
                return response()->json([
                    'error' => true,
                    'msg' => 'Please upload CSV formatted file'
                ]);
            }
        }
        catch (\Throwable $e) {
            // dd($e);
            return response()->json([
                'error' => true,
                'msg' => $e->getMessage()
            ]);
        }

    }
    public function getPackagingBySupplier(Request $request,$id = ''){
        $data = $this->service->getPackagingBySupplier($id,$request);
        if($request->type == 'kit')
        {
            return Datatables::of($data)->addColumn('action', function($row){
                $btn = '';
                $btn .= '<a href="javascript:void(0)" onClick=openQtyModal(\''.$row->id.'\') class="btn btn-primary btn-sm">Add package</a>';
                return $btn;
                
            })->rawColumns(['action'])->make(true);
        }
        else{
            return Datatables::of($data)
            ->addColumn('supplier_name',function($row){
                return $row->supplier->name;
            })
            ->addColumn('client_name',function($row){
                return $row->client ? $row->client->company_name : '-' ;
            })
            ->addColumn('action', function($row) use ($request){
                $btn = '';
                if($row->item_form_description=='kit')
                {
                    
                    if($request->type == 'packaginglist')
                    {
                        $btn .= '<a href="'.route('packagekiteditlist',$row->id).'" class="btn btn-primary"><i class="nav-icon i-Pen-2 "></i></a><a href="javascript:void(0)" onclick="deletePackagingMaterial(\''.$row->id.'\')" class="btn btn-danger ml-1"><i class="nav-icon i-Close-Window"></i> </a>';
                        return $btn;
                    }
                    $btn .= '<a href="'.route('packagekitedit',$row->id).'" class="btn btn-primary"><i class="nav-icon i-Pen-2 "></i></a><a href="javascript:void(0)" onclick="deletePackagingMaterial(\''.$row->id.'\')" class="btn btn-danger ml-1"><i class="nav-icon i-Close-Window"></i> </a>';
                    return $btn;
                }
                if($request->type == 'packaginglist')
                {
                    $btn .= '<a href="'.route('editpackagemateriallist',$row->id).'" class="btn btn-primary"><i class="nav-icon i-Pen-2 "></i></a>';
                }
                else{
                    $btn .= '<a href="'.route('editpackagematerial',$row->id).'" class="btn btn-primary"><i class="nav-icon i-Pen-2 "></i></a>';
                }
                $btn .=  '<a href="javascript:void(0)" onclick="deletePackagingMaterial(\''.$row->id.'\')" class="btn btn-danger ml-1"><i class="nav-icon i-Close-Window"></i> </a>';
                return $btn;
            })->rawColumns(['action'])->make(true);
        }
    }

    public function saveSupplierImportHeaders(Request $request){
        $user = auth()->user();
        $csvHeader = CsvHeader::where('supplier_id', $request->supplier_id)->where('map_type',$request->csv_format)->first();
        if (!$csvHeader) {
            $fields = $request->input('fields');
            $fields = array_flip($fields);
            unset($fields['Select']);
            $fields = array_flip($fields);
            $csv_header_data = new CsvHeader();
            $csv_header_data->supplier_id = $request->supplier_id;
            $csv_header_data->map_type = $request->csv_format;
            $csv_header_data->map_data = json_encode($fields);
            $csv_header_data->save();
        } else {
            $fields = $request->input('fields');
            $fields = array_flip($fields);
            unset($fields['Select']);
            $fields = array_flip($fields);
            $csvHeader->map_data = json_encode($fields);
            $csvHeader->save();
        }

        return response()->json([
            'error' => false,
            'msg' => 'Success'
        ]);
    }

    public function delete_supplier_product_header($id){
        $csvHeader = CsvHeader::where('id', $id)->delete();
        return back()->with(['success' => 'Success']);
    }

    public function destroy($id){
        if(ReadWriteAccess('DeleteSupplier') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}
        $Supplier = Supplier::find($id);
        $this->service->deleteSupplierPackgematirial($id);
        $Supplier->delete();
        return redirect()->route('suppliers.index')->with('success','Deleted successfully');
    }

    public function contactList($id){
        
        $contacts = Contact::where('supplier_id',$id)->get();
        return Datatables::of($contacts)
            ->addIndexColumn()
            ->addColumn('action', function($contact){
                    $btn = '';
                    $url = route('suppliers.editContact',$contact->id);
                    $btn .= '<a href="javascript:void(0);" onclick="getModal(\''.$url.'\')"  class="edit btn btn-primary btn-sm mr-2">Edit</a>';
                    $btn .= '<a  href="javascript:void(0);" onclick="deleteContact(\''.route('suppliers.deleteContact',$contact->id).'\')"  class="delete btn btn-danger btn-sm">Delete</a>';
                    return $btn;
            })
            ->editColumn('status',function($contact){
                $checked = '';
                if ($contact->is_primary == 1) {
                    $checked = "checked";
                }
                return '<input type="checkbox" onclick="setPrimaryContact(this,\''.$contact->id.'\')" name="is_primary" value="1" '.$checked.'>';
            })
            ->rawColumns(['status','action'])
            ->make(true);
    }

    public function setPrimaryContact(Request $request){
        $contact = Contact::find($request->id);
        if ($contact->is_primary) {
            $contact->is_primary = 0;
            $contact->update();
            return response()->json(['msg' => 'Success', 'error' => 0]);
        }
        else{
            $contact->is_primary = 1;
            $contact->update();

            $unsetAll = Contact::where('id','!=',$request->id)->where('supplier_id',$contact->supplier_id)->update(['is_primary' => 0]);
            return response()->json(['msg' => 'Success', 'error' => 0]);
        }
    }

    public function createContact($id){
        return view('suppliers.contacts.create',compact('id'));
    }

    public function storeContact(Request $request){
      $result = Contact::create([
        'supplier_id' => $request->supplier_id,
        'name' => $request->name,
        'title' => $request->title,
        'email' => $request->email,
        'office_phone' => $request->office_phone,
        'cell_phone' => $request->cell_phone,
        'contact_note' => $request->contact_notes
      ]);
      if ($result){
        return response()->json(['msg' => 'Success', 'error' => 0]);
      }
    }

    public function editContact($id){
        $row = Contact::find($id);
        return view('suppliers.contacts.edit',compact('id','row'));
    }

    public function updateContact(Request $request,$id){
        $contact = Contact::find($id);
        $contact->supplier_id = $request->supplier_id;
        $contact->name = $request->name;
        $contact->title = $request->title;
        $contact->email = $request->email;
        $contact->office_phone = $request->office_phone;
        $contact->cell_phone = $request->cell_phone;
        $contact->contact_note = $request->contact_note;
        $contact->update();
        return response()->json(['msg' => 'Success', 'error' => 0]);
    }

    public function deleteContact($id){
       $contact = Contact::find($id);

       if (!empty($contact)) {
            $contact->delete();
            return response()->json(['msg' => 'Success', 'error' => 0]);
       }
       else{
        return response()->json(['msg' => 'Something went wrong!', 'error' => 1]);
       }
    }

    // account notes
    public function noteList($id){
        $accountNotes = SupplierAccountNote::where('supplier_id',$id)->leftJoin('users',function($join){
            $join->on('users.id','=','supplier_account_notes.user');
        })->select('supplier_account_notes.*','users.name as user')->get();
        return Datatables::of($accountNotes)
            ->addIndexColumn()
            ->addColumn('action', function($accountNote){
                $btn = '';
                $url = route('suppliers.editNote',$accountNote->id);
                $btn .= '<a href="javascript:void(0);" onclick="getModal(\''.$url.'\')" class="edit btn btn-primary btn-sm mr-2">Edit</a>';
                $btn .= '<a onclick="deleteNote(\''.route('suppliers.deleteNote',$accountNote->id).'\')" class="delete btn btn-danger btn-sm text-white">Delete</a>';
                return $btn;
            })
            ->editColumn('date',function($accountNote){
                return date("m-d-Y h:i",strtotime($accountNote->created_at));
            })
            ->rawColumns(['date','action'])
            ->make(true);
    }

    public function createNote($id){
        $users = User::pluck('name','id')->toArray();
        return view('suppliers.notes.create',compact('id','users'));
    }
    public function storeNote(Request $request){
      $result = SupplierAccountNote::create([
        'supplier_id' => $request->supplier_id,
        'event' => $request->event,
        'details' => $request->details,
        'user' => Auth::user()->id,
        // 'date_and_time' => $request->date_and_time,
      ]);
      if ($result) {
        return response()->json([
            'error' => 0,
            'msg' => 'Success'
        ]);
      }else{
           return response()->json([
            'error' => 0,
            'msg' => 'Something Went Wrong'
        ]);
      }
    }

    public function editNote($id){
        $row = SupplierAccountNote::find($id);
        $users = User::pluck('name','id')->toArray();
        return view('suppliers.notes.edit',compact('id','row','users'));
    }

    public function updateNote(Request $request,$id){
        $Eventnote = SupplierAccountNote::find($id);
        $Eventnote->supplier_id = $request->supplier_id;
        $Eventnote->event = $request->event;
        $Eventnote->details = $request->details;
        // $Eventnote->date_and_time = $request->date_and_time;
        $Eventnote->user = $request->user;
        $Eventnote->update();
        return response()->json([
            'error' => 0,
            'msg' => 'Success'
        ]);
    }

    public function deleteNote($id){
       $event = SupplierAccountNote::find($id);
       if (!empty($event)) {
            $event->delete();
            return response()->json([
                'error' => 0,
                'msg' => 'Success'
            ]);
       }
       else{
            return response()->json([
                'error' => 1,
                'msg' => 'Something went wrong!'
            ]);
       }
    }
    // documents
    public function documentList($id){
        $documents = SupplierDocument::where('supplier_id',$id)->get();
        return Datatables::of($documents)
            ->addIndexColumn()
            ->editColumn('date',function($document){
                return date("m-d-Y H:i",strtotime($document->created_at));
            })
            ->addColumn('action', function($document){
                    $btn = '';
                    $btn .= '<a href="'.route('suppliers.document.download',$document->id).'" class="edit btn btn-primary btn-sm mr-2">Download</a>';
                    $btn .= '<a onclick="deleteDocument(\''.route('suppliers.deleteDocument',$document->id).'\')" class="delete btn btn-danger btn-sm text-white">Delete</a>';
                    return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function createDocument($id){
        return view('suppliers.documents.create',compact('id'));
    }
    public function storeDocument(Request $request){
        if($request->hasFile('document'))
        {
            $file = $request->file('document');
            $docPath = public_path('/supplier_documents/');
            if (!file_exists($docPath)) {
                mkdir($docPath, 0775, true);
            }
            $document = md5(time().'_'.$file->getClientOriginalName()).'.'.$file->getClientOriginalExtension();
            $file->move($docPath,$document);
        }
      $result = SupplierDocument::create([
        'supplier_id' => $request->supplier_id,
        'type' => $request->type,
        'name' => $request->name,
        'description' => $request->description,
        'date' => date('d-m-y H:i:s'),
        'document' => $document ?? ''
      ]);
      if ($result) {
        return response()->json([
            'error' => 0,
            'msg' => 'Success'
        ]);
      }else{
           return response()->json([
            'error' => 1,
            'msg' => 'Something Went Wrong'
        ]);
      }
    }

    public function downloadDocument($id)
    {
        $getFile = SupplierDocument::find($id);
        $file = public_path().'/supplier_documents/'.$getFile->document;

        if(File::exists($file)){
            return Response::download($file);
            session()->flash('Success');
        }
        else{
            return back()->with(['error' => 'No file is there!']);
        }

    }
    public function deleteDocument($id){
        $document = SupplierDocument::find($id);

        if (!empty($document)) {
             $document->delete();
             return response()->json([
                 'error' => 0,
                 'msg' => 'Success'
             ]);
        }
        else{
             return response()->json([
                 'error' => 1,
                 'msg' => 'Something went wrong!'
             ]);
        }
     }


     public function linkList($id){
         $links = SupplierDocumentsLink::where('supplier_id',$id)->get();
         return Datatables::of($links)
             ->addIndexColumn()
             ->editColumn('date',function($link){
                return date("m-d-Y H:i",strtotime($link->created_at));
            })
            ->editColumn('url',function($link){
                $url = $link->url;
                if(substr($link->url,0,3) != "http"){
                    $url = "http://".$link->url;

                }
                return '<a href="'.$url.'">'.$url.'</a>';
            })
             ->addColumn('action', function($link){
                     $btn = '';
                     $url = route('suppliers.editLink',$link->id);
                     $btn .= '<a href="javascript:void(0);" onclick="getModal(\''.$url.'\')"  class="edit btn btn-primary btn-sm mr-2">Edit</a>';
                     $btn .= '<a  href="javascript:void(0);" onclick="deleteLink(\''.route('suppliers.deleteLink',$link->id).'\')"  class="delete btn btn-danger btn-sm">Delete</a>';
                     return $btn;
             })
             ->rawColumns(['action','url'])
             ->make(true);
     }

     public function createLink($id){
         return view('suppliers.document_links.create',compact('id'));
     }
     public function storeLink(Request $request){
            $url = $request->url;
            // if(substr($request->url,0,7) != "http://"){
            //     $url = "http://".$request->url;
            // }
         $result = SupplierDocumentsLink::create([
             'supplier_id' => $request->supplier_id,
             'url' => $url,
             'name' => $request->name,
             'description' => $request->description,
           ]);
       if ($result) {
         return response()->json(['msg' => 'Success', 'error' => 0]);
       }
     }

     public function editLink($id){
         $row = SupplierDocumentsLink::find($id);
         return view('suppliers.document_links.edit',compact('id','row'));
     }

     public function updateLink(Request $request,$id){
         $link = SupplierDocumentsLink::find($id);
         $link->supplier_id = $request->supplier_id;
         $link->url = $request->url;
         $link->name = $request->name;
         $link->description = $request->description;
         $link->update();
         return response()->json(['msg' => 'Success', 'error' => 0]);
     }

     public function deleteLink($id){
        $link = SupplierDocumentsLink::find($id);
        if (!empty($link)) {
             $link->delete();
             return response()->json(['msg' => 'Success', 'error' => 0]);
        }
        else{
         return response()->json(['msg' => 'Something went wrong!', 'error' => 1]);
        }
     }

     public function getmasterproductsbysupplier(Request $request,$id = ''){

        $header = DB::table('csv_header')->where('supplier_id',$id)->first();
        $supplier = '';
        if($header){
            $supplier = $header->map_type;
        }
        if ($request->ajax()) {
            $data = [];
            if(isset($supplier) && $supplier == "supplier_dot" ){
                $data = DB::table('supplier_dot')
                ->leftJoin('master_product',function($join){
                    $join->on('supplier_dot.UPC', '=','master_product.upc');
                })
                ->leftJoin('etailer_availability',function($join){
                    $join->on('master_product.etailer_availability', '=','etailer_availability.id');
                })
                ->select('supplier_dot.UPC as upc','master_product.upc as upc2','supplier_dot.id as supplier_product_id','master_product.ETIN as ETIN','supplier_dot.sync_master','supplier_dot.brand','supplier_dot.item_description','supplier_dot.dot_item as item_number','supplier_dot.availability as supplier_status','master_product.supplier_product_number as supplier_product_number','master_product.etailer_availability','etailer_availability.etailer_availability as etailer_status','master_product.warehouses_assigned as warehouse_assigned','supplier_dot.my_case_10000 as cost','master_product.id as master_product_id')
                ->take(10)->get();
            }
            if(isset($supplier) && $supplier == "supplier_hersley" ){
                $data = DB::table('supplier_hersley')
                ->leftJoin('master_product',function($join){
                    $join->on('supplier_hersley.UPC', '=','master_product.upc');
                })
                ->leftJoin('etailer_availability',function($join){
                    $join->on('master_product.etailer_availability', '=','etailer_availability.id');
                })
                ->select('supplier_hersley.UPC as upc','master_product.upc as upc2','supplier_hersley.id as supplier_product_id','master_product.ETIN as ETIN','supplier_hersley.sync_master','supplier_hersley.brand','supplier_hersley.description as item_description','supplier_hersley.item_no as item_number','master_product.supplier_status','master_product.supplier_product_number as supplier_product_number','master_product.etailer_availability','master_product.warehouses_assigned','master_product.etailer_availability','etailer_availability.etailer_availability as etailer_status','master_product.warehouses_assigned as warehouse_assigned','supplier_hersley.price_sch_2_1000_5_999_lbs as cost','master_product.id as master_product_id')

                ->get();
            }
            if(isset($supplier) && $supplier == "supplier_kehe" ){
                $data = DB::table('supplier_kehe')
                ->leftJoin('master_product',function($join){
                    $join->on('supplier_kehe.UPC', '=','master_product.upc');
                })
                ->leftJoin('etailer_availability',function($join){
                    $join->on('etailer_availability.id', '=','master_product.etailer_availability');
                })
                ->select('supplier_kehe.UPC as upc','master_product.upc as upc2','supplier_kehe.id as supplier_product_id','supplier_kehe.sync_master','master_product.ETIN as ETIN','supplier_kehe.BRAND as brand','supplier_kehe.DESCRIPTION as item_description','supplier_kehe.item_number as item_number','supplier_kehe.list_status as supplier_status','master_product.supplier_product_number as supplier_product_number','master_product.warehouses_assigned','master_product.etailer_availability','master_product.warehouses_assigned','supplier_kehe.acquisition_cost as cost','supplier_kehe.etailer_stock_status','master_product.id as master_product_id')
                ->get();
            }
            if(isset($supplier) && $supplier == "supplier_mars"){
                $data = DB::table('supplier_mars')
                ->leftJoin('master_product',function($join){
                    $join->on('supplier_mars.twelve_digit_unit_UPC', '=','master_product.upc');
                })
                ->leftJoin('etailer_availability',function($join){
                    $join->on('etailer_availability.id', '=','master_product.etailer_availability');
                })

                ->select('supplier_mars.twelve_digit_unit_UPC as upc','master_product.upc as upc2','master_product.ETIN as ETIN','supplier_mars.id as supplier_product_id','supplier_mars.brand','supplier_mars.sync_master','supplier_mars.product as item_description','supplier_mars.ITEM_NO as item_number','master_product.supplier_status as supplier_status','master_product.supplier_product_number as supplier_product_number','master_product.etailer_availability','master_product.warehouses_assigned',
                'etailer_availability.etailer_availability as e_tailer_status','supplier_mars.PRICE_AND_WEIGHT_SCHEDULE_10_22_PALLETS as cost','master_product.id as master_product_id'
                )
                ->get();
            }
            if(isset($supplier) && $supplier == "supplier_dryers" ){
                $data = DB::table('supplier_dryers')
                ->leftJoin('master_product',function($join){
                    $join->on('supplier_dryers.UPC', '=','master_product.upc');
                })
                ->leftJoin('etailer_availability',function($join){
                    $join->on('etailer_availability.id', '=','master_product.etailer_availability');
                })
                ->select('supplier_dryers.UPC as upc','master_product.upc as upc2','supplier_dryers.id as supplier_product_id','supplier_dryers.fanc_name as item_description','supplier_dryers.sync_master','master_product.ETIN as ETIN','supplier_dryers.brand_name as brand','master_product.supplier_status','master_product.etailer_availability','master_product.warehouses_assigned','master_product.id as master_product_id')
                ->get();

            }
            if(isset($supplier) && $supplier == "3pl_client_product" ){
                $data = DB::table('3pl_client_product')
                ->leftJoin('master_product',function($join){
                    $join->on('3pl_client_product.supplier_product_number', '=','master_product.supplier_product_number');
                })
                ->leftJoin('etailer_availability',function($join){
                    $join->on('etailer_availability.id', '=','master_product.etailer_availability');
                })
                ->select('3pl_client_product.ETIN','3pl_client_product.brand','3pl_client_product.product_description as item_description','3pl_client_product.supplier_product_number as item_number','master_product.supplier_status as supplier_status','master_product.supplier_product_number as supplier_product_number','master_product.etailer_availability','master_product.warehouses_assigned',
                'etailer_availability.etailer_availability as e_tailer_status','master_product.id as master_product_id'
                )
                ->where('client_id',$id)
                ->get();
            }
            if(isset($supplier) && $supplier == "supplier_miscellaneous" ){
                $data = DB::table('supplier_miscellaneous')
                ->leftJoin('master_product',function($join){
                    $join->on('supplier_miscellaneous.supplier_product_number', '=','master_product.supplier_product_number');
                })
                ->leftJoin('etailer_availability',function($join){
                    $join->on('etailer_availability.id', '=','master_product.etailer_availability');
                })

                ->select('supplier_miscellaneous.ETIN','supplier_miscellaneous.brand','supplier_miscellaneous.product_description as item_description','supplier_miscellaneous.supplier_product_number as item_number','master_product.supplier_status as supplier_status','master_product.supplier_product_number as supplier_product_number','master_product.etailer_availability','master_product.warehouses_assigned',
                'etailer_availability.etailer_availability as e_tailer_status','master_product.id as master_product_id'
                )
                ->where('supplier_id',$id)
                ->get();
            }
            return Datatables::of($data)
                    ->addColumn('action', function($row) use($supplier){
                        if ($supplier == "supplier_kehe") {
                            $btn = '';
                            if($row->upc != $row->upc2){
                                $btn.= '<a href="javascript:void(0)" onclick="syncKeheWithMasterProduct(\''.$row->supplier_product_id.'\')" id="syncKeheWithMasterProduct" class="edit btn btn-primary btn-sm">Add New</a>';
                            }
                            else{
                                $btn.='<a href="'.route('editmasterproduct',$row->master_product_id).'" class="edit btn btn-primary btn-sm" target="_blank">Edit Product</a>';
                                // $btn .= '<a href="javascript:void(0)" onclick="resyncKeheWithMasterProduct(\''.$row->supplier_product_id.'\')" id="resyncKeheWithMasterProduct" class="btn btn-raised btn-raised-success m-1">Already SYNC</a>';
                            }
                            return $btn;
                        }
                        if($supplier == "supplier_dot"){
                            $btn = '';
                            if($row->upc != $row->upc2){
                                $btn .= '<a href="javascript:void(0)" onclick="syncdotproduct(\''.$row->supplier_product_id.'\')" id="syncDotWithMasterProduct" class="edit btn btn-primary btn-sm">Add New</a>';

                            }
                            else{
                                // return $row->etailer_status ?? '';
                                $btn .= '<a href="javascript:void(0)" onclick="resyncdotWithMasterProduct(\''.$row->supplier_product_id.'\')" id="resyncDotWithMasterProduct" class="btn btn-raised btn-raised-success m-1">Already SYNC</a>';
                            }
                             return $btn;

                        }
                        if($supplier == "supplier_hersley"){
                            $btn = '';
                            if($row->sync_master == 0){
                                $btn = '<a href="javascript:void(0)" onclick="syncHarsheyWithMasterProduct(\''.$row->supplier_product_id.'\')" id="syncHarsheyWithMasterProduct" class="edit btn btn-primary btn-sm">Add New</a>';

                            } else {
                                // return $row->etailer_status ?? '';
                                $btn = '<a href="javascript:void(0)" onclick="resyncHarsheyWithMasterProduct(\''.$row->supplier_product_id.'\')" id="resyncHarsheyWithMasterProduct" class="btn btn-raised btn-raised-success m-1">Already SYNC</a>';
                            }
                             return $btn;
                        }
                        if($supplier == "supplier_dryers"){
                            $btn = '';
                            if($row->upc != $row->upc2){
                                $btn .= '<a href="javascript:void(0)" onclick="syncDryersWithMasterProduct(\''.$row->supplier_product_id.'\')" id="syncDotWithMasterProduct" class="edit btn btn-primary btn-sm">Add New</a>';

                            }
                            else{
                                // return $row->etailer_status ?? '';
                                $btn .= '<a href="javascript:void(0)" onclick="resyncDryersWithMasterProduct(\''.$row->supplier_product_id.'\')" id="resyncDotWithMasterProduct" class="btn btn-raised btn-raised-success m-1">Already SYNC</a>';
                            }
                             return $btn;
                        }
                        if($supplier == "supplier_mars"){
                            $btn = '';
                            if($row->upc != $row->upc2){
                                $btn .= '<a href="javascript:void(0)" onclick="syncMarsWithMasterProduct(\''.$row->supplier_product_id.'\')" id="syncDotWithMasterProduct" class="edit btn btn-primary btn-sm">Add New</a>';

                            }
                            else{
                                // return $row->etailer_status ?? '';
                                $btn .= '<a href="javascript:void(0)" onclick="resyncMarsWithMasterProduct(\''.$row->supplier_product_id.'\')" id="resyncDotWithMasterProduct" class="btn btn-raised btn-raised-success m-1">Already SYNC</a>';
                            }
                             return $btn;
                        }
                    })

                    ->rawColumns(['item_number','supplier_status','cost','warehouses_assigned','action'])
                    ->make(true);
        }
    }

    public function show(){
        //
    }

    public function upload_bulk_product($id){
        $csvHeader = CsvHeader::where('supplier_id', $id)->first();
        return view('cranium.upload_supplier_bulk_product',['supplier_id' => $id,'csvHeader' => $csvHeader]);
    }

    public function upload_supplier_product2(Request $request){
		$errorRows = 0;
		$successRows = 0;
        $skiprows = 0;

		$supplier_id = $request->supplier_id;
        $table_name = $request->supplier_name;
        if($table_name == 'supplier_miscellaneous'){
            $table_name = 'supplier_miscellaneous';
        }
        if($table_name == 'supplier_hersley'){
            $table_name = 'supplier_hersley';
        }
        // dd($table_name);
		$draf_option = NULL;
		if(isset($request->select_option) && $request->select_option == 'Upload & Edit') $draf_option = 'd';
        // dd($request->supplier_name);
		$csv_header = DB::table('csv_header')->where('supplier_id', $supplier_id)->where('map_type',$request->supplier_name)->get();
		$map_json_array = json_decode($csv_header[0]->{'map_data'});
		$file = $request->file('csv_file');
		$path = $file->getRealPath();
        $UploadHistory = new UploadHistory;
		$UploadHistory->client_id = $supplier_id;
		$UploadHistory->save();
        Excel::import(new SupplierProductImport($map_json_array,$supplier_id,$UploadHistory->id,$table_name), request()->file('csv_file'));

		$data = array_map('str_getcsv', file($path));

		$data_header = (new HeadingRowImport)->toArray(request()->file('csv_file'));
		$csv_data_for_header = $data_header[0];
		$csv_data = array_slice($data, 1, 500000);
		$csv_data_count = count($csv_data);
		$csvheader = $csv_data_for_header[0];
		foreach($csv_data as $csv_data_single){
			$keyarray = null;
			$keynumarray = null;
			foreach ($map_json_array as $key=>$value){
				if($value){
					if ($keynum = array_search(strtolower($value), array_map('strtolower', $csvheader))) {
				 		$keyarray[] = $key;
				 		$keynumarray[]= $keynum;
					}
				}
			}
			$insertarray = null;

			foreach($keynumarray as $keynumsingle){
                if(isset($csv_data_single[$keynumsingle])){
                    $insertarray[] = htmlspecialchars_decode(str_replace("<br />", "",nl2br($csv_data_single[$keynumsingle])),ENT_SUBSTITUTE);
                }
			}

			$orderProductsData[] = array_combine ($keyarray , $insertarray);
			$NewProductArray = [];
            if($orderProductsData){
				foreach($orderProductsData as $row){
                    if($draf_option != 'd'){
                        if(isset($row['UPC']) && $row['UPC'] != ""){
                            $check_upc = DB::table($table_name)->where('UPC',$row['UPC'])->first();
                            if($check_upc){
                                DB::table($table_name)->where('UPC',$row['UPC'])->update($row);
                                $check_if_produt_upc = DB::table('master_product')->where('upc',$row['UPC'])->first();

                                if($table_name == 'supplier_dot'){
                                    $model = SupplierDot::where('UPC',$row['UPC'])->first();
                                }
                                if($table_name == 'supplier_dryers'){
                                    $model = SupplierDryers::where('UPC',$row['UPC'])->first();
                                }
                                if($table_name == 'supplier_kehe'){
                                    $model = SupplierKehe::where('UPC',$row['UPC'])->first();
                                }
                                if($table_name == 'supplier_miscellaneous'){
                                    $model = SupplierMiscellaneous::where('UPC',$row['UPC'])->first();
                                }
                                if($table_name == 'supplier_hersley'){
                                    $model = SupplierHershey::where('UPC',$row['UPC'])->first();
                                }

                                if(!$check_if_produt_upc){
                                    $pro_rerult = $model->addMasterProduct($draf_option);
                                }else{
                                    $pro_rerult = $model->updateMasterProduct($draf_option);
                                }
                                $model->updateETIN($pro_rerult->ETIN);
                            }else{
                                $product_id = DB::table($table_name)->insertGetId($row);
                                $supplier_pro =  DB::table($table_name)->where('id',$product_id)->first();

                                if($table_name == 'supplier_dot'){
                                    $model = SupplierDot::where('UPC',$row['UPC'])->first();
                                }
                                if($table_name == 'supplier_dryers'){
                                    $model = SupplierDryers::where('UPC',$row['UPC'])->first();
                                }
                                if($table_name == 'supplier_kehe'){
                                    $model = SupplierKehe::where('UPC',$row['UPC'])->first();
                                }
                                if($table_name == 'supplier_miscellaneous'){
                                    $model = SupplierMiscellaneous::where('UPC',$row['UPC'])->first();
                                }
                                if($table_name == 'supplier_hersley'){
                                    $model = SupplierHershey::where('UPC',$row['UPC'])->first();
                                }

                                $check_if_produt_upc = DB::table('master_product')->where('upc',$row['UPC'])->first();
                                if(!$check_if_produt_upc){
                                    $pro_rerult = $model->addMasterProduct($draf_option);
                                }else{
                                    $pro_rerult = $model->updateMasterProduct($draf_option);
                                }
                                $model->updateETIN($pro_rerult->ETIN);
                            }
                            $successRows += 1;
                        }
                        elseif(isset($row['twelve_digit_unit_UPC']) && $row['twelve_digit_unit_UPC'] != ""){

                            $model = SupplierMars::where('twelve_digit_unit_UPC',$row['twelve_digit_unit_UPC'])->first();

                            $check_upc = DB::table($table_name)->where('twelve_digit_unit_UPC',$row['twelve_digit_unit_UPC'])->first();

                            if($check_upc){
                                DB::table($table_name)->where('twelve_digit_unit_UPC',$row['twelve_digit_unit_UPC'])->update($row);
                                $check_if_produt_upc = DB::table('master_product')->where('upc',$row['twelve_digit_unit_UPC'])->first();

                                if(!$check_if_produt_upc){
                                    $pro_rerult = $model->addMasterProduct($draf_option);
                                }else{
                                    $pro_rerult = $model->updateMasterProduct($draf_option);
                                }
                                $model->updateETIN($pro_rerult->ETIN);
                            }else{

                                $product_id = DB::table($table_name)->insertGetId($row);
                                $supplier_pro =  DB::table($table_name)->where('id',$product_id)->first();

                                if($table_name == 'supplier_mars'){
                                    $model = SupplierMars::where('twelve_digit_unit_UPC',$row['twelve_digit_unit_UPC'])->first();
                                }

                                $check_if_produt_upc = DB::table('master_product')->where('upc',$row['twelve_digit_unit_UPC'])->first();

                                if(!$check_if_produt_upc){
                                    $pro_rerult = $model->addMasterProduct($draf_option);
                                }else{
                                    $pro_rerult = $model->updateMasterProduct($draf_option);
                                }
                                $model->updateETIN($pro_rerult->ETIN);
                            }
                            $successRows += 1;
                        }
                        elseif(isset($row['material_number']) && $row['material_number'] != ""){

                            if($table_name == 'supplier_nestle'){
                                $model = SupplierNestle::where('material_number',$row['material_number'])->first();
                            }

                            $check_upc = DB::table($table_name)->where('material_number',$row['material_number'])->first();

                            if($check_upc){
                                DB::table($table_name)->where('material_number',$row['material_number'])->update($row);
                                $check_if_produt_upc = DB::table('master_product')->where('upc',$row['material_number'])->first();

                                if(!$check_if_produt_upc){
                                    $pro_rerult = $model->addMasterProduct($draf_option);
                                }else{
                                    $pro_rerult = $model->updateMasterProduct($draf_option);
                                }
                                // $model->updateETIN($pro_rerult->ETIN);
                            }else{

                                $product_id = DB::table($table_name)->insertGetId($row);
                                $supplier_pro =  DB::table($table_name)->where('id',$product_id)->first();

                                if($table_name == 'supplier_nestle'){
                                    $model = SupplierNestle::where('material_number',$row['material_number'])->first();
                                }

                                $check_if_produt_upc = DB::table('master_product')->where('upc',$row['material_number'])->first();

                                if(!$check_if_produt_upc){
                                    $pro_rerult = $model->addMasterProduct($draf_option);
                                }else{
                                    $pro_rerult = $model->updateMasterProduct($draf_option);
                                }
                                // $model->updateETIN($pro_rerult->ETIN);
                            }
                            $successRows += 1;
                        }else{
                            $skiprows += 1;
                        }
                    }else{
                        $product_id = DB::table($table_name)->insertGetId($row);
                        // $supplier_pro =  DB::table($table_name)->where('id',$product_id)->first();

                        if($table_name == 'supplier_dot'){
                            $model = SupplierDot::find($product_id);
                        }
                        if($table_name == 'supplier_dryers'){
                            $model = SupplierDryers::find($product_id);
                        }
                        if($table_name == 'supplier_kehe'){
                            $model = SupplierKehe::find($product_id);
                        }
                        if($table_name == 'supplier_miscellaneous'){
                            $model = SupplierMiscellaneous::find($product_id);
                        }
                        if($table_name == 'supplier_hersley'){
                            $model = SupplierHershey::find($product_id);
                        }
                        if($table_name == 'supplier_mars'){
                            $model = SupplierMars::find($product_id);
                        }
                        if($table_name == 'supplier_nestle'){
                            $model = SupplierNestle::find($product_id);
                        }
                        $pro_rerult = $model->addMasterProduct($draf_option);
                        $model->updateETIN($pro_rerult->ETIN);
                        $successRows += 1;
                    }
				}
			}
			$orderProductsData = null;
		}
		if($errorRows>0){
			return respnse()->json([
				'error' => 1,
				'msg' => $successRows." Rows Inserted Sucessfully, ".$errorRows." don't have required values like (Brand, Product Type, Unit Size, Unit Description, Pack Form Count, Units in Pack, Item Form Description, Product Category, Product Temperature, Supplier Product Number, e-tailer Availability, Current Supplier, Supplier Status, Cost, Warehouse(s) Assigned, Status)"
			]);
		}

		return response()->json([
			'error' => 0,
			'msg' =>  $successRows." Rows Inserted and ". $skiprows. " Skipped"
		]);
	}

    public function upload_supplier_product(Request $request){
		$errorRows = 0;
		$successRows = 0;
		$skiprows = 0;

		$supplier_id = $request->supplier_id;
        $table_name = $request->supplier_name;

		$draf_option = NULL;
		if(isset($request->select_option) && $request->select_option == 'Upload & Edit') $draf_option = 'd';
		$csv_header = DB::table('csv_header')->where('supplier_id', $supplier_id)->where('map_type',$request->supplier_name)->get();
		$map_json_array = json_decode($csv_header[0]->{'map_data'});
		$file = $request->file('csv_file');
		$path = $file->getRealPath();
		$UploadHistory = new UploadHistory;
		$UploadHistory->client_id = $supplier_id;
		$UploadHistory->save();

        Excel::import(new SupplierProductImport($map_json_array,$supplier_id,$UploadHistory->id,$table_name), request()->file('csv_file'));



		$result = UploadHistory::find($UploadHistory->id);
        $errorRows = 0;
        $successRows = 0;
        if($result){
			if($result->failed_products_count > 0){
				$errorRows = $result->failed_products_count;
			}

			if($result->total_products > 0){
				$successRows = $result->total_products;
			}
		}

		$result->delete();
        $msg = $successRows." Rows Inserted Sucessfully, ".$errorRows." rows don't have required values like (Supplier Product Number) OR Dublicate Supplier Product Number";

		if($errorRows>0){
			return response()->json([
				'error' => 1,
				'msg' => $msg
			]);
		}

		return response()->json([
			'error' => 0,
			'msg' => $successRows." Rows Inserted and ".$skiprows." Skipped"
		]);
	}
    public function createPackageMaterial($id)
    {
        $supplier = Supplier::find($id);
        $mstrProd = new MasterProduct();
        $type ='package';
        $newetin = $mstrProd->getETIN('','package');
        $materialTypes = MaterialType::all();
        $producttemp = ProductTemperature::orderBy('product_temperature','ASC')->pluck('product_temperature')->toArray();
        $client = Client::orderBy('company_name','ASC')->pluck('company_name','id')->toArray();
        $warehouse = WareHouse::orderBy('warehouses','ASC')->pluck('warehouses')->toArray();
        return view('suppliers.packaging_material.create',compact('materialTypes','producttemp','client','newetin','warehouse','supplier','type'));
    }
    public function editpackagematerial($id,$type='package'){
        //$mstrProd = new MasterProduct();
        //$newetin = $mstrProd->getETIN('','package');
        //$type ='package';
        $packagingMatirials = $this->service->editpackagematerial($id);
        $materialTypes = MaterialType::all();
        $producttemp = ProductTemperature::orderBy('product_temperature','ASC')->pluck('product_temperature')->toArray();
        $client = Client::orderBy('company_name','ASC')->pluck('company_name','id')->toArray();
        $warehouse = WareHouse::orderBy('warehouses','ASC')->pluck('warehouses')->toArray();

        $channels = ClientChannelConfiguration::where('client_id', $packagingMatirials->clients_assigned)->get()->toArray();
        if (isset($packagingMatirials->clients_assigned)) {
            $result_obj = MasterProduct::whereRaw('FIND_IN_SET(' . $packagingMatirials->clients_assigned . ',lobs)');
            $result_obj->select(DB::raw('CONCAT(product_listing_name," - ", ETIN) as product_name'),'ETIN','id','unit_description','item_form_description');	
        }
		$products = !isset($result_obj) ? array() : $result_obj->get()->toArray();

        $all_unit_descs = [];
        $all_item_form_descs = []; 
        foreach($products as $mp) {
            if (isset($mp['unit_description']) && $mp['unit_description'] != '' 
                && !in_array($mp['unit_description'], $all_unit_descs)) {
                    array_push($all_unit_descs, $mp['unit_description']);
            }

            if (isset($mp['item_form_description']) && $mp['item_form_description'] != '' 
                && !in_array($mp['item_form_description'], $all_item_form_descs)) {
                    array_push($all_item_form_descs, $mp['item_form_description']);
            }
        }

        $wh_td_count = [];
        $mwtc = MaterialWarehouseTdCount::where('material_id', $id)->get();
        if (isset($mwtc) && count($mwtc) > 0) {
            foreach($mwtc as $m) {
                $wh_td_count[$m->warehouse->warehouses][$m->transit_days] = $m->count;
            }
        }

        return view('suppliers.packaging_material.edit',
            compact('packagingMatirials','materialTypes',
            'producttemp','client','warehouse','type', 'channels', 'products', 'wh_td_count',
            'all_item_form_descs', 'all_unit_descs'));
    }
    public function editpackagemateriallist($id,$type='packaginglist'){
        //$mstrProd = new MasterProduct();
        //$newetin = $mstrProd->getETIN('','package');
        //$type ='package';
        $packagingMatirials = $this->service->editpackagematerial($id);
        $materialTypes = MaterialType::all();
        $producttemp = ProductTemperature::orderBy('product_temperature','ASC')->pluck('product_temperature')->toArray();
        $client = Client::orderBy('company_name','ASC')->pluck('company_name','id')->toArray();
        $warehouse = WareHouse::orderBy('warehouses','ASC')->pluck('warehouses')->toArray();

        $channels = ClientChannelConfiguration::where('client_id', $packagingMatirials->clients_assigned)->get()->toArray();
        if (isset($packagingMatirials->clients_assigned)) {
            $result_obj = MasterProduct::whereRaw('FIND_IN_SET(' . $packagingMatirials->clients_assigned . ',lobs)');
            $result_obj->select(DB::raw('CONCAT(product_listing_name," - ", ETIN) as product_name'),'ETIN','id','unit_description','item_form_description');	
        }
		$products = !isset($result_obj) ? array() : $result_obj->get()->toArray();

        $all_unit_descs = [];
        $all_item_form_descs = []; 
        foreach($products as $mp) {
            if (isset($mp['unit_description']) && $mp['unit_description'] != '' 
                && !in_array($mp['unit_description'], $all_unit_descs)) {
                    array_push($all_unit_descs, $mp['unit_description']);
            }

            if (isset($mp['item_form_description']) && $mp['item_form_description'] != '' 
                && !in_array($mp['item_form_description'], $all_item_form_descs)) {
                    array_push($all_item_form_descs, $mp['item_form_description']);
            }
        }

        $wh_td_count = [];
        $mwtc = MaterialWarehouseTdCount::where('material_id', $id)->get();
        if (isset($mwtc) && count($mwtc) > 0) {
            foreach($mwtc as $m) {
                $wh_td_count[$m->warehouse->warehouses][$m->transit_days] = $m->count;
            }
        }

        return view('suppliers.packaging_material.edit',
            compact('packagingMatirials','materialTypes',
            'producttemp','client','warehouse','type', 'channels', 'products', 'wh_td_count',
            'all_item_form_descs', 'all_unit_descs'));
    }
    public function addPackageMaterialStore (Request $request){
        if($request->product_temperature){
            $explodearray = explode('-', $request->ETIN);
            $etinmid = NULL;
            if (count($explodearray) > 1){
                $insertmasterproduct['ETIN'] = end($explodearray);
                $etinmid = $explodearray[1];
            } else {
                $insertmasterproduct['ETIN'] = $request->ETIN;
            }
            if($request->product_temperature == "Frozen"){
                $insertmasterproduct['ETIN'] = 'ETFZ-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
            } else if($request->product_temperature == "Dry-Strong"){
                $insertmasterproduct['ETIN'] = 'ETDS-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
            } else if($request->product_temperature == "Refrigerated"){
                $insertmasterproduct['ETIN'] = 'ETRF-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
            } else if($request->product_temperature == "Beverages"){
                $insertmasterproduct['ETIN'] = 'ETBV-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
            } else if($request->product_temperature == "Dry-Perishable"){
                $insertmasterproduct['ETIN'] = 'ETDP-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
            } else if($request->product_temperature == "Dry-Fragile"){
                $insertmasterproduct['ETIN'] = 'ETDF-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
            } else if($request->product_temperature == "Thaw & Serv"){
                $insertmasterproduct['ETIN'] = 'ETTS-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
            } else if($request->product_temperature == "Packaging"){
                $insertmasterproduct['ETIN'] = 'ETPM-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
            }
            else {
                $insertmasterproduct['ETIN'] = 'ETOT-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
            }

            $request['ETIN'] = $insertmasterproduct['ETIN'];
        }
        $packagingMatirials = $this->service->addPackageMaterialStore($request->all());
        if(!$packagingMatirials)
        {
            return response()->json([
                'error' => 1,
                'msg' => 'Product Description shoud be unique',
            ]);   
        }
        $this->add_edit_wh_td_count($request, $packagingMatirials->id, false);
        if($request->item_form_description == 'kit')
        {
            $this->service->addKitPackageMaterialStore($request);
            return response()->json([
                'error' => 0,
                'msg' => 'Kit Added Sucessfully',
                'url' => url('/suppliers/'.$request->supplier_id.'/edit')
            ]);
        }
        return response()->json([
            'error' => 0,
            'msg' => 'Package & Material Added Sucessfully',
            'url' => url('/suppliers/'.$request->supplier_id.'/edit')
        ]);
    }

    private function add_edit_wh_td_count($request, $id, $is_edit) {

        $whs = WareHouse::all();
        $wh_td_count = [];
        foreach($whs as $wh_a) {
            $wh = strtolower($wh_a->warehouses);
            foreach(range(1, 5) as $td) {
                $col = $wh . '_td_' . $td;
                $val = $request->$col;
                if (isset($val) && $val !== '') {
                    $wh_td_count[$wh_a->id][$td] = $val;
                }
            }
        }

        if ($is_edit) {
            MaterialWarehouseTdCount::where('material_id', $id)->delete();
        }

        foreach($wh_td_count as $wh => $td_counts) {
            foreach($td_counts as $td => $count) {
                MaterialWarehouseTdCount::create([
                    'material_id' => $id, 
                    'wh_id' => $wh, 
                    'transit_days' => $td, 
                    'count' => $count
                ]);
            }
        }
    }

    public function updatePackageMaterial (Request $request,$id){

        $packagingMatirials = $this->service->updatePackageMaterial($request->all(),$id);
        if(!$packagingMatirials)
        {
            return response()->json([
                'error' => 1,
                'msg' => 'Product Description shoud be unique',
            ]);   
        }
        $this->add_edit_wh_td_count($request, $packagingMatirials->id, true);
        if($request->item_form_description == 'kit')
        {
            $this->service->updateKitPackageMaterial($request);
           if($request->pagetype == 'packaginglist')
           {
                return response()->json([
                    'error' => 0,
                    'msg' => 'Package & Material Updated Sucessfully',
                    'url' => url('/listpackagingmatirial')
                ]);
           }
            return response()->json([
                'error' => 0,
                'msg' => 'Kit Added Sucessfully',
                'url' => url('/suppliers/'.$request->supplier_id.'/edit')
            ]);
        }
        if($request->item_form_description == 'packaginglist')
        {
            return response()->json([
                'error' => 0,
                'msg' => 'Package & Material Updated Sucessfully',
                'url' => url('/listpackagingmatirial')
            ]);
        }
        return response()->json([
            'error' => 0,
            'msg' => 'Package & Material Updated Sucessfully',
            'url' => url('/suppliers/'.$request->supplier_id.'/edit')
        ]);
    }
    public function destroyPackageMaterial(Request $request){
        $result = $this->service->destroyPackageMaterial($request->id);
        return response()->json([
            'result' => $result 
        ]);   
    }
}
