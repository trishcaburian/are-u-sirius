<?PHP
//include("initer.php");
//session_start();
if(isset($_SESSION['usersess'])){
	header("Location: testpage.php");
}
//formhandling
	if($_POST && isset($_POST['loginuser'], $_POST['username'], $_POST['password'])){
		$username = trim($_POST['username']);
		$password = trim($_POST['password']);
	
        $uspw = $password." ".$username;
        $pythcommand = "/usr/bin/python /home/siriuser/pythons/thes.py -p"." ".$uspw;
        $pythlogin = exec($pythcommand);
		$pythlogin = trim($pythlogin);
	
		//echo "<script type='text/javascript'>alert('$pythlogin');</script>";
        if($pythlogin == "This is a Domain Admin"){
            if(!isset($_SESSION)){
                session_start();
                $_SESSION['usersess'] = $username;
				$_SESSION['role'] = "DA";
				//echo "<script type='text/javascript'>alert('Session!');</script>";
			}
			header("Location: testpage.php");
			//echo "<script type='text/javascript'>alert('$pythlogin');</script>";
        }
		elseif($pythlogin == "This is a Non-Admin"){
			if(!isset($_SESSION)){
                session_start();
                $_SESSION['usersess'] = $username;
				$_SESSION['role'] = "U";
				//echo "<script type='text/javascript'>alert('Session!');</script>";
			}
			header("Location: testpage.php");
			//echo "<script type='text/javascript'>alert('$pythlogin');</script>";
		}
		else{
			echo "<script type='text/javascript'>alert('$pythlogin');</script>";
		}

	}
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
      <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
      <title>Login</title>
      <link rel="STYLESHEET" type="text/css" href="style/sitestyle.css" />
      <script type='text/javascript' src='scripts/gen_validatorv31.js'></script>
</head>

<body>
	<form id='login' action="<?PHP echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method='post' accept-charset='UTF-8'>
		<fieldset >
			<legend>Login</legend>
			<input type='hidden' name='submitted' id='submitted' value='1'/>

			<label for='username' >UserName*:</label>
			<input type='text' name='username' id='username' maxlength="50" />

			<label for='password' >Password*:</label>
			<input type='password' name='password' id='password' maxlength="50" />

			<input type='submit' name='loginuser' value='Submit' />
		</fieldset>
	</form>
	<script type='text/javascript'>
	// <![CDATA[

		var frmvalidator  = new Validator("login");
		frmvalidator.EnableOnPageErrorDisplay();
		frmvalidator.EnableMsgsTogether();

		frmvalidator.addValidation("username","req","Please provide your username");
		
		frmvalidator.addValidation("password","req","Please provide the password");

	// ]]>
	</script>
</body>
