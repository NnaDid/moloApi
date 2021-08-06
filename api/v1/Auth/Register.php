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
    if($this->checkUserExist($email,"users","email")==true){	
        $this->data['status']= "false";
        $this->data['message'] = 'Account exists with this email';
    } else if($this->checkUserExist($phone,"users","phone")==true){ 
        $this->data['status']= "false";
        $this->data['message'] = "User with this phone number already exist";
    }else{
            $pass_hash = password_hash($pass,PASSWORD_DEFAULT); 
            $sql       = "INSERT INTO `users`(`name`,`email`,`phone`,`paswd`,`createdAt`, `updatedAt`) VALUES(?,?,?,?,NOW(),NOW())";
            $stmt      = $con->prepare($sql);
            $stmt->bind_param("ssss",$fname,$email,$phone,$pass_hash);
            if($stmt->execute()){
                $this->data['status']= "true";
                $this->data['message'] = 'success';
            }else{
                $this->data['status']= "false";
                    $this->data['message'] = "Error Registering ".$stmt->error;
                }
               $stmt->close(); 
            }
       
    
     $con->close();    
     echo json_encode($this->data); 
  }

   }

new Register();
?>