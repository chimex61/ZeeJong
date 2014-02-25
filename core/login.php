<?php
/*
Login file allowing users to log in
*/
require_once(dirname(__FILE__) . '/config.php');	// Require config file containing configuration of the database
require_once(dirname(__FILE__) . '/database.php');	// Require the database file
require_once(dirname(__FILE__) . '/classes/User.php');	// We need the user class file
require_once(dirname(__FILE__) . '/functions.php');




class Login {


	public $loggedIn;
	public $loginMessage;
	public $user;


	public function __construct() {
	
		$this->checkLogin();
	
	}


	/**
	Try to login with the given username and password
	
	@param username
	@param password
	
	@return true (login succeded) / false (wrong login details)
	*/
	private function login($username, $password) {
		$d = new Database;
		if($d->doesUsernameExist($username)){
			$user = $d->getUser($username);
			if(hashPassword($password,$user->getSalt()) == $user->getHash()){
				session_regenerate_id();
				$_SESSION['userID'] = $user->getID();
				session_write_close();
				return true;
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}
	
	
	

	private function checkLogin() {
	
		global $database;
	
		//Check if login page
		if(defined('PAGE') && PAGE == 'login') {
				
			//Check for active session
			if( isset($_SESSION['userID']) and $database->doesUserExist($_SESSION['userID'])) {
				$this->loginMessage = 'You are already logged in';
				$this->loggedIn = true;
				$this->user = new User($_SESSION['userID']);
			}
			elseif(!isset($_POST['username']) or !isset($_POST['password'])) {
				$this->loginMessage = 'Please provide username and password';
				$this->loggedIn = false;
			}
			else {
			
				$username = htmlspecialchars($_POST['username']);
				$password = htmlspecialchars($_POST['password']); 
				
				if($this->login($username,$password)) {
					$this->loginMessage = "Hi, $username!";
					$this->loggedIn = true;
					$this->user = $database->getUser($username);
				}
				else {
					$this->loginMessage = 'Wrong username or password';
					$this->loggedIn = false;
				}
				
			}
				
			
		}
		else {
		
			//Check for active session
			if( isset($_SESSION['userID']) and $database->doesUserExist($_SESSION['userID'])) {
				$this->loggedIn = true;
				$this->user = new User($_SESSION['userID']);
			}
			else {
				$this->loggedIn = false;
			}
		}
	
		
	
	}
	


}


?>