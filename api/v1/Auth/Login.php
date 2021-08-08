<?php
   header('Content-type: application/json');
   require_once('../Common.php');

   class Login{

    use Common;
    private $data = [];

    public function __construct(){
        // Getting the received JSON into $json variable.
        $jsonInput = file_get_contents('php://input');
        $this->login($jsonInput);
    }
    
           
    private function login($input){ 
        $con    = $this->con();
        $obj    = json_decode($input,true);  
        $email  = $obj['email'];   // User can login with either Phone|Email
        $pass   = $obj['paswd'];  
        if($this->checkUserExist($email,"users","email") || $this->checkUserExist($email,"users","phone")){	
            $sql  		= "SELECT * FROM `users` WHERE `email`= ? OR `phone`=?";
            $stmt 		= $con->prepare($sql);
            $stmt->bind_param("ss",$email,$email);  
           if($stmt->execute()){
                $result	   = $stmt->get_result();
                $row       = $result->fetch_assoc();
                if(password_verify($pass,$row['paswd'])){
                    $this->data['message'] = 'success';
					$this->data['data']    = [
                                              "name"=>$row['name'],
                                              "email"=>$row['email'],
                                              "phone"=>$row['phone'],   
                                              "user_id"=>$row['id'], 
                                            ];
                }else{
                    $this->data['message'] ="Incorrect Password";
               }
                
           }else{
              $this->data['message'] = "LOGIN FAILED  ".$stmt->error;
           }
           
        }else{
            $this->data['message'] = "User does not exist";
        }
        $con->close();  
        echo json_encode($this->data); 
     }
   


   }

new Login();
?>