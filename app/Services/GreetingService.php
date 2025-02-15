<?php

namespace App\Services;

class GreetingService
{
    public function getGreeting($name)
    {
        return "Hello, {$name}! Welcome to my post.";
    }
}


