<?php



namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GreetingService;

class UserController extends Controller
{
  
      Public $service ;
        public function __construct(GreetingService $greetingService)
        {
            $this->service = $greetingService;
        }
        // public function greetUser(){
        //     return $this->service ->getGreeting('follower');
        // }

        public function Container(){
                  $container = app('container');
                 return $container->getGreeting('follower');
        }

};
