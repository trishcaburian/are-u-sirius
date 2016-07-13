<?PHP

class SiriusSamba{
	var $username;
	var $pass;

	function GetSelf(){
		return htmlentities($_SERVER['PHP_SELF']);
	}    
	
	function EmptyDisp($value_name)
    {
        if(empty($_POST[$value_name]))
        {
            return'';
        }
        return htmlentities($_POST[$value_name]);
    }
	
	function Login(){
		$username = trim($_POST['username']);
        $password = trim($_POST['password']);
		if(!isset($_SESSION)){ session_start(); }
		$_SESSION[$this->GetLoginSessionVar()] = $username;
		
		$uspw = $password." ".$username;
		$pythcommand = "/path/to/python /path/to/pyfile -p"." ".$uspw;
		$pythlogin = exec($pythcommand);
		
		if($pythlogin == "This is right user"){
			return true;
		}
		
		return false;
	}
	
	function RedirectToURL($url)
	{
		header("Location: $url");
		exit;
	}
	
	function LogOut()
    {
        session_start();
        
        $sessionvar = $this->GetLoginSessionVar();
        
        $_SESSION[$sessionvar]=NULL;
        
        unset($_SESSION[$sessionvar]);
    }
 
}
?>

<html>
 <head>
  <title>PHP Test</title>
 </head>
 <body>
 <?php echo $username; ?> 
 </body>
</html>