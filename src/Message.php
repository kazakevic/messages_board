<?php
namespace src;
use PDO;
use DateTime;
use DateTimeZone;

class Message extends Database
{
    private $message_data = [];
    const MESSAGES_PER_PAGE = 5;

    public static $page = 1;

    /**
     * Set propertis when message object is create if data is passsed
     *
     * @param boolean|array $data
     */
    public function __construct($data = false){
        if($data) {
            $this->message_data['name'] = $data['name'];
            $this->message_data['b_date'] = $data['b_date'];
            $this->message_data['msg'] = $data['msg'];
            $this->message_data['email'] = $data['email'];
        }
    }

    /**
     * Stores messages in DB
     *
     * @return void
     */
    public function saveMessage(){
        $sql = "INSERT INTO messages(name, b_date, msg, email) VALUES (:name, :b_date, :msg, :email)";
            $this->query($sql,[
                'name' => $this->message_data['name'],
                'b_date' => $this->message_data['b_date'],
                'msg' => $this->message_data['msg'],
                'email' => $this->message_data['email']
            ] );
        return true;
    }
    /**
     * Read all messages from DB
     * Order by date_create DESC
     * Paginates messages by static properties and const - MESSAGES_PER_PAGE
     *
     * @return array
     */
    public function getAllMessages(){

        $page = self::$page;
        if($page > Message::getMessagesCount() % Message::MESSAGES_PER_PAGE ) 
        {
            self::$page = Message::getMessagesCount() % Message::MESSAGES_PER_PAGE;
        }
        else if ($page < 1){
            self::$page = 1;
        }
        $offset = ($page * Message::MESSAGES_PER_PAGE) - Message::MESSAGES_PER_PAGE;

        $limit = self::MESSAGES_PER_PAGE;
        $res = false;
        $sql = "SELECT * FROM messages ORDER BY date_created DESC LIMIT :offset, :limit";
        $sth = $this->getConnection()->prepare($sql);
        //$sth->debugDumpParams();

        $sth->bindParam(':offset', $offset, PDO::PARAM_INT); 
        $sth->bindParam(':limit', $limit, PDO::PARAM_INT); 
        $sth->execute();

        if($sth->rowCount() > 0){
            $res = $sth->fetchAll(PDO::FETCH_OBJ);
        }
        return $res;
    }

    /**
     * Count age by provided date
     *
     * @param [string] $date
     * @return string
     */
    public static function getAge($date){
       
        $current_date = new DateTime("now", new DateTimeZone('UTC'));
        $another_date = new DateTime($date." 00:00", new DateTimeZone('UTC'));

        $difference = $another_date->diff($current_date);

        $age = $difference->format('%y');
        if($age < 0){
            $age = 0;
        }
        return $age;
    }
    /**
     * Full message validation
     *
     * @param [array] $message_data - message data array
     * @return array with errors or empty array
     */
    public static function MessageValidate($message_data){

        $errors = [];

        //this is need for checkdate function
        $date_info = explode("-", $message_data['b_date']);

        if(empty($message_data['name'])){
            $errors[] = "Name is empty!";
        }

        if(empty($message_data['b_date'])){
            $errors[] = "Please enter your birth date!";
        }
        //checking Year, month,day
        if(!empty($message_data['b_date']) && !checkdate($date_info[1], $date_info[2], $date_info[0])){
            $errors[] = "Wrong date!";
        }
        if(self::isFuture($message_data['b_date'])){
            $errors[] = "Birth date cannot be future!";
        }
        
        if(empty($message_data['msg'])){
            $errors[] = "Please enter message!";
        }
       
        if (!empty($message_data['email']) && !filter_var($message_data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email is not valid!";
        }
       
        return $errors;
    }

    /**
     * Just gets messages count for pagination
     *
     * @return int - messages count
     */
    public function getMessagesCount(){
        return $this->query("SELECT count(id) as cnt FROM messages", [])
        ->fetchColumn();
        
    }

    public function isFuture($date){
        $current_date = new DateTime("now", new DateTimeZone('UTC'));
        $another_date = new DateTime($date." 00:00", new DateTimeZone('UTC'));

        if($another_date > $current_date){
            return true;
        }
        else {
            return false;
        }
    }

}

