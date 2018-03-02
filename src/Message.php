<?php
namespace src;
use PDO;

class Message extends Database
{
    private $message_data = [];

    public function __construct($data){
        $this->message_data['name'] = $data['name'];
        $this->message_data['b_date'] = $data['b_date'];
        $this->message_data['msg'] = $data['msg'];
        $this->message_data['email'] = $data['email'];
    }

    public function saveMessage(){

        $sql = "INSERT INTO messages(name, b_date, msg) VALUES (:name, :b_date, :msg, :email)";
        $sth = $this->getConnection()->prepare($sql);
        $sth->execute(
            [
                'name' => $this->message_data['name'],
                'b_date' => $this->message_data['b_date'],
                'msg' => $this->message_data['msg'],
                'email' => $this->message_data['email']
            ]
        );
    }

    public function getAllMessages($order = 'newest'){
        $res = false;

        $sql = "SELECT * FROM messages order by $order DESC";
        $sth = $this->getConnection()->prepare($sql);
        $sth->execute();

        if($sth->rowCount() > 0){
            $res = $sth->fetchAll(PDO::FETCH_OBJ);
        }
        return $res;
    }

}
