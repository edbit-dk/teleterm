<?php

namespace App\User;

use Lib\Session;
use Lib\Input;

use App\User\UserModel as User;

use App\User\UserService as Auth;
use App\Host\HostService as Host;
use App\AppService as App;

use App\AppController;

class UserController extends AppController
{
    public function login() 
    {
        // Check if the user is already blocked
        Auth::blocked();

        if(!Auth::check() && $this->data) {

            $input = App::auth($this->data);

            if(Auth::login($input['username'], $input['password'])) {
                Host::attempt(1, Auth::id());
                sleep(1);
                echo 'IDENTIFICATION VERIFIED';
                exit;  

            } else {
                echo 'IDENTIFICATION NOT RECOGNIZED BY SYSTEM';
                exit;
            }    
        }
    }

    public function user() 
    {
        $user = auth();
        $password = base64_encode($user->password);

        echo "ACCESS CODE: {$user->code} \n";
        echo "SIGNUP: {$user->created_at} \n";
        echo "USERNAME: {$user->username} \n";
        echo "PASSWORD: {$password} \n";
        echo "LEVEL: {$user->level_id} \n";
        echo "XP: {$user->xp} \n";
        echo "REP: {$user->rep} \n";
    }

    public function password()
    {
        if(!Auth::check() && $this->data) {
            Auth::data()->update([
                'password' => $this->data
            ]);
    
            echo 'PASSWORD UPDATED';
        }
    }

    public function newuser() 
    {
        // Check if the user is already blocked
        Auth::blocked();

        $input = App::auth($this->data);

        if(!Auth::check() && $this->data) {

            $code = Session::get($this->user['code']);
            $username = $input['username'];
            $password = $input['password'];

            if (User::where($this->user['username'], '=', $username)->exists()) {
                echo 'USERNAME TAKEN';
                exit;
             }

             User::create([
                $this->user['username'] => $username,
                $this->user['email'] => "$username@teleterm.net",
                $this->user['fullname'] => ucfirst($username),
                $this->user['password'] => $password,
                $this->user['code'] => $code,
                $this->user['created'] => now()
            ]);
        }
        
        if(Auth::login($input['username'], $input['password'])) {
            Host::attempt(1, Auth::id());
            sleep(1);
            echo 'IDENTIFICATION VERIFIED';
            exit;  

        } else {
            echo 'IDENTIFICATION NOT RECOGNIZED BY SYSTEM';
            exit;
        }   
    }

    public function logout() 
    {
        Auth::logout();
    }

    public function unlink()
    {
        Auth::uplink(false);
        echo '--DISCONNECTING--';
    }
}