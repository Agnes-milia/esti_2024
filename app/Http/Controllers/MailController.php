<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\DemoMail;


class MailController extends Controller
{
    public function index()
   {
       $mailData = [
           'title' => 'Levél címe',
           'body' => 'Levél törzse'
       ];   

       foreach(['laralaravelvel@gmail.com', '4brainnotfound04@gmail.com'] as $cim) {
        Mail::to($cim)
        /* ->cc($moreUsers)
        ->bcc($evenMoreUsers) */
        ->send(new DemoMail($mailData));
       }
       
       dd("Email küldése sikeres.");
   }

}
