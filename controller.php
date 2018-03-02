<?php
require_once "vendor/autoload.php";
use src\Message;


    if(isset($_POST['msg'])){
        $data['name'] = $_POST['name'];
        $data['b_date'] = $_POST['b_date'];
        $data['msg'] = $_POST['msg'];
        $data['email'] = $_POST['email'];
        $data['age'] = Message::getAge($_POST['b_date']);
        $data['date_created'] = date('Y-m-d H:i:s');

        $message = new Message($data);

        $message->saveMessage();
            //retunr json message
            header("Access-Control-Allow-Origin: *");
            header("Content-Type: application/json; charset=UTF-8");
            echo json_encode($data);
    }

 
