<?php

namespace App\Http\Controllers;

use File;
use App\User;
use DateTime;
use App\Carrier;
use App\Client;
use App\Contact;
use App\CarrierDocument;
use App\CarrierDynamicFee;
use App\CarrierAccountNote;
use App\CarrierStandardFee;
use App\CarrierAccounts;
use Illuminate\Http\Request;
use App\CarrierDocumentsLink;
use App\CarrierPeakSurcharge;
use App\EtailerService;
use App\ShippingServiceType;
use App\ProcessingGroups;
use App\WareHouse;
use App\CarrierOrderAccountAssignments;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use App\Http\Requests\UpdateCarrierRequest;
use DB;
use App\OrderAutomaticUpgrades;
use App\UpsZipZoneByWH;

class CarriersController extends Controller
{

    public function index()
    {
        $data = Carrier::all();
        return view('carriers.index',compact('data'));
    }

    public function create()
    {
        return view('carriers.create');
    }


    public function store(UpdateCarrierRequest $request)
    {
        // if(ReadWriteAccess('AddNewCarrier') == false){
		// 	return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		// }
       $input = $request->all();
       $carrier = Carrier::create([
           'company_name' => $input['company_name'],
            'main_point_of_contact' => $input['main_point_of_contact'],
            'client_address' => $input['client_address'],
            'client_address2' => $input['client_address2'],
            'client_city' => $input['client_city'],
            'client_state' => $input['client_state'],
            'client_zip' => $input['client_zip'],
            'client_phone' => $input['client_phone'],
            'client_email' => $input['client_email'],
            'client_website' => $input['client_website'],
            'client_status' => $input['client_status']
        ]);
        return response()->json([
            'error' => false,
            'msg' => 'Success',
            'url' => route('carriers.edit',$carrier->id)
        ]);
    }

    public function edit($id){
        $row = Carrier::find($id);
        $sfee = CarrierStandardFee::where('carrier_id',$id)->first();
        $surcharge = CarrierPeakSurcharge::where('carrier_id',$id)->first();
        $dynamic_fees_data = CarrierDynamicFee::where('carrier_id',$id)->get();
        $surcharge_data = CarrierPeakSurcharge::where('carrier_id',$id)->get();
        return view('carriers.edit',compact('row','dynamic_fees_data','surcharge_data','sfee','surcharge'));
    }

    public function update(UpdateCarrierRequest $request, $id){
        $input = $request->all();
        Carrier::where('id', $id)->update([
            'company_name' => $input['company_name'],
            'main_point_of_contact' => $input['main_point_of_contact'],
            'client_address' => $input['client_address'],
            'client_address2' => $input['client_address2'],
            'client_city' => $input['client_city'],
            'client_state' => $input['client_state'],
            'client_zip' => $input['client_zip'],
            'client_phone' => $input['client_phone'],
            'client_email' => $input['client_email'],
            'client_website' => $input['client_website'],
            'client_status' => $input['client_status'],
        ]);

        return response()->json(['msg' => 'Success', 'error' => false]);
    }

    public function updateConfig(Request $request){

        $data = request()->all();

        $result = CarrierStandardFee::updateOrCreate([
                'carrier_id' => $request->carrier_id,
        ],[
            'weight_gt_50_lbs_1' => $data['weight_gt_50_lbs_1'],
            'weight_gt_50_lbs_2' => $data['weight_gt_50_lbs_2'],
            'weight_gt_50_lbs_3' => $data['weight_gt_50_lbs_3'],
            'length_girth_gt_105_in_1' => $data['length_girth_gt_105_in_1'],
            'length_girth_gt_105_in_2' => $data['length_girth_gt_105_in_2'],
            'length_girth_gt_105_in_3' => $data['length_girth_gt_105_in_3'],
            'length_gt_48_in_1' => $data['length_gt_48_in_1'],
            'length_gt_48_in_2' => $data['length_gt_48_in_2'],
            'length_gt_48_in_3' => $data['length_gt_48_in_3'],
            'width_gt_30_in_1' => $data['width_gt_30_in_1'],
            'width_gt_30_in_2' => $data['width_gt_30_in_2'],
            'width_gt_30_in_3' => $data['width_gt_30_in_3'],
            'packaging_exeptions_1' => $data['packaging_exeptions_1'],
            'packaging_exeptions_2' => $data['packaging_exeptions_2'],
            'packaging_exeptions_3' => $data['packaging_exeptions_3'],
            'commercial_1' => $data['commercial_1'],
            'commercial_2' => $data['commercial_2'],
            'commercial_3' => $data['commercial_3'],
            'residential_1' => $data['residential_1'],
            'residential_2' => $data['residential_2'],
            'residential_3' => $data['residential_3'],
            'commercial_ground' => $data['commercial_ground'],
            'commercial_air' => $data['commercial_air'],
            'residential_ground' => $data['residential_ground'],
            'residential_air' => $data['residential_air'],
            'commercial_extended_ground' => $data['commercial_extended_ground'],
            'commercial_extended_air' => $data['commercial_extended_air'],
            'residential_extended_ground' => $data['residential_extended_ground'],
            'residential_extended_air' => $data['residential_extended_air'],
            'residential_surcharge_ground' => $data['residential_surcharge_ground'],
            'residential_surcharge_air' => $data['residential_surcharge_air'],
            'continental_us_ground' => $data['continental_us_ground'],
            'alaska_ground' => $data['alaska_ground'],
            'hawaii_ground' => $data['hawaii_ground'],
            'dry_ice_surcharge_ground' => $data['dry_ice_surcharge_ground'],
            'dry_ice_surcharge_air' => $data['dry_ice_surcharge_air'],
            'dim_weight_divisor' => $data['dim_weight_divisor'],
        ]);


        if(isset($data['sure_post_effective_date'])) $data['sure_post_effective_date'] = DateTime::createFromFormat('m/d/Y',$request->sure_post_effective_date)->format('Y-m-d');
        if(isset($data['sure_post_end_date'])) $data['sure_post_end_date'] = DateTime::createFromFormat('m/d/Y',$request->sure_post_end_date)->format('Y-m-d');
        if(isset($data['ground_residential_effective_date'])) $data['ground_residential_effective_date'] = DateTime::createFromFormat('m/d/Y',$request->ground_residential_effective_date)->format('Y-m-d');
        if(isset($data['ground_residential_end_date'])) $data['ground_residential_end_date'] = DateTime::createFromFormat('m/d/Y',$request->ground_residential_end_date)->format('Y-m-d');
        if(isset($data['air_residential_effective_date'])) $data['air_residential_effective_date'] = DateTime::createFromFormat('m/d/Y',$request->sure_post_effective_date)->format('Y-m-d');
        if(isset($data['air_residential_end_date'])) $data['air_residential_end_date'] = DateTime::createFromFormat('m/d/Y',$request->sure_post_end_date)->format('Y-m-d');
        if(isset($data['additional_handling_effective_date'])) $data['additional_handling_effective_date'] = DateTime::createFromFormat('m/d/Y',$request->sure_post_effective_date)->format('Y-m-d');
        if(isset($data['additional_handling_end_date'])) $data['additional_handling_end_date'] = DateTime::createFromFormat('m/d/Y',$request->sure_post_end_date)->format('Y-m-d');
        if(isset($data['large_package_effective_date'])) $data['large_package_effective_date'] = DateTime::createFromFormat('m/d/Y',$request->sure_post_effective_date)->format('Y-m-d');
        if(isset($data['large_package_end_date'])) $data['large_package_end_date'] = DateTime::createFromFormat('m/d/Y',$request->sure_post_effective_date)->format('Y-m-d');

        $surcharge = CarrierPeakSurcharge::updateOrCreate([
            'carrier_id' => $request->carrier_id
        ],[
            'sure_post_per_package' => $data['sure_post_per_package'],
            'sure_post_status' => $data['sure_post_status'],
            'sure_post_effective_date' => $data['sure_post_effective_date'] ?? null,
            'sure_post_end_date' => $data['sure_post_end_date'] ?? null,
            'ground_residential_per_package' => $data['ground_residential_per_package'],
            'ground_residential_status' => $data['ground_residential_status'],
            'ground_residential_effective_date' => $data['ground_residential_effective_date'] ?? null,
            'ground_residential_end_date' => $data['ground_residential_end_date'] ?? null,
            'air_residential_per_package' => $data['air_residential_per_package'],
            'air_residential_status' => $data['air_residential_status'],
            'air_residential_effective_date' => $data['air_residential_effective_date'] ?? null,
            'air_residential_end_date' => $data['air_residential_end_date'] ?? null,
            'additional_handling_per_package' => $data['additional_handling_per_package'],
            'additional_handling_status' => $data['additional_handling_status'],
            'additional_handling_effective_date' => $data['additional_handling_effective_date'] ?? null,
            'additional_handling_end_date' => $data['additional_handling_end_date'] ?? null,
            'large_package_per_package' => $data['large_package_per_package'],
            'large_package_status' => $data['large_package_status'],
            'large_package_effective_date' => $data['large_package_effective_date'] ?? null,
            'large_package_end_date' => $data['large_package_end_date'] ?? null,
        ]);

       if($result){
        return response()->json(['msg' => 'Success', 'error' => false]);
       }
       else{
        return response()->json(['msg' => 'Something went wrong!', 'error' => true]);
       }
    }

    public function destroy($id)
    {
        Carrier::find($id)->delete();
        return redirect()->back()->with('success','Deleted successfully');
    }

    public function contactList($id){
        $contacts = Contact::where('carrier_id',$id)->get();
        return Datatables::of($contacts)
            ->addIndexColumn()
            ->addColumn('action', function($contact){
                    $btn = '';
                    $url = route('carriers.editContact',$contact->id);
                    $btn .= '<a href="javascript:void(0);" onclick="getModal(\''.$url.'\')"  class="edit btn btn-primary btn-sm mr-2">Edit</a>';
                    $btn .= '<a  href="javascript:void(0);" onclick="deleteContact(\''.route('carriers.deleteContact',$contact->id).'\')"  class="delete btn btn-danger btn-sm">Delete</a>';
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

            $unsetAll = Contact::where('id','!=',$request->id)->where('carrier_id',$contact->carrier_id)->update(['is_primary' => 0]);
            return response()->json(['msg' => 'Success', 'error' => 0]);
        }
    }

    public function setPrimaryFee(Request $request){
        $contact = CarrierDynamicFee::find($request->id);
        if ($contact->is_primary) {
            $contact->is_primary = 0;
            $contact->update();
            return response()->json(['msg' => 'Success', 'error' => 0]);
        }
        else{
            $contact->is_primary = 1;
            $contact->update();

            $unsetAll = CarrierDynamicFee::where('id','!=',$request->id)->where('carrier_id',$contact->carrier_id)->update(['is_primary' => 0]);
            return response()->json(['msg' => 'Success', 'error' => 0]);
        }
    }

    public function createContact($id){
        return view('carriers.contacts.create',compact('id'));
    }

    public function storeContact(Request $request){
      $result = Contact::create([
        'carrier_id' => $request->carrier_id,
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
        return view('carriers.contacts.edit',compact('id','row'));
    }

    public function updateContact(Request $request,$id){
        $contact = Contact::find($id);
        $contact->carrier_id = $request->carrier_id;
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
        $accountNotes = CarrierAccountNote::where('carrier_id',$id)->leftJoin('users',function($join){
            $join->on('users.id','=','carrier_account_notes.user');
        })->select('carrier_account_notes.*','users.name as user')->get();
        return Datatables::of($accountNotes)
            ->addIndexColumn()
            ->addColumn('action', function($accountNote){
                $btn = '';
                $url = route('carriers.editNote',$accountNote->id);
                $btn .= '<a href="javascript:void(0);" onclick="getModal(\''.$url.'\')" class="edit btn btn-primary btn-sm mr-2">Edit</a>';
                $btn .= '<a onclick="deleteNote(\''.route('carriers.deleteNote',$accountNote->id).'\')" class="delete btn btn-danger btn-sm text-white">Delete</a>';
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
        return view('carriers.notes.create',compact('id','users'));
    }
    public function storeNote(Request $request){
      $result = CarrierAccountNote::create([
        'carrier_id' => $request->carrier_id,
        'event' => $request->event,
        'details' => $request->details,
        'user' => Auth::user()->id,
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
        $row = CarrierAccountNote::find($id);
        $users = User::pluck('name','id')->toArray();
        return view('carriers.notes.edit',compact('id','row','users'));
    }

    public function updateNote(Request $request,$id){
        $Eventnote = CarrierAccountNote::find($id);
        $Eventnote->carrier_id = $request->carrier_id;
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
       $event = CarrierAccountNote::find($id);
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
        $documents = CarrierDocument::where('carrier_id',$id)->get();
        return Datatables::of($documents)
            ->addIndexColumn()
            ->editColumn('date',function($document){
                return date("m-d-Y H:i",strtotime($document->created_at));
            })
            ->addColumn('action', function($document){
                    $btn = '';
                    $btn .= '<a href="'.route('carriers.document.download',$document->id).'" class="edit btn btn-primary btn-sm mr-2">Download</a>';
                    $btn .= '<a onclick="deleteDocument(\''.route('carriers.deleteDocument',$document->id).'\')" class="delete btn btn-danger btn-sm text-white">Delete</a>';
                    return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function createDocument($id){
        return view('carriers.documents.create',compact('id'));
    }
    public function storeDocument(Request $request){
        if($request->hasFile('document'))
        {
            $file = $request->file('document');
            $docPath = public_path('/carrier_documents/');
            if (!file_exists($docPath)) {
                mkdir($docPath, 0775, true);
            }
            $document = md5(time().'_'.$file->getClientOriginalName()).'.'.$file->getClientOriginalExtension();
            $file->move($docPath,$document);
        }
      $result = CarrierDocument::create([
        'carrier_id' => $request->carrier_id,
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
        $getFile = CarrierDocument::find($id);
        $file = public_path().'/carrier_documents/'.$getFile->document;

        if(File::exists($file)){
            return Response::download($file);
            session()->flash('Success');
        }
        else{
            return back()->with(['error' => 'No file is there!']);
        }

    }
    public function deleteDocument($id){
        $document = CarrierDocument::find($id);

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
        $links = CarrierDocumentsLink::where('carrier_id',$id)->get();
        return Datatables::of($links)
            ->addIndexColumn()
            ->editColumn('date',function($link){
               return date("m-d-Y H:i",strtotime($link->created_at));
           })
           ->editColumn('url',function($link){
               $url = $link->url;
            //    if(substr($link->url,0,3) != "http"){
            //        $url = $link->url;

            //    }
               return '<a href="'.$url.'">'.$url.'</a>';
           })
            ->addColumn('action', function($link){
                    $btn = '';
                    $url = route('carriers.editLink',$link->id);
                    $btn .= '<a href="javascript:void(0);" onclick="getModal(\''.$url.'\')"  class="edit btn btn-primary btn-sm mr-2">Edit</a>';
                    $btn .= '<a  href="javascript:void(0);" onclick="deleteLink(\''.route('carriers.deleteLink',$link->id).'\')"  class="delete btn btn-danger btn-sm">Delete</a>';
                    return $btn;
            })
            ->rawColumns(['action','url'])
            ->make(true);
    }

    public function createLink($id){
        return view('carriers.document_links.create',compact('id'));
    }
    public function storeLink(Request $request){
           $url = $request->url;

           if(substr($url,0,4) != "http" || substr($url,0,5) == 'https'){
                $url = str_replace('https','http',$url);
           }
           else{
               $url = 'http://'.$request->url;
           }

        $result = CarrierDocumentsLink::create([
            'carrier_id' => $request->carrier_id,
            'url' => $url,
            'name' => $request->name,
            'description' => $request->description,
          ]);
      if ($result) {
        return response()->json(['msg' => 'Success', 'error' => 0]);
      }
    }

    public function editLink($id){
        $row = CarrierDocumentsLink::find($id);
        return view('carriers.document_links.edit',compact('id','row'));
    }

    public function updateLink(Request $request,$id){

        $url = $request->url;

           if(substr($url,0,7) != 'http://' && substr($url,0,8) == 'https://'){
                str_replace('http://','https://',$url);
                $url = 'http://'.$url;
           }

        $link = CarrierDocumentsLink::find($id);
        $link->carrier_id = $request->carrier_id;
        $link->url = $url;
        $link->name = $request->name;
        $link->description = $request->description;
        $link->update();
        return response()->json(['msg' => 'Success', 'error' => 0]);
    }

    public function deleteLink($id){
       $link = CarrierDocumentsLink::find($id);
       if (!empty($link)) {
            $link->delete();
            return response()->json(['msg' => 'Success', 'error' => 0]);
       }
       else{
        return response()->json(['msg' => 'Something went wrong!', 'error' => 1]);
       }
    }

    public function carrierFeeList($id){
        $contacts = CarrierDynamicFee::where('carrier_id',$id)->orderBy('is_primary','desc')->get();
        return Datatables::of($contacts)
            ->addIndexColumn()
            ->addColumn('action', function($contact){
                    $btn = '';
                    $url = route('carriers.editFee',$contact->id);
                    $btn .= '<a href="javascript:void(0);" onclick="getModal(\''.$url.'\')"  class="edit btn btn-primary btn-sm mr-2">Edit</a>';
                    $btn .= '<a  href="javascript:void(0);" onclick="deleteFee(\''.route('carriers.destroyFee',$contact->id).'\')"  class="delete btn btn-danger btn-sm">Delete</a>';
                    return $btn;
            })
            ->editColumn('effective_date',function($contact){
                return date('Y-m-d',strtotime($contact->effective_date));
            })
            ->editColumn('is_primary',function($contact){
                if($contact->effective_date){
                    if($contact->effective_date > date('Y-m-d')){
                        return 'Scheduled';
                    }
                    if($contact->effective_date < date('Y-m-d')){
                        return 'Inactive';
                    }
                    if($contact->effective_date <= date('Y-m-d')){
                        return 'Active';
                    }
                }
                // $checked = '';
                // if ($contact->is_primary == 1) {
                //     $checked = "checked";
                // }
                // return '<input type="checkbox" onclick="setPrimaryFee(this,\''.$contact->id.'\')" name="is_primary" value="1" '.$checked.'>';
            })
            ->rawColumns(['effective_date','is_primary','action'])
            ->make(true);
    }

    public function createFee($carrier_id){
        return view('carriers.dynamic_fees.create',compact('carrier_id'));
    }

    public function storeFee(Request $request){
        $date = DateTime::createFromFormat('m/d/Y',$request->effective_date);
        $carrierDynamicFee = CarrierDynamicFee::create([
            'carrier_id' => $request->carrier_id,
            'effective_date' => $date->format('Y-m-d'),
            'ground' => $request->ground,
            'air' => $request->air,
            'is_primary' => 1,
            'international_air' => $request->international_air,
        ]);

        // CarrierDynamicFee::where('carrier_id',$request->carrier_id)->where('id','<>',$carrierDynamicFee->id)->update([
        //     'is_primary' => 0,
        // ]);
        return response()->json(['msg' => 'Success', 'error' => 0]);
    }

    public function editFee($id){
        $row = CarrierDynamicFee::find($id);
        return view('carriers.dynamic_fees.edit',compact('row'));
    }

    public function updateFee(Request $request, $id){
        $data = request()->all();
        $date = DateTime::createFromFormat('m/d/Y', $data['effective_date']);
        CarrierDynamicFee::where('id',$id)->update([
            'carrier_id' => $data['carrier_id'],
            'effective_date' => $date->format('Y-m-d'),
            'ground' => $data['ground'],
            'air' => $data['air'],
            'international_air' => $data['international_air'],
        ]);
        return response()->json(['msg' => 'Success', 'error' => 0]);
    }

    public function destroyFee($id){
        CarrierDynamicFee::find($id)->delete();
        return redirect()->back()->with('success','Success');
    }

    public function carrierSurchargeList($id){
        $contacts = CarrierPeakSurcharge::where('carrier_id',$id)
        ->where('end_date','>',date('Y-m-d'))
        // ->where('status','<>','Scheduled')
        ->get();
        return Datatables::of($contacts)
            ->addIndexColumn()
            ->addColumn('action', function($contact){
                    $btn = '';
                    $url = route('carriers.editSurcharge',$contact->id);
                    $btn .= '<a href="javascript:void(0);" onclick="getModal(\''.$url.'\')"  class="edit btn btn-primary btn-sm mr-2">Edit</a>';
                    $btn .= '<a  href="javascript:void(0);" onclick="deleteSurcharge(\''.route('carriers.destroySurcharge',$contact->id).'\')"  class="delete btn btn-danger btn-sm">Delete</a>';
                    return $btn;
            })
            ->editColumn('status',function($contact){
                if($contact->effective_date){
                    if($contact->effective_date > date('Y-m-d')){
                        return 'Scheduled';
                    }
                    if($contact->end_date < date('Y-m-d')){
                        return 'Inactive';
                    }
                    if($contact->effective_date <= date('Y-m-d') && $contact->end_date >= date('Y-m-d')){
                        return 'Active';
                    }
                }
            })
            ->editColumn('effective_date',function($contact){
                return date('Y-m-d',strtotime($contact->effective_date));
            })
            ->editColumn('end_date',function($contact){
                return date('Y-m-d',strtotime($contact->end_date));
            })
            ->rawColumns(['effective_date','end_date','action'])
            ->make(true);
    }

    public function createSurcharge($carrier_id){
        return view('carriers.peak_surcharge.create',compact('carrier_id'));
    }

    public function storeSurcharge(Request $request){

        $effectiveDate = DateTime::createFromFormat('d/m/y', $request->effective_date)->format('Y-m-d');
        $endDate = DateTime::createFromFormat('d/m/y', $request->end_date)->format('Y-m-d');


        $existingCarrierPeakSurcharge = CarrierPeakSurcharge::where('effective_date', '<', $effectiveDate)->where('end_date', '>', $effectiveDate)->first();

        if($existingCarrierPeakSurcharge){
            return response()->json(['msg' => 'We already have Active Peak Surcharge on this effective date', 'error' => 1]);
        }

        CarrierPeakSurcharge::create([
            'carrier_id' => $request->carrier_id,
            'status' => 'Scheduled',
            'effective_date' => DateTime::createFromFormat('d/m/y', $request->effective_date)->format('Y-m-d'),
            'end_date' => DateTime::createFromFormat('d/m/y', $request->end_date)->format('Y-m-d'),
            'sure_post' => $request->sure_post,
            'ground_residential' => $request->ground_residential,
            'air_residential' => $request->air_residential,
            'additional_handling' => $request->additional_handling,
            'large_package_gt_50_lbs' => $request->large_package_gt_50_lbs,
        ]);
        return response()->json(['msg' => 'Success', 'error' => 0]);
    }

    public function editSurcharge($id){
        $row = CarrierPeakSurcharge::find($id);
        return view('carriers.peak_surcharge.edit',compact('row'));
    }

    public function updateSurcharge(Request $request, $id){
        $data = request()->all();
        $date = DateTime::createFromFormat('d/m/y', $data['effective_date']);
        $date2 = DateTime::createFromFormat('d/m/y', $data['end_date']);
        CarrierPeakSurcharge::where('id',$id)->update([
            'carrier_id' => $data['carrier_id'],
            'status' => 'Scheduled',
            'effective_date' => $date->format('Y-m-d'),
            'end_date' => $date2->format('Y-m-d'),
            'sure_post' => $data['sure_post'],
            'ground_residential' => $data['ground_residential'],
            'air_residential' => $data['air_residential'],
            'additional_handling' => $data['additional_handling'],
            'large_package_gt_50_lbs' => $data['large_package_gt_50_lbs'],
        ]);
        return response()->json(['msg' => 'Success', 'error' => 0]);
    }

    public function destroySurcharge($id){
        CarrierPeakSurcharge::find($id)->delete();
        return redirect()->back()->with('success','Success');
    }

    public function CarrierAccounts(){
        $carrierAccounts = CarrierAccounts::select('carrier_accounts.*')->get();
        return Datatables::of($carrierAccounts)
            ->addIndexColumn()
            ->addColumn('carrier_name',function($carrierAccount){

                if(isset($carrierAccount->carrier->company_name)){
                    return $carrierAccount->carrier->company_name ;
                }else{
                    return '-';
                }
                
            })
            ->addColumn('action', function($carrierAccount){
                $btn = '';
                $url = route('carriers.createCarrierAccount',$carrierAccount->id);
                $btn .= '<a href="javascript:void(0);" onclick="GetCarrierModel(\''.$url.'\')" class="edit btn btn-primary btn-sm mr-2">Edit</a>';
                //$btn .= '<a onclick="deleteCarrierAccount(\''.$carrierAccount->id.'\')" class="delete btn btn-danger btn-sm text-white">Delete</a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    public function createCarrierAccount($id=null){
        $carriers = Carrier::all();
        if(intval($id)>0)
        {
            $carrierAccounts = CarrierAccounts::find($id);
            return view('carriers.carrier_accounts.create',compact('carriers','carrierAccounts','id'));
        }
        return view('carriers.carrier_accounts.create',compact('carriers','id'));
    }
    public function createCarrierAccountStore(Request $request){
        if($request->id == 0){
            CarrierAccounts::create([
                'description' => $request->description,
                'carrier_id' =>$request->carrier_id,
                'account_number' =>$request->account,
                'api_key' =>$request->apikey,
                'account_rules' => $request->account_rules,
            ]);
        }
        else{
            CarrierAccounts::find($request->id)
            ->update([
                'description' => $request->description,
                'carrier_id' =>$request->carrier_id,
                'account_number' =>$request->account,
                'api_key' =>$request->apikey,
                'account_rules' => $request->account_rules,
            ]);
        }
        $data_info = [
            'msg' => 'Success',
            'error' => 0
        ];
        return response()->json($data_info);
    }
    public function carrierAccountDestroy(Request $request){
        return CarrierAccounts::destroy($request->id);;   
    }

    public function carrierOrderAssignment($id){
        $carriers = Carrier::select('id','company_name')->get();
        $processing_groups = ProcessingGroups::select('id','group_name')->get();
        $warehouses = WareHouse::select('id','warehouses')->get();
        $carrier_accounts = CarrierAccounts::select('id','description','account_number','carrier_id')->get();
        $client = Client::get()->pluck('company_name','id')->toArray();
        if($id > 0){
            $row_orders = CarrierOrderAccountAssignments::find($id);
            $chanels = DB::table('client_channel_configurations')->where('client_id',$row_orders->client_id)->pluck('channel','id')->toArray();
            return view('carriers.carrier_order_assignment.create',compact('carriers','id','processing_groups','warehouses','carrier_accounts','row_orders','client','chanels'));
        }
        return view('carriers.carrier_order_assignment.create',compact('carriers','id','processing_groups','warehouses','carrier_accounts','client'));
    }

    public function GetDefaultOrderAccountAssignments(){
        $row = CarrierOrderAccountAssignments::find(1);
        $html='';
        $html.='<table class="table table-bordered">
                    <tr>
                        <th>Temperature/<br>Processing Group</th>
                        <th>WI</th>
                        <th>NV</th>
                        <th>OKC</th>
                        <th>PA</th>
                    </tr>
                    <tr>
                        <th>Dry</th>
                        <td>
                            '.(isset($row->dry_wi_carrier_name->company_name) ? $row->dry_wi_carrier_name->company_name : '-').' | 
                            '.(isset($row->dry_wi_account_name->account_number) ? $row->dry_wi_account_name->account_number : '-').'
                        </td>
                        <td>
                            '.(isset($row->dry_nv_carrier_name->company_name) ? $row->dry_nv_carrier_name->company_name : '-').' | 
                            '.(isset($row->dry_nv_account_name->account_number) ? $row->dry_nv_account_name->account_number : '-').'
                        </td>
                        <td>
                            '.(isset($row->dry_ok_carrier_name->company_name) ? $row->dry_ok_carrier_name->company_name : '-').' | 
                            '.(isset($row->dry_ok_account_name->account_number) ? $row->dry_ok_account_name->account_number : '-').'
                        </td>
                        <td>
                            '.(isset($row->dry_pa_carrier_name->company_name) ? $row->dry_pa_carrier_name->company_name : '-').' | 
                            '.(isset($row->dry_pa_account_name->account_number) ? $row->dry_pa_account_name->account_number : '-').'
                        </td>
                    </tr>
                    <tr>
                        <th>Frozen</th>
                        <td>
                            '.(isset($row->frozen_wi_carrier_name->company_name) ? $row->frozen_wi_carrier_name->company_name : '-').' | 
                            '.(isset($row->frozen_wi_account_name->account_number) ? $row->frozen_wi_account_name->account_number : '-').'
                        </td>
                        <td>
                            '.(isset($row->frozen_nv_carrier_name->company_name) ? $row->frozen_nv_carrier_name->company_name : '-').' | 
                            '.(isset($row->frozen_nv_account_name->account_number) ? $row->frozen_nv_account_name->account_number : '-').'
                        </td>
                        <td>
                            '.(isset($row->frozen_ok_carrier_name->company_name) ? $row->frozen_ok_carrier_name->company_name : '-').' | 
                            '.(isset($row->frozen_ok_account_name->account_number) ? $row->frozen_ok_account_name->account_number : '-').'
                        </td>
                        <td>
                            '.(isset($row->frozen_pa_carrier_name->company_name) ? $row->frozen_pa_carrier_name->company_name : '-').' | 
                            '.(isset($row->frozen_pa_account_name->account_number) ? $row->frozen_pa_account_name->account_number : '-').'
                        </td>
                    </tr>
                    <tr>
                        <th>Refrigerated</th>
                        <td>
                            '.(isset($row->refrigerated_wi_carrier_name->company_name) ? $row->refrigerated_wi_carrier_name->company_name : '-').' | 
                            '.(isset($row->refrigerated_wi_account_name->account_number) ? $row->refrigerated_wi_account_name->account_number : '-').'
                        </td>
                        <td>
                            '.(isset($row->refrigerated_nv_carrier_name->company_name) ? $row->refrigerated_nv_carrier_name->company_name : '-').' | 
                            '.(isset($row->refrigerated_nv_account_name->account_number) ? $row->refrigerated_nv_account_name->account_number : '-').'
                        </td>
                        <td>
                            '.(isset($row->refrigerated_ok_carrier_name->company_name) ? $row->refrigerated_ok_carrier_name->company_name : '-').' | 
                            '.(isset($row->refrigerated_ok_account_name->account_number) ? $row->refrigerated_ok_account_name->account_number : '-').'
                        </td>
                        <td>
                            '.(isset($row->refrigerated_pa_carrier_name->company_name) ? $row->refrigerated_pa_carrier_name->company_name : '-').' | 
                            '.(isset($row->refrigerated_pa_account_name->account_number) ? $row->refrigerated_pa_account_name->account_number : '-').'
                        </td>
                    </tr>
                </table>';

                return $html;

    }

    public function storeAssignedOrderAccounts(Request $request){
        if($request->id > 0){
            $row = CarrierOrderAccountAssignments::find($request->id);
        }else{
            $row = new CarrierOrderAccountAssignments;
        }
        //dd($request->all());
        $row->description = $request->description;
        $row->rules = $request->rules;
        if($request->rules == 'Client' || $request->rules == '3rd Party Billing'){
            $row->client_id = $request->client_id;
            $row->client_channel_configurations_ids = implode(',',$request->client_channel_configurations_ids);
        }
        else{
            $row->client_id = null;    
        }
        
        if($request->group_details){
            $row->group_details = implode(',',$request->group_details);    
        }
        
        $row->warehouse = $request->warehouses;
        $row->zipcode = $request->tags;
        
        $row->dry_wi_carrier_id = $request->dry_wi_carrier_id;
        $row->dry_wi_account_id = $request->dry_wi_account_id;
        $row->dry_nv_carrier_id = $request->dry_nv_carrier_id;
        $row->dry_nv_account_id = $request->dry_nv_account_id;
        $row->dry_ok_carrier_id = $request->dry_ok_carrier_id;
        $row->dry_ok_account_id = $request->dry_ok_account_id;
        $row->dry_pa_carrier_id = $request->dry_pa_carrier_id;
        $row->dry_pa_account_id = $request->dry_pa_account_id;

        $row->frozen_wi_carrier_id = $request->frozen_wi_carrier_id;
        $row->frozen_wi_account_id = $request->frozen_wi_account_id;
        $row->frozen_nv_carrier_id = $request->frozen_nv_carrier_id;
        $row->frozen_nv_account_id = $request->frozen_nv_account_id;
        $row->frozen_ok_carrier_id = $request->frozen_ok_carrier_id;
        $row->frozen_ok_account_id = $request->frozen_ok_account_id;
        $row->frozen_pa_carrier_id = $request->frozen_pa_carrier_id;
        $row->frozen_pa_account_id = $request->frozen_pa_account_id;

        $row->refrigerated_wi_carrier_id = $request->refrigerated_wi_carrier_id;
        $row->refrigerated_wi_account_id = $request->refrigerated_wi_account_id;
        $row->refrigerated_nv_carrier_id = $request->refrigerated_nv_carrier_id;
        $row->refrigerated_nv_account_id = $request->refrigerated_nv_account_id;
        $row->refrigerated_ok_carrier_id = $request->refrigerated_ok_carrier_id;
        $row->refrigerated_ok_account_id = $request->refrigerated_ok_account_id;
        $row->refrigerated_pa_carrier_id = $request->refrigerated_pa_carrier_id;
        $row->refrigerated_pa_account_id = $request->refrigerated_pa_account_id;
        
        $row->save();

        return response()->json([
            'error' => false,
            'msg' => 'Success'
        ]);
    }
    public function CarrierAccountsAssignments()
    {
        $CarrierOrderAccountAssignments = CarrierOrderAccountAssignments::where('id','!=',1)->get();
        return Datatables::of($CarrierOrderAccountAssignments)
            ->addIndexColumn()
            ->addColumn('client_name',function($CarrierOrderAccountAssignment){
                return $CarrierOrderAccountAssignment->Client ? $CarrierOrderAccountAssignment->Client->company_name : '';
            })
            ->addColumn('action', function($CarrierOrderAccountAssignment){
                $btn = '';
                $url = route('carriers.carrierOrderAssignment',$CarrierOrderAccountAssignment->id);
                $btn .= '<a href="javascript:void(0);" onclick="GetCarrierModel(\''.$url.'\')" class="edit btn btn-primary btn-sm mr-2">Edit</a>';
                $btn .= '<a onclick="deleteCarrierAccountAssigment(\''.$CarrierOrderAccountAssignment->id.'\')" class="delete btn btn-danger btn-sm text-white">Delete</a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
     public function deleteCarrierAccountAssigment(Request $request){
        return CarrierOrderAccountAssignments::destroy($request->id);;   
    }
    public function CarrierAllServiceConf()
    {
        $etailerServices = EtailerService::all();
        return Datatables::of($etailerServices)
            ->addIndexColumn()
            ->addColumn('ups_service_type_id',function($etailerService){
                return $etailerService->upsShippingServiceType ? $etailerService->upsShippingServiceType->service_name : '';
            })
             ->addColumn('fedex_service_type_id',function($etailerService){
                return $etailerService->fdxShippingServiceType ? $etailerService->fdxShippingServiceType->service_name : '';
            })
            ->addColumn('action', function($etailerService){
                $btn = '';
                $url = route('carriers.carrierServiceConf',$etailerService->id);
                $btn .= '<a href="javascript:void(0);" onclick="GetCarrierModel(\''.$url.'\')" class="edit btn btn-primary btn-sm mr-2">Edit</a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    public function carrierServiceConf($id){
        $etailerServices =  EtailerService::find($id);
        $shippingServiceTypes = ShippingServiceType::where('is_active','1')->get();
        return view('carriers.carrier_accounts.edit',compact('shippingServiceTypes','id','etailerServices'));
    }
    public function addcarrierServiceConf(){
        $shippingServiceTypes = ShippingServiceType::where('is_active','1')->get();
        return view('carriers.carrier_accounts.add_stc',compact('shippingServiceTypes'));
    }
    public function storeCarriershipping(Request $request){
            $etailer = $request->service_type_etailer;
            $ups = ShippingServiceType::find($request->service_type_ups);
            $fedex = ShippingServiceType::find($request->service_type_fedex);
            if (isset($request->id)) {
                EtailerService::find($request->id)
                    ->update([
                        'etailer_service_name' => $etailer,
                        'ups_service_type_id' =>$request->service_type_ups,
                        'fedex_service_type_id' =>$request->service_type_fedex,
                        'ups_service_code' =>$ups->api_code,
                        'fedex_service_code' => $fedex ? $fedex->fedex_service_code : null,
                    ]);
            } else {
                EtailerService::create([
                        'etailer_service_name' => $etailer,
                        'ups_service_type_id' =>$request->service_type_ups,
                        'fedex_service_type_id' =>$request->service_type_fedex,
                        'ups_service_code' =>$ups->api_code,
                        'fedex_service_code' => $fedex ? $fedex->fedex_service_code : null,
                    ]);
            }
        $data_info = [
            'msg' => 'Success',
            'error' => 0
        ];
        return response()->json($data_info);
    }
    public function deleteCarriershipping($id){
        $stc = EtailerService::find($id);
        if (!isset($stc)) {
            return response()->json([
                'msg' => 'Invalid Id',
                'error' => 1
            ]);
        }
        $stc->delete();
        $data_info = [
            'msg' => 'Deleted Successfully',
            'error' => 0
        ];
        return response()->json($data_info);
    }
    public function carrierOrderAutomaticUpgrades($id){
        $client = Client::get()->pluck('company_name','id')->toArray();
        $shippingServiceTypes = ShippingServiceType::where('is_active','1')->get();
        if($id > 0){
            $row_orders = OrderAutomaticUpgrades::find($id);
             $chanels = DB::table('client_channel_configurations')->where('client_id',$row_orders->client_id)->pluck('channel','id')->toArray();
            return view('carriers.carrier_accounts.carrierOrderAutomaticUpgrades',compact('shippingServiceTypes','id','row_orders','client','chanels'));
        }
        return view('carriers.carrier_accounts.carrierOrderAutomaticUpgrades',compact('client','id','shippingServiceTypes'));
    }
    public function GetAutomaticupgrades(){
        $OrderAutomaticUpgrades = OrderAutomaticUpgrades::all();
        return Datatables::of($OrderAutomaticUpgrades)
            ->addIndexColumn()
            ->addColumn('service_type_id',function($OrderAutomaticUpgrad){
                return $OrderAutomaticUpgrad->shippingServiceType ? $OrderAutomaticUpgrad->shippingServiceType->service_name : '';
            })
             ->addColumn('client_name',function($OrderAutomaticUpgrad){
                return $OrderAutomaticUpgrad->Client ? $OrderAutomaticUpgrad->Client->company_name : '';
            })
            ->addColumn('action', function($OrderAutomaticUpgrad){
                $btn = '';
                $url = route('carriers.carrierorderautomaticupgrades',$OrderAutomaticUpgrad->id);
                $btn .= '<a href="javascript:void(0);" onclick="GetCarrierModel(\''.$url.'\')" class="edit btn btn-primary btn-sm mr-2">Edit</a>';
                $btn .= '<a onclick="deleteOrderUpgrade(\''.$OrderAutomaticUpgrad->id.'\')" class="delete btn btn-danger btn-sm text-white">Delete</a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    public function storeOrderUpgrades(Request $request){
         if($request->id > 0){
            $row = OrderAutomaticUpgrades::find($request->id);
        }else{
            $row = new OrderAutomaticUpgrades;
        }
        $row->service_type_id = $request->service_type_ups;
        $row->group_detail = (isset($request->group_details) ? implode(',',$request->group_details) : NULL);
        $row->transit_day = (isset($request->days) ? implode(',',$request->days) : NULL);
        $row->client_id = $request->client_id;
        $row->client_channel_configurations_ids = (isset($request->client_channel_configurations_ids) ? implode(',',$request->client_channel_configurations_ids) : NULL);
         $row->save();
        $data_info = [
            'msg' => 'Success',
            'error' => 0
        ];
        return response()->json($data_info);
    }
    public function getDropdown(Request $request){
        $chanels = DB::table('client_channel_configurations')->where('client_id',$request->id)->pluck('channel','id')->toArray();
        $html = '';
        foreach($chanels as $key=>$val){
            $html.='<option value="'.$key.'">'.$val.'</option>';    
        }
        return $html;
    }
    public function deleteOrderUpgrade(Request $request){
        return OrderAutomaticUpgrades::destroy($request->id);
        $data_info = [
            'msg' => 'Success',
            'error' => 0
        ];
    }

    public function edit_zip_zone_wh($id) {
        $zip_zone_wh = UpsZipZoneByWH::where('id', $id)->first();
        return view('carriers.edit_zip_zone_wh', compact('zip_zone_wh')); 
    }

    public function update_transit_day(Request $request) {

        $id = $request->id;

        $td_zip = UpsZipZoneByWH::where('id', $id)->first();

        if (!isset($td_zip)) {
            return response()->json([
                'msg' => 'Invalid Id',
                'error' => 1
            ]);
        }

        $td_wi = isset($request->td_wi) ? $request->td_wi : $td_zip->transit_days_WI;
        $td_pa = isset($request->td_pa) ? $request->td_pa : $td_zip->transit_days_PA;
        $td_nv = isset($request->td_nv) ? $request->td_nv : $td_zip->transit_days_NV;
        $td_okc = isset($request->td_okc) ? $request->td_okc : $td_zip->transit_days_OKC;

        if (!(is_numeric($td_wi) && is_numeric($td_pa) && is_numeric($td_nv) && is_numeric($td_okc))) {
            return response()->json([
                'msg' => 'Transit days cannot be non-numeric',
                'error' => 1
            ]);
        }

        $td_zip = UpsZipZoneByWH::where('id', $id)->first();

        if (!isset($td_zip)) {
            return response()->json([
                'msg' => 'Invalid Id',
                'error' => 1
            ]);
        }
        
        UpsZipZoneByWH::where('id', $id)->update([
            'transit_days_PA' => $td_pa,
            'transit_days_WI' => $td_wi,
            'transit_days_NV' => $td_nv,
            'transit_days_OKC' => $td_okc
        ]);        

        return response()->json([
            'msg' => 'Transit days updated successfully',
            'error' => 0
        ]);
    }
}
