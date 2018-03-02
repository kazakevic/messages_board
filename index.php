<?php
require_once "vendor/autoload.php";
use src\Message;
?>
<!doctype html>
<html lang="en">
  <head>
    <title>Title</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  </head>
  <body>

    <div class="container mt-2">

    <div class="row">

    <div class="col-md-4">
           <!-- form-->
        <form method="POST" action="" id="msg_form">

        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" class="form-control" id="name" placeholder="Enter name and surname" value="John McDoh">
        </div>

        <div class="form-group">
            <label for="b_date">Birth date</label>
            <input type="text" name="b_date" class="form-control" id="b_date" placeholder="Enter birth date">
        </div>

        <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" name="email" class="form-control" id="email" placeholder="Enter email" value="test@test.lt">
        </div>
    
         <div class="form-group">
            <label for="msg">Your message:</label>
            <textarea class="form-control" id="msg" name="msg" rows="3">Test message <?php echo uniqid(); ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary" name="send" id="send">Send message</button>
        </form><br><br>
        <!-- form-->
    </div>

    <div class="col-md-4">
    <br><br>
    <div id="notice"></div>

        <div id="messages"></div>
        <?php
            $msg = new Message();
            $messages = $msg->getAllMessages();

            if(!empty($messages)):
                foreach($messages as $message):?>
                <div class="card mb-2">
                <div class="card-header">
                <?php
                $head = ", ".Message::getAge($message->b_date)." <small>[".$message->date_created."]</small>";

                if(!empty($message->email)): ?>
                    <a href="mailto:<?php echo $message->email ?>"><?php echo $message->name ?></a><?php echo $head ?>
                <?php else:
                echo $message->name.$head;
                endif;
                ?>
                </div>
                <div class="card-body">
                    <blockquote class="blockquote mb-0">
                    <p><?php echo $message->msg ?></p>
                    </blockquote>
                </div>
                </div>
                <?php 
                endforeach;
            endif;
            ?>
    </div>

    <div class="col-md-4"></div>
    </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>

    $( function() {
        $( "#b_date" ).datepicker({dateFormat: 'yy-mm-dd'});
    });

    $("#msg_form").submit(function(e){
        e.preventDefault();

        var message = {};
        //put all data from form to message object
        $.each($( this ).serializeArray(), function(key, val){
            var prop = val.name; 
            message[prop] = val.value;
        })
        //send data to PHP
        var req = $.ajax({
                url: 'controller.php',
                method: 'POST',
                data: message
            })
            .done(function(data) {

                var notice = `<div class="alert alert-success">Message saved</div>`;
                $("#notice").html("");
                $("#notice").append(notice);

                var msg_block = `<div class="card">
                                    <div class="card-header">
                                        ${data.name}, ${data.age}  <small>[${data.date_created}]</small>
                                    </div>
                                    <div class="card-body">
                                        <blockquote class="blockquote mb-0">
                                        <p>${data.msg}</p>
                                        </blockquote>
                                    </div>
                                    </div><br>`;

            $("#messages").append(msg_block);    

            })
            .fail(function(data) {

               $("#notice").html("");
               $.each(data.responseJSON, function(key, val){
                var notice = `<div class="alert alert-danger">${val}</div>`;
                $("#notice").prepend(notice);
               })
              
            })
            .always(function() {
            ;
            }); 
    })
    </script>
  </body>
</html>
