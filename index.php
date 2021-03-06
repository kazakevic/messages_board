<?php
require_once "vendor/autoload.php";
use src\Message;
session_start();
?>
<!doctype html>
<html lang="en">
  <head>
    <title>Message Board</title>
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
        <form method="POST" action="controller_nojs.php" id="msg_form">

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
        <img src="assets/img/ajax-loader.gif" style="display: none" id="loader" />
        </form><br><br>
        <!-- form-->
    </div>

    <div class="col-md-4">
    <br><br>
    <div id="notice">
    <?php if(isset($_SESSION['errors'])): ?>
        <?php foreach($_SESSION['errors'] as $error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endforeach; ?>
        <!-- Clear messages after first show-->
        <?php unset($_SESSION['errors']) ?>
    <?php endif; ?>

    <?php if(isset($_SESSION['notice'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['notice']; ?></div>
    <?php unset($_SESSION['notice']) ?>
    <?php endif; ?>
    </div>

    <div id="new_message"></div>
    <div id="messages"></div>
        <?php
            $msg = new Message();
            if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1){
                Message::$page = $_GET['page'];
            }
  
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


    <nav aria-label="Page navigation example">
    <ul class="pagination">
        <?php $current_page = Message::$page; ?>
        <li class="page-item"><a class="page-link" href="index.php?page=<?php echo $current_page - 1 ?>">Previous</a></li>

        <?php 
        $page = 0;
            for($i = 1; $i < $msg->getMessagesCount(); $i+=Message::MESSAGES_PER_PAGE): 
            $page++;
        ?>

        <li class="page-item"><a class="page-link" href="index.php?page=<?php echo $page; ?>"><?php echo $page ?></a></li>
        <?php endfor; ?>
        <li class="page-item"><a class="page-link" href="index.php?page=<?php echo $current_page+ 1 ?>">Next</a></li>
    </ul>
    </nav>

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
    <script src="maplabel-compiled.js"></script>
    <script>

    $( function() {
        $( "#b_date" ).datepicker({dateFormat: 'yy-mm-dd'});
    });

    function disableElements(){
        $("#name").prop('disabled', true);
        $("#email").prop('disabled', true);
        $("#msg").prop('disabled', true);
        $("#b_date").prop('disabled', true);
        $("#send").hide();
        $("#loader").show();
    }
    function enableElements(){
        $("#name").prop('disabled', false);
        $("#email").prop('disabled', false);
        $("#msg").prop('disabled', false);
        $("#b_date").prop('disabled', false);
        $("#send").show();
        $("#loader").hide();
    }

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
                data: message,
                beforeSend: function(){
                    disableElements();
                }
            })
            .done(function(data) {

                var notice = `<div class="alert alert-success">Message saved</div>`;
                $("#notice").html("");
                $("#notice").append(notice);

                var msg_block = `<div class="card">
                                    <div class="card-header">
                                    <h6><span class="badge badge-secondary">New</span>
                                        ${data.name}, ${data.age}  <small>[${data.date_created}]</small>
                                    </div>
                                    </h6>
                                    <div class="card-body">
                                        <blockquote class="blockquote mb-0">
                                        <p>${data.msg}</p>
                                        </blockquote>
                                    </div>
                                    </div><br>`;

            $("#new_message").html(msg_block);    

            })
            .fail(function(data) {

               $("#notice").html("");
               $.each(data.responseJSON, function(key, val){
                var notice = `<div class="alert alert-danger">${val}</div>`;
                $("#notice").prepend(notice);
               })
            })
            .always(function() {
                enableElements();
            }); 

    })
    </script>
  </body>
</html>
