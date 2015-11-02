<!DOCTYPE html/>
<html>
<head lang="en">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="UTF-8">
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="login.css">
	<title> Cough Monitor Portal </title>
</head>
<body>

	
	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>


<script>
document.onload(function(){
			var email = document.getElementById("inputEmail").value;
			var pass = document.getElementById("inputPassword").value;
			var xhttp = new XMLHttpRequest();
  			xhttp.onreadystatechange = function() {
	   		 	if (xhttp.readyState == 4 && xhttp.status == 200) {
	   		 		response = JSON.parse(xhttp.responseText);
	   		 		if(response.success == false){
		      			document.innerHTML = response.message;
					} else{
              			window.location.replace('add-pt.html');

						//redirect
					}
			  	}
			}
		  xhttp.open("POST", "/php/login.php", true);
		  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		  xhttp.send("email="+email+"&pass="+pass);
		})
</script>


	
</body>
</html>
