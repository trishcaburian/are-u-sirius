<?PHP
//include("initer.php");
session_start();
if(!isset($_SESSION['usersess'])){ 
	header("Location: login.php");
}
else{
//formhandling
	if($_POST && isset($_POST['adduser'], $_POST['firstname'], $_POST['surname'], $_POST['usergroup'], $_POST['password'], $_POST['username'])){
		$username = trim($_POST['username']);
		$password = trim($_POST['password']);
		$firstname = trim($_POST['firstname']);
		$surname = trim($_POST['surname']);
		$usergroup = trim($_POST['usergroup']);
		
		$pythcommand = "sudo /usr/bin/python /home/siriuser/pythons/add.py -n ".$firstname;
		$pythcommand = $pythcommand." -s ".$surname." -g ".$usergroup." -p ".$password." ".$username;
        	$pythadduser = shell_exec($pythcommand." 2>&1");
		$pythadduser = trim($pythadduser);

		$addsuccess = "Changing UNIX and samba passwords for ".$username;
		if($pythadduser == $addsuccess){
			echo "<script type='text/javascript'>alert('Successfully added user ".$username."!')</script>";
		}
		else{
			echo "<script type='text/javascript'>alert('$pythadduser')</script>";
		}
	}
	//list users
	function listUsers(){
		$getUsercommand = "sudo smbldap-userlist | awk '{print $2}'";
		$pythuserlist = shell_exec($getUsercommand." 2>&1");
		/*echo "<script type='text/javascript'>alert('$pythadduser')</script>";*/
		$userlistTrimmed=str_replace('|',"",$pythuserlist);
		echo $userlistTrimmed;
	}
	//delete from smbldap	
	//$userdel="deleteme";
	//$deleteUsercommand = "sudo smbldap-userdel ".$userdel;
	//$pythnewuserlist = shell_exec($deleteUsercommand." 2>&1");
	//echo $pythnewuserlist;
	function deleteUser($array){
		//$array =array("del1","del2","del3");
		foreach($array as $value){
			$delUsercommand ="sudo smbldap-userdel ".$value;
			$pythnewuserlist = shell_exec($delUsercommand." 2>&1");
			echo "exeuted";	
		}	
	}
	//$getUsercommand = "sudo smbldap-userlist | awk '{print $2}'";
	//$pythuserlist = shell_exec($getUsercommand." 2>&1");
	/*echo "<script type='text/javascript'>alert('$pythadduser')</script>";*/	
	//$userlistTrimmed=str_replace('|',"",$pythuserlist);
	
	function convertGrStringtoGID($groupName){
		$getGIDcommand="sudo smbldap-grouplist | awk '/".$groupName."/ {{print $1}}'";
		$GID= shell_exec($getGIDcommand." 2>&1");
		return $GID;

	}


	//groupname can be in array
	function editUser($username, $groupname, $oldusername){
			$editusercommand ="sudo smbldap-usermod ";
			if(!empty($username)){
				$editusercommand=$editusercommand."-r ".$username." ";
			}
			if(!empty($groupname)){
				$gid=convertGrStringtoGID($groupname);
				$editusercommand=$editusercommand."-g ".$gid;
			}
			$editusercommand=$editusercommand." ".$oldusername;
			echo $editusercommand;
	}
	editUser("editmeXXX","10000","editme");
	//listusers();
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
			<legend>Add New User</legend>
			<input type='hidden' name='submitted' id='submitted' value='1'/>

			<label for='username' >UserName*:</label>
			<input type='text' name='username' id='username' maxlength="50" />
			</br>
			<label for='password' >Password*:</label>
			<input type='password' name='password' id='password' maxlength="50" />
			</br>
			<label for='firstname' >First Name*:</label>
			<input type='text' name='firstname' id='firstname' maxlength="50" />
			</br>
			<label for='surname' >Surame*:</label>
			<input type='text' name='surname' id='surname' maxlength="50" />
			</br>
			<label for='usergroup' >Group*:</label>
			<input type='text' name='usergroup' id='firstname' maxlength="50" />
			</br>
			<input type='submit' name='adduser' value='Submit' />
		</fieldset>
	</form>
	<textarea rows="4" cols="50"></textarea>
	<a href="logout.php">Log Out</a>
	<script type='text/javascript'>
	// <![CDATA[

		var frmvalidator  = new Validator("login");
		frmvalidator.EnableOnPageErrorDisplay();
		frmvalidator.EnableMsgsTogether();

		frmvalidator.addValidation("username","req","Please provide your username");
		
		frmvalidator.addValidation("password","req","Please provide the password");
		
		frmvalidator.addValidation("firstname","req","Please provide a first name");

		frmvalidator.addValidation("surname","req","Please provide surname");

		frmvalidator.addValidation("usergroup","req","Please provide a usergroup");
	// ]]>
	</script>
</body>
