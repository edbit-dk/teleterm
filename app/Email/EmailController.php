<?php

namespace App\Email;

use Lib\Controller;

use App\Email\EmailService as Mail;

class EmailController extends Controller
{
    public function mail()
    {
        return Mail::handle(parse_request('data'));
    }

}