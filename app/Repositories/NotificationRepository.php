<?php

namespace App\Repositories;
use App\Notifications\ProductApprovalNotification;
use App\User;
use App\Jobs\SendEmailJob;
use App\Mail\ProductEmail;
use DB;

class NotificationRepository extends BaseRepository
{
    public function SendProductNotification($input){
        $subject = (isset($input['subject']) ? $input['subject'] : NULL);
        $body = (isset($input['body']) ? $input['body'] : NULL);
        $user = (isset($input['user']) ? $input['user'] : NULL);
        $url = (isset($input['url']) ? $input['url'] : NULL);
        //SendEmailJob::dispatch(new ProductEmail('Test','Test Email'), 'soumabha.sunny1987@gmail.com');
        
        $get_users  = DB::table('user_notification_settings')->where('product_management',1)->get();
        if($get_users && $subject != '' && $body != ''){
            foreach($get_users as $row_users){
                $notification_type = explode(',',$row_users->notification_type);
                $all_users = User::find($row_users->user_id);
                if($all_users){
                    if(in_array('email',$notification_type) && $all_users->email != ''){
                        SendEmailJob::dispatch(new ProductEmail($subject,$body), $all_users->email);
                    }

                    if(in_array('in_app',$notification_type)){
                        $all_users->notify(new ProductApprovalNotification($user, $body, $subject, $url));
                    }
                }
            }
        }
    }

    public function SendInventoryNotification($input){
        $subject = (isset($input['subject']) ? $input['subject'] : NULL);
        $body = (isset($input['body']) ? $input['body'] : NULL);
        $user = (isset($input['user']) ? $input['user'] : NULL);
        $url = (isset($input['url']) ? $input['url'] : NULL);
        $notification_type = $input['notification_type'];
        $get_users  = DB::table('user_notification_settings')->where($notification_type,1)->get();
        if($get_users && $subject != '' && $body != ''){
            foreach($get_users as $row_users){
                $notification_type = explode(',',$row_users->notification_type);
                $all_users = User::find($row_users->user_id);
                if($all_users){
                    if(in_array('email',$notification_type) && $all_users->email != ''){
                        SendEmailJob::dispatch(new ProductEmail($subject,$body), $all_users->email);
                    }

                    if(in_array('in_app',$notification_type)){
                        $all_users->notify(new ProductApprovalNotification(($user == NULL ? $all_users: $user), $body, $subject, $url));
                    }
                }
            }
        }
    }


    public function SendOrderNotification($input){
        $subject = (isset($input['subject']) ? $input['subject'] : NULL);
        $body = (isset($input['body']) ? $input['body'] : NULL);
        $user_id = (isset($input['user_id']) ? $input['user_id'] : NULL);
        $url = (isset($input['url']) ? $input['url'] : NULL);
        $transit_days = (isset($input['transit_days']) ? $input['transit_days'] : NULL);
        $order_number = $input['order_number'];
        $user = NUll;
        if($user_id != ''){
            $user = User::find($user_id);
        }
        $order = DB::table('order_summary')->where('etailer_order_number',$order_number)->where('receive_notification',1)->first();
        
        if($order){
            $client_id = $order->client_id;
            $order_type_id = $order->order_type_id;
            $url = '/summery_orders/'.$order->id.'/view';
            $get_users  = DB::table('user_notification_settings')->where('order_by_client',$client_id)->whereRaw('FIND_IN_SET('.$order_type_id.',order_by_order_type)')->get();
            if($get_users && $subject != '' && $body != ''){
                foreach($get_users as $row_users){
                    if($transit_days != '' && !in_array($transit_days,explode(',',$row_users->order_by_shipping_speed))){
                        continue;
                    }
                    $notification_type = explode(',',$row_users->notification_type);
                    $all_users = User::find($row_users->user_id);

                    if($all_users){
                        if(in_array('email',$notification_type) && $all_users->email != ''){
                            SendEmailJob::dispatch(new ProductEmail($subject,$body), $all_users->email);
                        }
    
                        if(in_array('in_app',$notification_type)){
                            $all_users->notify(new ProductApprovalNotification(($user == NULL ? $all_users: $user), $body, $subject, $url));
                        }
                    }
                }
            }
        }
        
        
    }
   
}