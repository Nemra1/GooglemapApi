
<?php
session_start(); //session start
include ('libraries/Google/autoload.php');
//ini_set('display_errors', 'On');
//ini_set('html_errors', 0);
    

//Insert your cient ID and secret 
//You can get it from : https://console.developers.google.com/
$client_id = '74526084907-2sjbn5p6i7btu9t2tblbqmr5h02f3tc0.apps.googleusercontent.com'; 
$client_secret = 'SNbFXKP6H48SlMM6gp2gg1D5';
$redirect_uri ="http://gbsservice.in/hacker/Login_api/Api_call.php";

//database
$db_username = "gbsservi_check"; //Database Username
$db_password = "checkuser65"; //Database Password
$host_name = "localhost"; //Mysql Hostname
$db_name = "gbsservi_security"; //Database Name


//incase of logout request, just unset the session var
if (isset($_GET['logout'])) {
  unset($_SESSION['access_token']);
}

/************************************************
  Make an API request on behalf of a user. In
  this case we need to have a valid OAuth 2.0
  token for the user, so we need to send them
  through a login flow. To do this we need some
  information from our API console project.
 ************************************************/
$client = new Google_Client();
$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setRedirectUri($redirect_uri);
$client->addScope("email");
$client->addScope("profile");

/************************************************
  When we create the service here, we pass thess
  client to it. The client then queries the service
  for the required scopes, and uses that when
  generating the authentication URL later.
 ************************************************/
$service = new Google_Service_Oauth2($client);

/************************************************
  If we have a code back from the OAuth 2.0 flow,
  we need to exchange that with the authenticate()
  function. We store the resultant access token
  bundle in the session, and redirect to ourself.
*/
  
if (isset($_GET['code'])) {
  $client->authenticate($_GET['code']);
  $_SESSION['access_token'] = $client->getAccessToken();
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
  exit;
}

/************************************************
  If we have an access token, we can make
  requests, else we generate an authentication URL.
 ************************************************/
 
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
  $client->setAccessToken($_SESSION['access_token']);
} else {
  $authUrl = $client->createAuthUrl();
}


//Display user info or display login url as per the info we have.
if (isset($authUrl))
{ 	//show login url
	//echo '<div align="center">';
	//echo '<h3>Login with Google</h3>';
	//echo '<div>Please click login button to connect to Google.</div>';
	//echo '<a class="login" href="'. $authUrl .'"> <img src="images/google-login-button.png" /></a>';
	//echo '</div>';
	
} else {
	
	$user = $service->userinfo->get(); //get user info 
	
	// connect to database
	//$mysqli = new mysqli($host_name, $db_username, $db_password, $db_name);
    //if ($mysqli->connect_error) {
        //die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
    //}
	
	//check if user exist in database using COUNT
	//$result = $mysqli->query("SELECT COUNT(google_id) as usercount FROM google_users WHERE google_id=$user->id");
	
	//$user_count = $result->fetch_object()->usercount; //will return 0 if user doesn't exist
	
	//show user picture
	echo '<img src="'.$user->picture.'" style="float: right;margin-top: 40px;" />';
	
        echo '<br>';
        echo '<br>';
        
        echo 'Hi '.$user->name.',Click Here ! [<a href="'.$redirect_uri.'?logout=1">Log Out</a>]';
        
		//$statement = $mysqli->prepare("INSERT INTO google_users (google_id, google_name, google_email, google_link, google_picture_link) VALUES (?,?,?,?,?)");
		//$statement->bind_param('issss', $user->id,  $user->name, $user->email, $user->link, $user->picture);
		//$statement->execute();
		//echo $mysqli->error;
		
	//print user details
	echo '<pre>';
	$id=$user->id;
    $name=$user->name;	
	$email=$user->email;
	//print_r($id);
	//print_r($user->email);
	echo '</pre>';
}
echo '</div>';


?>
<!DOCTYPE html>
<html lang="en" >

<head>
  <meta charset="UTF-8">
  <title>Online-Registration</title>

      <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <link href="//db.onlinewebfonts.com/c/a4e256ed67403c6ad5d43937ed48a77b?family=Core+Sans+N+W01+35+Light" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="form.css" type="text/css">
    <div class="body-content">
    <div class="module">

    <h4>'Hi '. <?php echo $name?>.', Please Sign up to continue !</h4>
    <br>    
    <form class="form" method="post" enctype="multipart/form-data" autocomplete="off">
      
      <div class="alert alert-error"></div>
      
      <input type="text"  name="username"  placeholder="ID" value="<?php echo $id; ?>" disabled="disabled"/>
      
      <input type="text" placeholder="User Name" name="username" value="<?php echo $name;?>" disabled="disabled"/>
      
      <input type="email" placeholder="Email" name="email" value="<?php echo $email;?>" disabled="disabled"/>
      
      <input type="password" placeholder="Password" name="password" autocomplete="new-password" required />
      
      <input type="password" placeholder="Confirm Password" name="confirmpassword" autocomplete="new-password" required />
     
        <br>
        
        <h6>*After Registration your Google account is logged out automatically</h6>
        
        <br>
        
      <input type="submit" value="Register" name="submit" class="btn btn-block btn-primary" />
     
        <br><br>
    
        <?php  echo '<a class="login" href="' . $authUrl . '"><img src="images/google-login-button.png" /></a>';  ?>

     
    </form>
  </div>
</div>
</body>
</html>
<?php

 if (isset($_POST['submit']))
{
            echo "<script>
                alert('You have registered successfully !');
            </script>";
}
?>
