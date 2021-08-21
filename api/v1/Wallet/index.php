<?php
   header('Content-type: application/json');
   require_once('../Common.php');

class Wallet{

    use Common;
    private $data    = [];
    private $Balance = 0;

    public function __construct(){
        $jsonInput  = file_get_contents('php://input');

        $action_obj = json_decode($jsonInput,true);  

        switch($action_obj['action']){
            case "fund"     :   $this->fundWallet($jsonInput);           break;
            case "bal"      :   $this->getUserWalletBalance($jsonInput); break;
            case "transfer" :   $this->transferFund($jsonInput);         break;
            case "donate"   :   $this->donate($jsonInput);               break;
        }
       
    }


    public function fundWallet($input){
        $con    = $this->con();
        $obj    = json_decode($input,true);  

        $email    = $obj['email'];  
        $amount   = $obj['amount'];
        $ref      = $obj['ref'];
        $txType   = "funding";
        
        $uid      = $this->getMemberDetails($email)['id'];
        // get the verification object
        $confirm_payment_object = $this->verifyFunding($ref);
        
        // This way we affirm the user has actually made payment
        if ($confirm_payment_object['status']==='success' && $confirm_payment_object['amount']>=$amount){
            
                if($this->performTxn($uid,$txType,$amount,$ref)){
                    // Check if transaction is recorded first before updateting wallet
                    $checkUSer  =  $con->query("SELECT * FROM `wallet` WHERE `userId`='$uid'");
                    $sql2 		 =  "";
                    if($checkUSer->num_rows>0){
                        $sql2.="UPDATE `wallet` SET `wal_balance`=`wal_balance`+'$amount',`updatedAt`=NOW() WHERE `userId`='$uid'";
                    }else{        
                        $sql2.="INSERT INTO `wallet`(`userId`,`wal_balance`,`updatedAt`) VALUES('$uid','$amount',NOW())";
                    }
                    $fundingQuery      = $con->query($sql2);
                    if($fundingQuery){
                            $currentWalletBalance =  $checkUSer->fetch_assoc()['wal_balance'] + $amount;
                            $lastUpdated  = date("Y-m-d H:i:s",strtotime("now"));
                            $this->data['message'] = array(
                                                        "status"=>"Success",
                                                        "amount_funded"=>$amount,
                                                        "wallet_balance"=>$currentWalletBalance,
                                                        "lastUpdated"=>$lastUpdated
                                                    );
                    
                    }else{
                        $this->data['message'] = "Error Funding Account";
                    }
                }else{
                    $this->data['message'] = "Transaction Error";
                }
                $con->close();
                echo json_encode($this->data); 

        }else{
            $this->data['message'] = $confirm_payment_object;
        }

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
public function getUserWalletBalance($input){
    $con    = $this->con();
    $obj    = json_decode($input,true); 
    $email    = $obj['email'];  
    $uid      = $this->getMemberDetails($email)['id'];
    $sql      = "SELECT `wal_balance` FROM `wallet` WHERE `userId`=?";
    $stmt     = $con->prepare($sql);
    $stmt->bind_param("i",$uid);
    if($stmt->execute()){
        $result  = $stmt->get_result();
        if($result->num_rows>0){
            $row = $result->fetch_assoc();
            $this->Balance = $row['wal_balance'];            
            $this->data['message'] = ["status"=>"Success","wallet_balance"=>$this->Balance];
        }else{
            $this->data['message'] = ["status"=>"Success","wallet_balance"=>$this->Balance];
        }
    }
    echo json_encode($this->data); 
}


// this function tries to verify payment/Funding wallet 
// it TAKES a ref argument and returns an array of objects
// The object contains status and amount keys which we shall use to confirm payment
private function verifyFunding($ref){
    $curl = curl_init();
  
    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.paystack.co/transaction/verify/:{$ref}",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer sk_test_3c5a4b6ef47a9a7a0cda042092adf7dee8ceb949",
        "Cache-Control: no-cache",
      ),
    ));
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    
    if ($err) {
        $this->data['message']  = "cURL Error #:" . $err;
    } else {
      $res = json_decode($response,true);
      if($res['status']==true){
        return $res['data'];
      }
    }

}


}

new Wallet();