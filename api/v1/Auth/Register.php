<?php
   header('Content-type: application/json');
   require_once('../Common.php');

   class Register{

    use Common;
    private $data = [];
    public function __construct(){
        // Getting the received JSON into $json variable.
        $jsonInput = file_get_contents('php://input');
        $this->register($jsonInput);
    }
       
    private function register($input){
        $con    = $this->con();
        // decoding the received JSON and store into $obj variable.
        $obj    = json_decode($input,true); 
        $fname  = $obj['name']; 
        $email  = $obj['email'];
        $phone  = $obj['phone'];
        $pass   = $obj['paswd'];   
    if($this->checkUserExist($email,"users","email")==false){	 
        if($this->checkUserExist($phone,"users","phone")==false){ 
                $pass_hash = password_hash($pass,PASSWORD_DEFAULT); 
                $sql       = "INSERT INTO `users`(`name`,`email`,`phone`,`paswd`,`createdAt`) VALUES(?,?,?,?,NOW())";
                $stmt      = $con->prepare($sql);
                $stmt->bind_param("sssis",$fname,$email,$phone,$bvn,$pass_hash);
                if($stmt->execute()){
                    $this->data['message'] = 'success';
                }else{
                    $this->data['message'] = "Error Registering ".$stmt->error;
                }
               $stmt->close(); 
        }else{
            $this->data['message'] = "User with this phone number already exist";
        }
    }else{
        $this->data['message'] = 'Account exists with this email';
    }
     $con->close();    
     echo json_encode($this->data); 
  }

   }

new Register();
?>