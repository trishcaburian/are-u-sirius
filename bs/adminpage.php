
<?PHP
//include("initer.php");
/*session_start();
if(!isset($_SESSION['usersess'])){ 
	header("Location: login.php");
}*/
//else{
//formhandling
	if($_POST && isset($_POST['adduser'], $_POST['firstname'], $_POST['surname'], $_POST['usergroup'], $_POST['password'], $_POST['username'])){
		$username = trim($_POST['username']);
		$password = trim($_POST['password']);
		$firstname = trim($_POST['firstname']);
		$surname = trim($_POST['surname']);
		$usergroup = trim($_POST['usergroup']);
		
		 //echo "<script type='text/javascript'>alert('$usergroup')</script>";	
		$pythcommand = "sudo /usr/bin/python /home/siriuser/pythons/add.py -n ".$firstname;
		$pythcommand = $pythcommand." -s ".$surname." -g \"".$usergroup."\" -p ".$password." ".$username;
        	$pythadduser = shell_exec($pythcommand." 2>&1");
		$pythadduser = trim($pythadduser);
		 //echo "<script type='text/javascript'>alert('$pythcommand')</script>";
		$addsuccess = "Changing UNIX and samba passwords for ".$username;

		$smbenable = shell_exec("sudo smbpasswd -e ".$username." 2>&1");
		$smbenable = trim($smbenable);
		echo "<script type='text/javascript'>alert('$smbenable')</script>";
		if($pythadduser == $addsuccess){
			echo "<script type='text/javascript'>alert('Successfully added user ".$username."!')</script>";
		}
		else{
			echo "<script type='text/javascript'>alert('$pythadduser')</script>";
		}
	}
	
	/*if($_POST && isset($_POST['add-device'], $_POST['devicename']){
		//insert add device code here
		
	}*/
	
	//list users
	function listUsers(){
		$getUsercommand = "sudo smbldap-userlist | awk '{print $2}'";
		$pythuserlist = shell_exec($getUsercommand." 2>&1");
		/*echo "<script type='text/javascript'>alert('$pythadduser')</script>";*/
		$userlistTrimmed=str_replace('|'," ",$pythuserlist);
		return $userlistTrimmed;
	}
	
	function generateUserTable(){
		$usernames = listUsers();
		
		$users = preg_split("/\s+/", $usernames);
		$rownumber = 0;
		foreach($users as $user){
			if($rownumber++ < 4){
				continue;
			}
			$user = trim($user);
			$fullnameComm = "smbldap-usershow ".$user." | awk '/displayName: / {{print $2, $3}}'";
			$fullName = trim(shell_exec($fullnameComm));
			
			if(empty($user)){
				break;
			}
			//User group lookup
			$gidcomm = "smbldap-usershow ".$user." | awk '/gidNumber: / {{ print $2 }}'";
			$getgid = trim(shell_exec($gidcomm." 2>&1"));
			//$getgid = trim($getgid);
			
			$gnamecomm = "sudo smbldap-grouplist | awk '/".$getgid."/ {{print $2,$3}}'";
			$getgrpname = trim(shell_exec($gnamecomm." 2>&1"));
			//$getgrpname = trim($getgrpname);
			$userGroup = trim(str_replace('|',"",$getgrpname));
			
			echo '<tr>';
			echo "<td><input type='checkbox' name='checkbox[]' value='". $user . "'> </td>";
			echo '<th>'.$user.'</th>';
			echo '<td>'.$fullName.'</td>';
			echo '<td>'.$userGroup.'</td>';
			echo '</tr>';
			
		}
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
			echo "executed";	
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
	//editUser("editmeXXX","10000","editme");
	//listusers();
//}
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
  <head>
    <!-- Required meta tags always come first -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
	
	<!-- Validator -->
	<script type='text/javascript' src='scripts/gen_validatorv31.js'></script>
  </head>
  <body>

	<div class="container">    
		<h1>SIRIUS</h1>
		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
		  <li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#userlist" role="tab">User List</a>
		  </li>
		  <li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#deviceadd" role="tab">Add Device</a>
		  </li>
		  <li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#resources" role="tab">Resources</a>
		  </li>
		</ul>

		<!-- Tab panes -->
		<div class="tab-content">
			<div class="tab-pane" id="userlist" role="tabpanel">
				<div class="container">
					<table class="table table-striped">
						<thead class="thead-inverse">
							<tr>
								<th> </th>
								<th>Username</th>
								<th>First Name</th>
								<th>Group</th>
								<!--<th>...</th>-->
							</tr>
						</thead>
						<tbody class="username-table">
							<!--<tr>
								<th>gendo</th>
								<td>Gendo Ikari</td>
								<td>Domain Admins</td>
								<td></td>
							</tr>
							<tr>
								<th>amuro</th>
								<td>Amuro Ray</td>
								<td>Students</td>
								<td></td>
							</tr>-->
							<?php generateUserTable() ?>
						</tbody>
					</table>
					
					<!-- Button trigger modal -->
					<button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#adduserModal">
						Add new user
					</button>

					<!-- Modal -->
					<div class="modal fade" id="adduserModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">×</span>
									</button>
									<h4 class="modal-title" id="myModalLabel1">Add New User</h4>
								</div>

								<form id='newuser' action='' method='post' accept-charset='UTF-8'>
									<div class="modal-body">
									
										<fieldset >
											<!--<legend>Add New User</legend-->
											<!--<input type='hidden' class="form-control" name='submitted' id='submitted' value='1'/>-->
											
											<div class="form-group">
												<label for='username' >UserName*:</label>
												<input type='text' class="form-control" name='username' id='username' maxlength="50" required/>
											</div>
											<div class="form-group">
												<label for='password' >Password*:</label>
												<input type='password' class="form-control" name='password' id='password' maxlength="50" required/>
											</div>
											<div class="form-group">
												<label for='firstname' >First Name*:</label>
												<input type='text' class="form-control" name='firstname' id='firstname' maxlength="50" required/>
											</div>
											<div class="form-group">
												<label for='surname' >Surname*:</label>
												<input type='text' class="form-control" name='surname' id='surname' maxlength="50" required/>
											</div>
											<div class="form-group">
												<label for='usergroup' >Group*:</label>
												<input type='text' class="form-control" name='usergroup' id='firstname' maxlength="50" required/>
											</div>
										</fieldset>
									
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
										<!-- if nextuser, open clean modal. possibly php-handled -->
										<input type='submit' class="btn btn-info" name='nextuser' value='Submit and Add another user' />
										<input type='submit' class="btn btn-primary" name='adduser' value='Submit' />
									</div>
								</form>
							</div>
						</div>
					</div>
					<!-- END -->
				</div>
			</div>
			<div class="tab-pane" id="deviceadd" role="tabpanel">
				<div class="container">


					<!--<form id='usermgmt' action=" " method="post">-->
						<table class="table">
							<thead class="thead-inverse">
								<tr>
									<th> </th>
									<th>Device Name</th>
									<th>IP Address</th>
								</tr>
							</thead>
							<tbody>
								<td>
									<input type="checkbox" name="userchk">
								</td>
								<th>PC1</th>
								<td>---------</td>
							</tbody>
						</table>
					<!--</form>-->

					<!-- new device button -->
					<button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#add-deviceModal">
						Add new device
					</button>
					
					<!-- new device modal-->
					<div class="modal fade" id="add-deviceModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
									<h4 class="modal-title" id="myModalLabel1">Add device</h4>
								</div>
								<!-- newdevice form -->
								<form id='newdevice' action=" " method='post' accept-charset='UTF-8'>
									<div class="modal-body">
										<fieldset >
											<div class="form-group">
												<label for='devicename' >Device Name*:</label>
												<input type='text' class="form-control" name='devicename' id='devicename' maxlength="50" required />
											</div>
											
										</fieldset>
									
									<!-- FORM END -->
									</div>
								
									<div class="modal-footer">
										<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
										<input type='submit' class="btn btn-info" name='nextuser' value='Submit and Add new device' />
										<input type='submit' class="btn btn-primary" name='add-device' value='Submit' />
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane" id="resources" role="tabpanel">
				<div class="container">
					
				</div>
			</div>
		</div>
	</div> <!--container end-->
    <!-- jQuery first, then Bootstrap JS. -->
    <script src="https://code.jquery.com/jquery-2.2.4.js"></script>
    <script src="js/bootstrap.min.js"></script>
	
	<!-- MODIFIED HERE -->
	<script>
		/*$(document).ready(function(){
		        $(".username-table").append(" <tr><th>gendo3</th><td>Gendo Ikari3</td><td>Domain Admin</td><td></td></tr>");
		});*/
	</script>
	<!--MODIFIED TILL HERE-->



  </body>
</html>
