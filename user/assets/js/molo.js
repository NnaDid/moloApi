$(document).ready(function(){
    // For Airtime Top Up and Funding wallet
    const topUpUrl        = "../api/v1/Topup/index.php";
    const fundWalletURL   = "../api/v1/Wallet/index.php";

    $(document).on("submit",".airTimeTopUp",function(evt){
          evt.preventDefault();
          var phone   = $("#phone").val();
          var network = $("#network").val();
          var amount  = $("#tamount").val();
          $(".topUpStatus").html("<i class='lni-spinner lni-arrow-left lni-fade-left-effect'></i><i class='lni-arrow-right lni-fade-right-effect'></i>");
          $.ajax({
              url:topUpUrl,
              method:"GET",
              data:{phone:phone,network:network,amount:amount,txType:"vtu",action:'topUp'},
              success:(resp)=>{
                  console.log(resp);
                  $(".topUpStatus").html('<div class ="alert alert-info">'+resp+'</div>'); 
              }
          })
          
        });	 
   
   // Load the Data Plans Based on Network
$("#service").change(function(evt){
    $("#price").html("<option><i class='lni-spinner lni-arrow-left lni-fade-left-effect'></i></option>");
    let service = $(this).val();
       $.ajax({
           url:topUpUrl    ,
           method:"POST",
           data:{service:service},
           success:(resp)=>{
               console.log(resp);
               $("#price").html(resp); 
           }
       })
});

//fundTransfer  transferStatus
//Data TopUp Script dataTopUpStatus  dataTopUp
$(document).on("submit",".dataTopUp",function(evt){
    evt.preventDefault();
    var dphone   = $("#dphone").val();
    var service  = $("#service").val();
    var price    = $("#price").val(); 
    $(".dataTopUpStatus").html("<i class='lni-spinner lni-arrow-left lni-fade-left-effect'></i><i class='lni-arrow-right lni-fade-right-effect'></i>");
    $.ajax({
        url:requestUrl,
        method:"GET",
        data:{phone:dphone,network:service,amount:price,txType:"vtuData",action:'topUp'},
        success:(resp)=>{
            $(".dataTopUpStatus").html('<div class ="alert alert-info">'+resp+'</div>'); 
        }
    })
    
  });	 
 

    // Make Deposit or Fund Wallet
	$(".fundWalletForm").submit(function(evt){
		evt.preventDefault();
		let amount     = $("#amount").val()*1;
        let email      = $("#email").val();	
        let action     = 'fund';
    if(parseInt(amount)>99){
        payWithPaystack(email,amount,action);
		}else{
		  alert("Amount too Small");
		}
	});
	
//    pk_live_4043212da0f8df3fd6f092df1cb9e0f19d1cb1ae    // MOLO PUB_KEY
	function payWithPaystack(email,amount,action){
		var handler = PaystackPop.setup({
			key: 'pk_live_7568eb1ef389bd0454df4d963ac0d4593e9cd567',
			email: email,
			amount: amount+'00',
			currency: "NGN",
			ref: 'MOLO'+Math.floor((Math.random() * 1000000000) + 1), 
			callback: function(response){
				alert("Payment Successfull "+response.reference);
                let dataToSend = {
                    amount:amount,
                    ref:response.reference,
                    action:action
                };
               
				$.ajax({
    				url:requestUrl,
    				method:"POST",
    				data:JSON.stringify(dataToSend),
                    success:function(res){
                    if(res==1){
                        $(".fundWalletStatus").html('<div class ="alert alert-success"> Payment Successful! Ref Id ='+response.reference+'</div>');
                    }else{ 
                        $(".fundWalletStatus").html('<div class ="alert alert-danger">'+res+'</div>');    				
                    }
                    }
                });
			},
			onClose: function(){
				$(".fundWalletStatus").html(`<div class ="alert alert-danger">Please make sure you complete your payment process!!!</div>`);
			}
		});
		handler.openIframe();
	  }

   
   
   });	

   fetch(fundWalletURL, { 
    method: 'POST',
    body: JSON.stringify(dataToSend),
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    },
  })
    .then((response) => response.json())
    .then((responseJson) => {
      //Hide Loader

    })
    .catch((error) => {
      //Hide Loader
    });