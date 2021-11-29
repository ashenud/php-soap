<?php 

require_once __DIR__ . '/Database.php';

class Authentication extends Database
{

    private const AUTHENTICATION_TABLE = 'sap_authentications';
	
    public function __construct()
    {
		parent::__construct();
    }

    public function user_authentication($username, $password) {
        $table = self::AUTHENTICATION_TABLE;
        $sql = "SELECT * FROM $table WHERE `username` = '$username' AND `password` = '$password' AND `deleted_at` IS NULL";
        $query = $this->connection->query($sql);
        if($query->num_rows > 0){
            $hash = preg_replace('/[^a-zA-Z0-9]/i',"",strtolower(base64_encode(microtime()."_".$username)));
            $user = $query->fetch_object();
            $generate_token = $this->generate_tocken($hash,$user->id);
            if($generate_token) {
                $data = new stdClass();
                $data->user = $user;
                $data->token = $hash;
                return $data;
            }
            else {
                return false;
            }
        }
        else{
            return false;
        } 
    }

    public function check_authentication($token) {
        $table = self::AUTHENTICATION_TABLE;
        $sql = "SELECT * FROM $table WHERE `token` = '$token' AND `deleted_at` IS NULL";
        $query = $this->connection->query($sql);
        if($query->num_rows > 0){
            return true;
        }
        else{
            return false;
        } 
    }

    public function generate_tocken($hash,$user_id) {
        $table = self::AUTHENTICATION_TABLE;
        $sql = "UPDATE $table SET `token` = '$hash' WHERE `id` = $user_id";
        $query = $this->connection->query($sql);
        if($query){
            return true;
        }
        else{
            return false;
        } 
    }
}