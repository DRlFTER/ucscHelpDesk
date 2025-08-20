<?php

class Controller {
    public function view($view){
        $filename =  "../views/".$view.".view.php";
        if(file_exists($filename)){
            require $filename;
        }else{
            $filename = "../views/404.view.php";
            require $filename;
        }
    }
}