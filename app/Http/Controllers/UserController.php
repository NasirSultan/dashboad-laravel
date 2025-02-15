<?php



namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GreetingService;

class UserController extends Controller
{
       public function greetUser(){
    $greetingservice = new GreetingService();
    return $greetingservice ->getGreeting('follower');
    }
        // Public $service ;
        // public function __construct(GreetingService $greetingService)
        // {
        //     $this->service = $greetingService;
        // }
        // public function greetUser(){
        //     return $this->service ->getGreeting('follower');
        // }

};
