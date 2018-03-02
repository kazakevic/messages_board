<?php
require_once "vendor/autoload.php";
use src\Message;
session_start();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Can't access this page");
    }

    if(isset($_POST['msg'])){
        $data['name'] = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $data['b_date'] = $_POST['b_date'];
        $data['msg'] = filter_var($_POST['msg'], FILTER_SANITIZE_STRING);
        $data['email'] = $_POST['email'];
        //this is for JS respomse
        $data['age'] = Message::getAge($_POST['b_date']);
        $data['date_created'] = date('Y-m-d H:i:s');

        $validation_errors = Message::MessageValidate($data);
        
        if(empty($validation_errors)){
            $message = new Message($data);
            $message->saveMessage();
            $_SESSION['notice'] = "Message added!";
            header('Location: index.php'); 
        } else {
            $_SESSION['errors'] = $validation_errors;
            header('Location: index.php'); 
        }
          
    }

 
