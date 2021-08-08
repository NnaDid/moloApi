<?php


class DataPlans{


    public function __construct(){
        $network  = $_GET['network'];
        $this->dataPlans($network); 
    }


    
 private function dataPlans($network ){   
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://mobileairtimeng.com/httpapi/get-items?userid=08139240318&pass=e5e1a22c7fdccf6e0793909&service=$network",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_HTTPHEADER => array(
          "cache-control: no-cache",
          "postman-token: 978afdab-17ac-1ecc-0e3a-9ec9056beae0"
        ),
      ));
      
      $resp = curl_exec($curl);
      $err    = curl_error($curl);
      
      curl_close($curl);
      
      if ($err) {
        echo "cURL Error #:" . $err;
      } else {
        $result = json_decode($resp,true);
        echo $resp;

        // if($result['response'] =='OK'){ 
        //      $output = '<select>';
        //      for($i=0;$i<count($result['products']);$i++){
        //           $output.='<option value ="'.$result['products'][$i]['amount'].'">';            
        //           $output.= $result['products'][$i]['amount'].'('.($result['products'][$i]['data']).')'; 
        //           $output.= '</option>';    
        //      }
        //      echo $output.'</select>';
        //  }
      }
   }

}


new DataPlans();
?>