<?php
    require_once('../Common.php');

class Topup{
    use Common;
    private $data    = [];

	public function __construct(){	
        $jsonInput  = file_get_contents('php://input');
        $this->vtuTopUp($jsonInput); 	  
	}

	 public function vtuTopUp($input){
	  // $txType   = AIRTIME_VTU|DATA_VTU,    $uid,$amount,$txType
      $obj      = json_decode($input,true);  
      $phone    = $obj['phone'];  
      $amount   = $obj['amount']; 
      $txType   = $obj['txType'];     
      $network  = $obj['network'];   

      $uid      = $this->getUserByPhoneNumber($phone)['id'];     // get the userId
      $email    = $this->getUserByPhoneNumber($phone)['email'];  // Get the user email

	  $txRef    = substr($email,0,3).rand(99999,999999);  //this is unique per transaction

	   if($this->getUserWalletBalance($uid)>=$amount){
			 if($txType == 'AIRTIME_VTU'){
				 if($this->topUpActionAirtime($amount,$phone,$network)==100){  // Airtime TopUp SUCCESS CODE =100
					if($this->updateUserWallet($uid,$amount)){ 
                        $this->data['message'] = ["status"=>"Airtime topUp SUCCESS!","TopUp_Amount"=>$amount];
						   $this->performTxn($uid,$txType,$amount,$txRef); // Create TransactionRecord
                    		  //send Mail to User            
						}else{ 
                            $this->data['message'] ="Wallet Update Failed";
						}
				 }else{
                    $this->data['message'] = $this->topUpActionAirtime($amount,$phone,$network); //topUpActionAirtime($amount,$phone,$network)
				 } 
			    
			  }  

			 if($txType=='DATA_VTU'){
				if($this->dataTopUpAction($amount,$phone,$network)==100){   //Data TopUp SUCCESS CODE = 100
					if($this->updateUserWallet($uid,$amount)){ 
						$this->data['message'] = ["status"=>"Data topUp SUCCESS!","TopUp_Amount"=>$amount];
						$this->performTxn($uid,$txType,$amount,$txRef); // Create TransactionRecord
                            // Send Email Alert to User for this Tansaction.
                            //send Mail to User
					 }else{ 
                        $this->data['message'] = "Wallet Update Failed";
					 }
				  }else{
					$this->data['message'] = $this->dataTopUpAction($amount,$phone,$network);  // dataTopUpAction($amount,$phone,$network)
				}

			  }  

	   }else{
        $this->data['message'] = 'Insufficient Wallet Balance';
	   }
       echo json_encode($this->data); 

	 }



     // This function Updates The user Wallet >>>> Used only during TopUp (Airtime or Data)
    public function updateUserWallet($uid,$amount){
        $con         = $this->con();
        $checkUSer   =  $con->query("SELECT * FROM `wallet` WHERE `userId`='$uid'");
        $sql 		 =  "";   
        if($checkUSer->num_rows>0){
            $sql.="UPDATE `wallet` SET `wal_balance`=`wal_balance`-'$amount',`updatedAt`=NOW() WHERE `userId`='$uid'";
        }else{
            $sql.="INSERT INTO `wallet`(`userId`,`wal_balance`,`updatedAt`) VALUES('$uid','$amount',NOW())";
        }

        $query      = $con->query($sql);
        if($query){
            return true;
        }else{
            return false;
        }
    }


	 private function topUpActionAirtime($amount,$phone,$network){ 
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://mobileairtimeng.com/httpapi/?userid=08139240318&pass=e5e1a22c7fdccf6e0793909&network=$network&phone=$phone&amt=$amount",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_HTTPHEADER => array(
			"cache-control: no-cache",
			"postman-token: d9873c3a-1b3c-bf85-075f-2afbef364c45"
		  ),
		));
		
		$response = curl_exec($curl);
		$err = curl_error($curl);
		
		curl_close($curl);
		
		if ($err) {
		  echo "cURL Error #:" . $err;
		} else {
		  $resultSplited    = explode("|",$response);  
		  if($resultSplited[0]==100){
			return $resultSplited[0];
		  }else{
			return $resultSplited[1];
		  }
		}
			
	 }
	 

	 private function dataTopUpAction($amount,$phone,$network){ 
		$networkIDs = array("airtel"=>1,"9mobile"=>2,"mtn"=>5,"glo"=>6);
		$network    = $networkIDs[$network];
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://mobileairtimeng.com/httpapi/datatopup.php?userid=08139240318&pass=e5e1a22c7fdccf6e0793909&network=$network&phone=$phone&amt=$amount",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
			  "cache-control: no-cache",
			  "postman-token: 55155293-18c2-27aa-5874-0bed6adadf24"
			),
		  ));
		  
		  $response = curl_exec($curl);
		  $err = curl_error($curl);
		  
		  curl_close($curl);
		  
		  if ($err) {
			echo "cURL Error #:" . $err;
		  } else {
			$resultSplited    = explode("|",$response);  
			  if($resultSplited[0]==100){
				return $resultSplited[0];
			  }else{
				return $resultSplited[1];
			 }
		  }


	 }


}
new Topup();
?>