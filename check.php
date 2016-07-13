<?PHP
		$username = trim($_POST['username']);
                $password = trim($_POST['password']);
                if(!isset($_SESSION)){ session_start(); }
                $_SESSION[$this->GetLoginSessionVar()] = $username;
                
                $uspw = $password." ".$username;
                $pythcommand = "/path/to/python /path/to/pyfile -p"." ".$uspw;
                $pythlogin = exec($pythcommand);
                
                if($pythlogin == "This is right user"){
                        header("Location: initer.php");
                }
                
                return false;
?>
