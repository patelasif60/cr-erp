<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\TicketStoreRequest;
use App\ProductTicket;
use App\TicketMessage;
use DataTables;
use Auth;

class ProductTicketController extends Controller
{
    public function ProductTicketsList($master_product_id,$product_type){
        $data = ProductTicket::select('product_tickets.*','users.name')->leftjoin('users','users.id','product_tickets.created_by')->where('master_product_id',$master_product_id)->where('product_type',$product_type)->get();
        
        return Datatables::of($data)
            ->addColumn('ticket_status', function($data){
                $status = '';
                if($data->status == 1){
                    $status = 'Open';
                }
                if($data->status == 0){
                    $status = 'Close';
                }
                return $status;
            })
            ->addColumn('action', function($data){
                $btn = '<button onClick="GetChat(\''.route('ticket.get_chat',$data->id).'\')" type="button" class="btn btn-sm btn-primary btn-flat">Chat</button>';
                if($data->status == 1){
                    $btn .= '<button onClick="CloseTicket(\''.route('ticket.close_ticket',$data->id).'\')" type="button" class="btn btn-sm btn-danger btn-flat ml-2">Close</button>';
                }
                if($data->status == 0){
                    $btn .= '<button onClick="ReopenTicket(\''.route('ticket.reopen_ticket',$data->id).'\')" type="button" class="btn btn-sm btn-success btn-flat ml-2">Reopen</button>';
                }
                
                return $btn;
            })
            ->rawColumns(['action','ticket_status'])
            ->make(true);
    }

    public function store(TicketStoreRequest $request){
        $store = ProductTicket::create([
            'subject' => $request->subject,
            'description' => $request->description,
            'created_by' => Auth::user()->id,
            'master_product_id' => $request->master_product_id,
            'product_type' => $request->product_type,
            'status' => 1
        ]);

        if($store){
            $data = [
                'error'=>false,
                'msg'=>"Ticket Added"
            ];
        }else{
            $data = [
                'error'=>true,
                'msg'=>"Something Went Wrong"
            ];
        }

        return response()->json($data);
    }

    public function get_chat($id){
        $ticket_details = ProductTicket::find($id);
        $messages = TicketMessage::select('ticket_messages.*','users.name')->leftjoin('users','users.id','ticket_messages.send_by')->where('ticket_id',$id)->get();
        return view('cranium.product_tickets.chatbox',compact('ticket_details','messages'));
    }

    public function save_message(Request $request){
        $save = TicketMessage::create([
            'ticket_id' => $request->ticket_id,
            'message' => $request->msg,
            'send_by' => Auth::user()->id,
        ]);

        if($save){
            $data = [
                'error'=>false,
                'msg'=>"Msg Added",
                'time'=>gmdate("Y-m-d H:i:s")
            ];
        }else{
            $data = [
                'error'=>true,
                'msg'=>"Something Went Wrong"
            ];
        }
        return response()->json($data);
    }

    public function close_ticket($id){
        $close = ProductTicket::where('id',$id)->update([
            'status' => 0
        ]);

        if($close){
            $data = [
                'error'=>false,
                'msg'=>"Ticket Closed"
            ];
        }else{
            $data = [
                'error'=>true,
                'msg'=>"Something Went Wrong"
            ];
        }
        return response()->json($data);
    }

    public function reopen_ticket($id){
        $open = ProductTicket::where('id',$id)->update([
            'status' => 1
        ]);

        if($open){
            $data = [
                'error'=>false,
                'msg'=>"Ticket Re-Opened"
            ];
        }else{
            $data = [
                'error'=>true,
                'msg'=>"Something Went Wrong"
            ];
        }
        return response()->json($data);
    }
    
    public function show(){
        // return response()->json(['time'=>gmdate("Y-m-d H:i:s")]);
    }

    public function get_message_time(){
        return response()->json(['time'=>gmdate("Y-m-d H:i:s")]);
    }
}
