<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\SlackMessage;

class ApproveRejectProductNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($url,$for,$ETIN,$product_listing_name,$data = [])
    {
        $this->url = $url;
        $this->data = $data;
        $this->for = $for;
        $this->ETIN = $ETIN;
        $this->product_listing_name = $product_listing_name;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */

    public function toSlack($notifiable)
    {
        $url = $this->url;
        $data = $this->data;
        $for = $this->for;
        $ETIN = $this->ETIN;
        $product_listing_name = $this->product_listing_name;
        
       
        // $message = "Click to following link to Approve Or Reject the Master Product";

        if($for == 'add'){
            // $message = "Click to following link to Approve Or Reject the Master Product";
            return (new SlackMessage)
                ->from('Cranium', '')
                // ->content($message)
                ->attachment(function ($attachment) use ($ETIN,$product_listing_name) {
                    $attachment->fields([
                                    'New Product Added' => '',
                                    '' => '',
                                    'ETIN' => $ETIN
                                ]);
                })

                ->attachment(function ($attachment) use ($ETIN,$product_listing_name) {
                    $attachment->fields([
                                    'Product Listing Name' => $product_listing_name,
                                    '' => '',
                                ]);
                })
                
                ->attachment(function ($attachment) use ($url) {
                    $attachment->title('Open Product', $url);
                }); 
        }
        if($for == 'edit'){
            $message = "Click to following link to Approve Or Reject the Master Product";
            return (new SlackMessage)
                ->from('Cranium', '')
                // ->content($message)
                ->attachment(function ($attachment) use ($ETIN,$product_listing_name) {
                    $attachment->fields([
                                    'Product Edited, ready for approve/reject' => '',
                                    
                                    '' => '',
                                    'ETIN' => $ETIN
                                ]);
                })
                ->attachment(function ($attachment) use ($ETIN,$product_listing_name) {
                    $attachment->fields([
                                   
                                    'Product Listing Name' => $product_listing_name,
                                    '' => '',
                                    
                                ]);
                })
                ->attachment(function ($attachment) use ($url,$data) {
                    $attachment->title('Open Product Request', $url['product_request_url']);
                })
                ->attachment(function ($attachment) use ($url,$data) {
                    $attachment->fields($data);
                })
                ->attachment(function ($attachment)  use ($url,$data){
                    $attachment->title('Click Here to Approve', $url['approve_url']);
                })
                ->attachment(function ($attachment)  use ($url,$data){
                    $attachment->title('Click Here to Reject', $url['reject_url']);
                });
        }   
        
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
