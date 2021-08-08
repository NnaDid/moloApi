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

    public function getUserByPhoneNumber($phone){
        $con  = $this->con();
        $sql  = "SELECT * FROM `users` WHERE `phone`=?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s",$phone);
        if($stmt->execute()){
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();
            return $row;
        }
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


	 // This function creates Transaction Record and returns true on success and false on failure
     public function performTxn($uid,$txType,$amount,$txRef){
        $con     = $this->con();
        $sql    = "INSERT INTO `m_transactions`(`userId`,`amount`,`txtTpe`,`txRef`,`createdAt`) VALUES(?,?,?,?,NOW())";
        $stmt     = $con->prepare($sql);
        $stmt->bind_param("isss",$uid,$amount,$txType,$txRef);
        if($stmt->execute()){
           return true;
        }else{
           return false;
        }
        $stmt->close();
        $con->close();
    }


}


?>