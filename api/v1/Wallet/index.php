<?php
   header('Content-type: application/json');
   require_once('../Common.php');

class Wallet{

    use Common;
    private $data    = [];
    private $Balance = 0;

    public function __construct(){
        $jsonInput  = file_get_contents('php://input');

        $action_obj = json_decode($input,true);  

        switch($action_obj['action']){
            case 'fund'     :   $this->fundWallet($jsonInput);           break;
            case 'bal'      :   $this->getUserWalletBalance($jsonInput); break;
            case 'transfer' :   $this->transferFund($jsonInput);         break;
        }
       
    }




    public function fundWallet($input){
        $con    = $this->con();
        $obj    = json_decode($input,true);  

        $email    = $obj['email'];  
        $amount   = $obj['amount'];
        $ref      = $obj['ref'];
        $tx_type  = "funding";

        $receiver      = $this->getUserDetails($uid)['phone'];  //get the phone number of the current user since he is the one funding account
        $receiverEmail = $this->getUserDetails($uid)['email'];  // Get The receiver's email

        $uid            = $this->getMemberDetails($email)['id'];
        // Check if transaction is recorded first before updateting wallet
        if($this->performTxn($uid,$tx_type,$amount,$ref)){
             $checkUSer  =  $con->query("SELECT * FROM `wallet` WHERE `wal_id`='$uid'");
             $sql2 		 =  "";
                if($checkUSer->num_rows>0){
                        $sql2.="UPDATE `wallet` SET `wal_balance`=`wal_balance`+'$amount' WHERE `wal_id`='$uid'";
                }else{        
                    $sql2.="INSERT INTO `wallet`(`wal_id`,`wal_balance`,`updatedAt`) VALUES('$uid','$amount',NOW())";
                }
                $fundingQuery      = $con->query($sql2);
                if($fundingQuery){
                        $currentWalletBalance = $this->Balance + $amount;
                        $this->data['message'] = [
                                                    "status"=>"Wallet funding successful",
                                                    "amount_funded"=>$amount,
                                                    "wallet_balance"=>$currentWalletBalance
                                                ];
                    }else{
                        $this->data['message'] = "Error Funding Account";
                }

        }else{
            $this->data['message'] = "Error Making Payment -:) ". $stmt->error();
        }
        $stmt->close();
        $con->close();
        echo json_encode($this->data); 
    }


	 public function transferFund($uid,$amount,$txType,$recieverPhone){
		// $txType   = vtu|funding|withdrawal|transfer, $amount should be NEGATIVE
		// check to ensure Receiver's phone number Exists
        $con      = $this->con();
		if($this->checkUserExist($recieverPhone,'users','phone')){
			$receiverID     = $this->getUserByPhoneNumber($recieverPhone)['id'];
			if($this->getUserWalletBalance($uid)>=$amount){ 
			   $sqlReciever = $con->query("UPDATE `wallet` SET `wal_balance`=`wal_balance`+'$amount' WHERE `wal_id`='$receiverID'");
			   $sqlSender   = $con->query("UPDATE `wallet` SET `wal_balance`=`wal_balance`-'$amount' WHERE `wal_id`='$uid'");
			   if($sqlSender){
				   if($sqlReciever){
					   $txRef      = substr($this->getUserDetails($uid)['email'],0,3).rand(99999,999999); 
					   if($this->performTxn($uid,$txType,$amount,$recieverPhone,$txRef)){
							echo "Successfully Transfered NGN $amount  to $recieverPhone ";	
                            // Remember to Send Mails to Sender and Receiver									
					   }else{
						   echo "Error Occured During Transfer";
					   }
				   }
			   }			   
				   
			   }else{
				   echo 'Insufficient Fund!';
			   }

			}else{
				echo "Phone Number is not Registered on molo?";
			}
  
	   }


// This function Gets thee User's Wallet Balance
    public function getUserWalletBalance($uid){
        $con        = $this->con();
        $sql        = "SELECT `amount` FROM `wallet` WHERE `wal_id`=?";
        $stmt       = $con->prepare($sql);
        $stmt->bind_param("i",$uid);
        if($stmt->execute()){
            $result  = $stmt->get_result();
            if($result->num_rows>0){
                $row = $result->fetch_assoc();
                $this->Balance = $this->Balance + $row['amount'];
                return  $this->Balance;
            }else{
                return 0;
            }
        }
    }

// This function Updates The user Wallet >>>> Used only during TopUp
public function updateUserWallet($uid,$amount){
    $con         = $this->con();
    $checkUSer   =  $con->query("SELECT * FROM `wallet` WHERE `wal_id`='$uid'");
    $sql 		 =  "";   
    if($checkUSer->num_rows>0){
        $sql.="UPDATE `wallet` SET `wal_balance`=`wal_balance`-'$amount' WHERE `wal_id`='$uid'";
    }else{
        $sql.="INSERT INTO `wallet`(`wal_id`, `wal_balance`,`updatedAt`) VALUES('$uid','$amount',NOW())";
    }

    $query      = $con->query($sql);
    if($query){
        return true;
    }else{
        return false;
    }
}




}