<!-- PHP START
<?PHP
//include("initer.php");
//session_start();
/*if(!isset($_SESSION['usersess'])){ 
	header("Location: login.php");
}
else{*/
//formhandling
	if(isset($_POST['adduser'])){
		$username = trim($_POST['username']);
		$password = trim($_POST['password']);
		$firstname = trim($_POST['firstname']);
		$surname = trim($_POST['surname']);
		$usergroup = trim($_POST['usergroup']);
		
		$pythcommand = "sudo /usr/bin/python /home/siriuser/pythons/add.py -n ".$firstname;
		$pythcommand = $pythcommand." -s ".$surname." -g ".$usergroup." -p ".$password." ".$username;
        $pythadduser = shell_exec($pythcommand." 2>&1");
		$pythadduser = trim($pythadduser);

		$smbenable = shell_exec("sudo smbpasswd -e ".$username." 2>&1");
		$smbenable = trim($smbenable);
		echo "<script type='text/javascript'>alert('$smbenable')</script>";
		$addsuccess = "Changing UNIX and samba passwords for ".$username;
		if($pythadduser == $addsuccess){
			echo "<script type='text/javascript'>alert('Successfully added user ".$username."!')</script>";
		}
		else{
			echo "<script type='text/javascript'>alert('$pythadduser')</script>";
		}
	}
	
	else if(isset($_POST['confirmdelete'])){
		deleteUser($_POST['checkbox']);
	}
	
	else if(isset($_POST['add-device'])){
		$device = trim($_POST['devicename']);
		$ndcomm = "sudo smbldap-useradd -w ".$device;
		$ndexec = shell_exec($ndcomm." 2>&1");
		echo "<script type='text/javascript'>alert('Successfully added device ".$device."!')</script>";
	}
	
	function test(){
		 print_r($_POST['checkbox']); 
	}

	//list users
	function listUsers(){
		$getUsercommand = "sudo smbldap-userlist -u | awk '{print $2}'";
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
			
			$gnamecomm = "sudo smbldap-grouplist | awk '/".$getgid."/ {{print $2,$3}}'";
			$getgrpname = trim(shell_exec($gnamecomm." 2>&1"));
			$userGroup = trim(str_replace('|',"",$getgrpname));
			
			echo '<tr>';
			echo "<td><input type='checkbox' name='checkbox[]' value='". $user . "'> </td>";
			echo '<th>'.$user.'</th>';
			echo '<td>'.$fullName.'</td>';
			echo '<td>'.$userGroup.'</td>';
			echo '</tr>';
			
		}
	}
	
	function listMachines(){
		$getUsercommand = "sudo smbldap-userlist -m | awk '{print $2}'";
		$pythuserlist = shell_exec($getUsercommand." 2>&1");
		$maclistTrimmed=str_replace('|'," ",$pythuserlist);
		return $maclistTrimmed;
	}
	
	function generateMachineTable(){
		$maclist = listMachines();
		
		$machines = preg_split("/\s+/", $maclist);
		$rownumber = 0;
		foreach($machines as $machine){
			if($rownumber++ < 2){
				continue;
			}
			if(empty($machine)){
				break;
			}
			echo '<tr>';
			echo "<td><input type='checkbox' name='checkbox[]' value='". $machine . "'> </td>";
			echo '<th>'.$machine.'</th>';
			echo '</tr>';
		}
	}
	
	function deleteUser($array){
		//$array =array("del1","del2","del3");
		foreach($array as $value){
			$delUsercommand ="sudo smbldap-userdel ".$value;
			$pythnewuserlist = shell_exec($delUsercommand." 2>&1");
			echo "executed";	
		}	
	}
	
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
//}
?>

PHP END -->

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
  <head>
    <!-- Required meta tags always come first -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/sirius.min.css">
	
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:300,400,500,700" type="text/css">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

	<!-- Validator -->
	<script type='text/javascript' src='scripts/gen_validatorv31.js'></script>
	
	<!-- some iframe css code -->
	<style>
		.hadoopIframe {
			position: relative;
			padding-bottom: 65.25%;
			padding-top: 30px;
			height: 0;
			overflow: auto; 
			-webkit-overflow-scrolling:touch; //<<--- THIS IS THE KEY 
			border: solid black 1px;
		} 
		.hadoopIframe iframe {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
		}
	</style>
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
		
		<!--floating action button-->
					<!-- <div class="btn-group btn-group-lg dropup floating-action-button-custom" valign="bottom">
					  <button type="button" class="btn btn-info btn-fab" id="round_btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><i class="material-icons">add</i>
					  </button>
					  <ul class="dropdown-menu dropdown-menu-right">
						<li><a href="#" class="btn btn-danger btn-fab del_anchor" id="round_btn" data-toggle="tooltip" data-placement="top" title="Add User"><i class="material-icons">note_add</i></a></li>
						<li><a href="#" class="btn btn-danger btn-fab del_anchor" id="round_btn" data-toggle="tooltip" data-placement="top" title="Edit User"><i class="material-icons">mode_edit</i></a></li> 
						<li><a href="#" class="btn btn-danger btn-fab del_anchor" id="round_btn" data-toggle="tooltip" data-placement="top" title="Delete User"><i class="material-icons">clear</i></a></li>
					  </ul>
					</div> -->
					
					
		<!-- Tab panes -->
		<div class="tab-content">
				<div class="tab-pane" id="userlist" role="tabpanel">
				
					<!--floating action button-->
						<div class="btn-group btn-group-lg dropup floating-action-button-custom" id="floatingbutton" valign="bottom">
						  <button type="button" class="btn btn-info btn-fab" id="round_btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><i class="material-icons">add</i>
						  </button>
						  <ul class="dropdown-menu dropdown-menu-right">
							<li data-toggle="modal" data-target="#adduserModal"><a href="#" class="btn btn-danger btn-fab del_anchor" id="round_btn" data-toggle="tooltip" data-placement="top" title="Add User" ><i class="material-icons">note_add</i></a></li>
							<li><a href="#" class="btn btn-danger btn-fab del_anchor" id="round_btn" data-toggle="tooltip" data-placement="top" title="Edit User"><i class="material-icons">mode_edit</i></a></li> 
							<li data-toggle="modal" data-target="#confirmdeleteModal"><a href="#" class="btn btn-danger btn-fab del_anchor" id="round_btn" data-toggle="tooltip" data-placement="top" title="Delete User"><i class="material-icons">clear</i></a></li>
						  </ul>
						</div>  
					<!-- floating action button END -->
					
					<div class="container">
						<form id='usertable-form' action='' method='post' accept-charset='UTF-8'>
							<table class="table">
								<thead class="thead-inverse">
									<tr>
										<th> </th>
										<th>Username</th>
										<th>First Name</th>
										<th>Group</th>
									</tr>
								</thead>
								<tbody class="username-table">
									<?php generateUserTable() ?>
								</tbody>
							</table>
						</form>
						
						<!-- delete users confirm modal -->
						<div class="modal fade" id="confirmdeleteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
							<!--<form id="userdel" action='' method='post' accept-charset='UTF-8'>-->
								<div class="modal-dialog" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">×</span>
											</button>
										</div>
										<div class="modal-body">
											Are you sure you want to delete these users? <!-- maybe show selected users? -->
										</div>
										<div class="modal-footer">
											<input type='submit' class="btn btn-primary" name='confirmdelete' value='Submit' form="usertable-form"/>
										</div>
									</div>
								</div>
							<!--</form>-->
						</div>
						<!-- Button trigger modal -->
						<!-- <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#adduserModal">
							Add new user
						</button> -->
						
						
						
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
									<form id='newuser' action='<?PHP echo htmlspecialchars($_SERVER['PHP_SELF']); ?>' method='post' accept-charset='UTF-8'>
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
											<input type='submit' class="btn btn-primary" name='adduser' value='Submit' />
										</div>
									</form>
								</div>
							</div>
						</div>
						<!-- END -->
					</div>
				 </div> 
			<!-- </div> -->
			<div class="tab-pane" id="deviceadd" role="tabpanel">
				
				<!--floating action button-->
						 <div class="btn-group btn-group-lg dropup floating-action-button-custom" id="floatingbutton" valign="bottom">
						  <button type="button" class="btn btn-info btn-fab" id="round_btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><i class="material-icons">add</i>
						  </button>
						  <ul class="dropdown-menu dropdown-menu-right">
							<li><a href="#" class="btn btn-danger btn-fab del_anchor" id="round_btn" data-toggle="tooltip" data-placement="top" title="Add Machine"><i class="material-icons">note_add</i></a></li>
							<li><a href="#" class="btn btn-danger btn-fab del_anchor" id="round_btn" data-toggle="tooltip" data-placement="top" title="Edit Machine"><i class="material-icons">mode_edit</i></a></li> 
							<li><a href="#" class="btn btn-danger btn-fab del_anchor" id="round_btn" data-toggle="tooltip" data-placement="top" title="Delete Machine"><i class="material-icons">clear</i></a></li>
						  </ul>
						</div> 
				
				<div class="container">

					<table class="table">
						<thead class="thead-inverse">
							<tr>
								<th> </th>
								<th>Device Name</th>
							</tr>
						</thead>
						<tbody>
							<?php generateMachineTable() ?>
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
										<input type='submit' class="btn btn-primary" name='add-device' value='Submit' />
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane" id="resources" role="tabpanel">

				<div class="container hadoopIframe">
					<!-- replace the src to hadoop's web interface. current src is for testing only -->
					<iframe src="http://www.w3schools.com">iframes not supported?</iframe>
				</div>
				<!--floating action button-->
						 <div class="btn-group btn-group-lg dropup floating-action-button-custom" id="floatingbutton" valign="bottom">
						  <button type="button" class="btn btn-info btn-fab" id="round_btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><i class="material-icons">add</i>
						  </button>
						  <ul class="dropdown-menu dropdown-menu-right">
							<li><a href="#" class="btn btn-danger btn-fab del_anchor" id="round_btn" data-toggle="tooltip" data-placement="top" title="Change Permissions"><i class="material-icons">mode_edit</i></a></li> 

						  </ul>
						</div> 
			</div>
		</div>
		
		
		
		<!-- <div>
			Welcome to SIRIUS protection! :)  select a tab to begin <br> a project of names, pictures
		</div> -->
	</div> <!--container end-->
    <!-- jQuery first, then Bootstrap JS. -->
    <script src="https://code.jquery.com/jquery-2.2.4.js"></script>
	<script src="https://www.atlasestateagents.co.uk/javascript/tether.min.js"></script><!-- Tether for Bootstrap --> 
    <script src="js/bootstrap.min.js"></script>
	<script language="javascript" src="js/jquery.dimensions.js"></script>
	
	
	
	<!-- MODIFIED HERE -->
	<script language="javascript">
	
	var checkExist = setInterval(function() {
	   if ($('.floating-action-button-custom').length) {
		  console.log("Exists!");
		  var name = ".floating-action-button-custom";
		var menuYloc = null;
		console.log ("executed");
			pos=$(name).position();
			temp=$(name).css("top", pos.top + "px");
			console.log($(name).position());
			menuYloc = parseInt($(name).css("top").substring(0,$(name).css("top").indexOf("px")))
			console.log(menuYloc);
			$(window).scroll(function () { 
				offset = menuYloc+$(document).scrollTop()+"px";
				$(name).animate({top:offset},{duration:500,queue:false});
			});
		  clearInterval(checkExist);
	   }
	}, 100);
	
	//waitUntilExists(".floating-action-button-custom",function(){
	//	var name = ".floating-action-button-custom";
	//	var menuYloc = null;
	//	console.log ("executed");
	//		pos=$(name).position();
	//		temp=$(name).css("top", pos.top + "px");
	//		console.log($(name).position());
	//		menuYloc = parseInt($(name).css("top").substring(0,$(name).css("top").indexOf("px")))
	//		console.log(menuYloc);
	//		$(window).scroll(function () { 
	//			offset = menuYloc+$(document).scrollTop()+"px";
	//			$(name).animate({top:offset},{duration:500,queue:false});
	//		});
	//})
	
	
	 </script>
	 
	 <script>
	$(document).ready(function(){
		$('[data-toggle="tooltip"]').tooltip();   
	});
	</script> 
	<!--MODIFIED TILL HERE-->



  </body>
</html>
