<?php

trait Common{
     
    public $server = "localhost";
    public $user   = "root";
    public $pass   = "";
    public $db     = "molo";

    public function con(){
        $con = new Mysqli($this->server,$this->user,$this->pass,$this->db);
        return $con;
    }
    
   // Return User Details as Associative array
   public function getMemberDetails($email){
        $con        = $this->con();
        $queryRow   = $con->query("SELECT * FROM `users` WHERE `email` ='$email'")->fetch_assoc();
        return  $queryRow;
    }

    // Get User/member details by ID
    public function getUserById($uid){
        $con        = $this->con();
        $queryRow   = $con->query("SELECT * FROM `users` WHERE `userId` ='$uid'")->fetch_assoc();
        return  $queryRow;
    }

        // Get User/member details by UserName
    public function getMemberByUserName($uName){
        $con        = $this->con();
        $queryRow   = $con->query("SELECT * FROM `users` WHERE `userName` ='$uName'")->fetch_assoc();
        return  $queryRow;
     }
        

    public function checkUserExist($val,$table,$col){
        $con     = $this->con();
       if(!empty($val)){
       $sql       = "SELECT * FROM `".$table."` WHERE `".$col."`=?";
       $stmt      = $con->prepare($sql);
       $stmt->bind_param("s",$val);
       $exec      = $stmt->execute();
       if($exec){
       $result   = $stmt->get_result();
       $num_rows = $result->num_rows;
       if($num_rows>0){
           return true;
       }else{
           return false;
       }
       $stmt->close();
       }
       }
   }




}


?>