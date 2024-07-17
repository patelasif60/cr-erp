<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Feedback;
use Auth;
use DB;
use App\User;
use App\MasterProduct;
use App\Chat;
use App\Notifications\ProductApprovalNotification;

class NotificationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
		  return view('notification');
    }

    public function SaveChat(Request $request){
      $user_id = auth()->user()->id;
      $type_id = $request->type_id;
      $type = $request->type;
      $chat = $request->chat;
      $selected_users = $request->selected_users;

      $ch = new Chat();
      $ch->user_id = $user_id;
      $ch->chat = $chat;
      $ch->type = $type;
      $ch->type_id = $type_id;
      $ch->save();

      $all_admins = User::whereIn('name',explode(',',$selected_users))->get();
      $user = auth()->user();
      $url_id = '';
      $title = '';
      if($type == 'product'){
        $pro = MasterProduct::where('ETIN',$type_id)->first();
        if($pro){
          $url_id = $pro->id;
          $title = 'Product Notification: '. $pro->ETIN;
        }
        $url = '/editmasterproduct/'.$url_id.'/tab_comments';
      }else{
        $url = '';
      }
      
      
      if($all_admins){
          foreach($all_admins as $row_admin){
              $row_admin->notify(new ProductApprovalNotification($user, $chat, $title, $url));
          }
      }

      return response()->json(['error' => false, 'msg' => 'Success']);

    }

    public function GetChat(Request $request){
      $html = '';
      $type_id = $request->type_id;
      $type = $request->type;
      $user_id = auth()->user()->id;

      $Chat = Chat::leftJoin('users',function($q){
        $q->on('users.id','=','chat.user_id');
      })->select('chat.*','users.name')->where('type',$type)->where('type_id',$type_id)->orderBy('chat.id','DESC')->get();
      if($Chat){
        foreach($Chat as $row_chat){

          $html.='<div class="row mt-3">
              <div class="col-1">
                  <div class="chat_avtar">'.substr($row_chat->name,0,1).'</div>
              </div>
              <div class="col-10">
                  <p><b>'.$row_chat->name.'</b> <small class="float-right">'.$row_chat->created_at->diffForHumans().'</small><p>
                  <div>
                      '.htmlspecialchars_decode($row_chat->chat).'
                  </div>';
                  // if($row_chat->user_id == $user_id){
                  //   $html.='<div class="mt-3">
                  //     <a href="#" onClick="EditChat('.$row_chat->id.')" class="font-weight-bold">Edit</a>
                  //       <a href="#" onClick="DeleteChat('.$row_chat->id.')" class="font-weight-bold">Delete</a>
                  //   </div>';
                  // }
                  
                  $html.='</div>
          </div>';
          $html.='<hr>';
        }
      }
      

      return $html;
    }


}
