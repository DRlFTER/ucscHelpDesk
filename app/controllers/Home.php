<?php

class Home extends Controller {
    public function index($a = '', $b = '', $c = ''){
        echo "Welcome to the Home Page";
    }
}

$home = new Home();
call_user_func_array([$home, 'index'], ['arg1', 'arg2', 'arg3']);
