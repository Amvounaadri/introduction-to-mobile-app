<?php 

class User {
    
    protected $pdo;

    public function __construct($pdo) {                                                
        $this->pdo = $pdo;
    }

	///-------------------------//
	public function uploadCV($user_id, $file){
		$cvDir = 'CVs/';
		if (!is_dir($cvDir)) {
			mkdir($cvDir, 0777, true);
			error_log("CV directory created.");
		}
	
		$fileTempName = $file['tmp_name'];
		$fileName = $file['name'];
		$fileError = $file['error'];
	
		// Check for upload errors
		if($fileError === 0) {
			$fileNewName = uniqid('', true) . "." . pathinfo($fileName, PATHINFO_EXTENSION);
			$fileDestination = $cvDir . $fileNewName;
	
			if(move_uploaded_file($fileTempName, $fileDestination)){
				error_log("File uploaded successfully to $fileDestination.");
	
				$stmt = $this->pdo->prepare("UPDATE users SET cv = :cv WHERE user_id = :user_id");
				$stmt->bindParam(":cv", $fileDestination, PDO::PARAM_STR);
				$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
				$stmt->execute();
	
				return true;
			} else {
				error_log("File could not be moved to $fileDestination.");
			}
		} else {
			error_log("Error uploading file. Error code: $fileError");
		}
		return false;
	}
	
			/////////----------------------------------/////////
			public function updateUserInfo($user_id, $data, $cvFile = null) {
				// Process profile info (name, bio, website, country)
				$columns = '';
				$i = 1;
				
				foreach($data as $name => $value) {
					$columns .= "{$name} = :{$name}";
					if($i < count($data)) {
						$columns .= ', ';
					}
					$i++;
				}
			
				$sql = "UPDATE users SET {$columns} WHERE user_id = :user_id";
				$stmt = $this->pdo->prepare($sql);
			
				foreach($data as $name => $value) {
					$stmt->bindValue(":{$name}", $value);
				}
			
				$stmt->bindValue(":user_id", $user_id);
				$stmt->execute();
			
				// Handle CV upload if provided
				if($cvFile) {
					$this->uploadCV($user_id, $cvFile);
				}
			}
			

	// Assuming you have a database connection set up
		public function getAllUsers() {
			$stmt = $this->pdo->prepare("SELECT * FROM users");
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_OBJ);
		}

	///--------------------/

    public function getAppliedJobs($user_id) {
        $stmt = $this->pdo->prepare("SELECT jobs.job_title, applications.applied_at 
            FROM applications 
            JOIN jobs ON applications.job_id = jobs.job_id 
            WHERE applications.graduate_id = :user_id");
        
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Return associative array
    }

    public function getApplications($user_id) {
        $stmt = $this->pdo->prepare("SELECT applications.application_id, users.screenName AS graduateName, jobs.job_title 
            FROM applications 
            JOIN users ON applications.graduate_id = users.user_id 
            JOIN jobs ON applications.job_id = jobs.job_id 
            WHERE applications.company_id = :user_id");
        
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Return associative array
    }
	
    // New method to check if the user has already applied for a specific job
    public function hasApplied($user_id, $job_id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM applications WHERE graduate_id = :user_id AND job_id = :job_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':job_id', $job_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0; // Returns true if count is greater than 0
    }
	public function checkInput($data){
		$data = htmlspecialchars($data);
		$data = trim($data);
		$data = stripcslashes($data);
		return $data;
	}
	
	public function preventAccess($request, $currentFile, $currently){
		if($request == 'GET' && $currentFile == $currently){
			header('Location:'.BASE_URL.'index.php');
		}
	}
	
	public function search($search){
		$stmt = $this->pdo->prepare("SELECT `user_id`,`username`,`screenName`,`profileImage`,`profileCover` FROM `users` WHERE `username` LIKE ? OR `screenName` LIKE ?");
		$stmt->bindValue(1, $search.'%', PDO::PARAM_STR);
		$stmt->bindValue(2, $search.'%', PDO::PARAM_STR);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}

	public function login($email, $password){
		$passwordHash = md5($password);
		$stmt = $this->pdo->prepare('SELECT `user_id` FROM `users` WHERE `email` = :email AND `password` = :password');
		$stmt->bindParam(':email', $email, PDO::PARAM_STR);
		$stmt->bindParam(':password', $passwordHash, PDO::PARAM_STR);
		$stmt->execute();

		$count = $stmt->rowCount();
		$user = $stmt->fetch(PDO::FETCH_OBJ);

		if($count > 0){
			$_SESSION['user_id'] = $user->user_id;
			header('Location: home.php');
		}else{
			return false;
		}
	}


	  public function register($email, $password, $screenName){
	    $passwordHash = md5($password);
	    $stmt = $this->pdo->prepare("INSERT INTO `users` (`email`, `password`, `screenName`, `profileImage`, `profileCover`) VALUES (:email, :password, :screenName, 'assets/images/defaultprofileimage.png', 'assets/images/defaultCoverImage.png')");
	    $stmt->bindParam(":email", $email, PDO::PARAM_STR);
 	    $stmt->bindParam(":password", $passwordHash , PDO::PARAM_STR);
	    $stmt->bindParam(":screenName", $screenName, PDO::PARAM_STR);
	    $stmt->execute();

	    $user_id = $this->pdo->lastInsertId();
	    $_SESSION['user_id'] = $user_id;
	  }


	public function userData($user_id){
		$stmt = $this->pdo->prepare('SELECT * FROM `users` WHERE `user_id` = :user_id');
		$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetch(PDO::FETCH_OBJ);
	}

	public function logout(){
		$_SESSION = array();
		session_destroy();
		header('Location: ../index.php');
	}

	public function create($table, $fields = array()){
		$columns = implode(',', array_keys($fields));
		$values  = ':'.implode(', :', array_keys($fields));
		$sql     = "INSERT INTO {$table} ({$columns}) VALUES ({$values})";

		if($stmt = $this->pdo->prepare($sql)){
			foreach ($fields as $key => $data) {
				$stmt->bindValue(':'.$key, $data);
			}
			$stmt->execute();
			return $this->pdo->lastInsertId();
		}
	}

	public function update($table, $user_id, $fields = array()){
		$columns = '';
		$i       = 1;
	
		foreach ($fields as $name => $value) {
			$columns .= "`{$name}` = :{$name} ";
			if($i < count($fields)){
				$columns .= ', ';
			}
			$i++;
		}
		$sql = "UPDATE {$table} SET {$columns} WHERE `user_id` = {$user_id}";
		if($stmt = $this->pdo->prepare($sql)){
			foreach ($fields as $key => $value) {
				$stmt->bindValue(':'.$key, $value);
			}
			$stmt->execute();
		}
	}
	

	public function delete($table, $array){
		$sql   = "DELETE FROM " . $table;
		$where = " WHERE ";

		foreach($array as $key => $value){
			$sql .= $where . $key . " = '" . $value . "'";
			$where = " AND ";
		}
		$sql .= ";";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
	}

	public function checkUsername($username){
		$stmt = $this->pdo->prepare("SELECT `username` FROM `users` WHERE `username` = :username");
		$stmt->bindParam(':username', $username, PDO::PARAM_STR);
		$stmt->execute();

		$count = $stmt->rowCount();
		if($count > 0){
			return true;
		}else{
			return false;
		}
	}
	

	public function checkPassword($password){
		$stmt = $this->pdo->prepare("SELECT `password` FROM `users` WHERE `password` = :password");
        $md5 = md5($password);
		$stmt->bindParam(':password', $md5, PDO::PARAM_STR);
		$stmt->execute();

		$count = $stmt->rowCount();
		if($count > 0){
			return true;
		}else{
			return false;
		}
	}

	public function checkEmail($email){
		$stmt = $this->pdo->prepare("SELECT `email` FROM `users` WHERE `email` = :email");
		$stmt->bindParam(':email', $email, PDO::PARAM_STR);
		$stmt->execute();

		$count = $stmt->rowCount();
		if($count > 0){
			return true;
		}else{
			return false;
		}
	}	

	public function loggedIn(){
		return (isset($_SESSION['user_id'])) ? true : false;
	}

	public function userIdbyUsername($username){
		$stmt = $this->pdo->prepare("SELECT `user_id` FROM `users` WHERE (`username`  = :username)");
		$stmt->bindParam("username", $username, PDO::PARAM_STR);
		$stmt->execute();
	    $user = $stmt->fetch(PDO::FETCH_OBJ);
	    return $user->user_id;
	}


	//i--------------------------------------//
	public function uploadImage($file, $path){
		$filename = $file['name'];
		$fileTmp  = $file['tmp_name'];
		$fileSize = $file['size'];
		$errors   = $file['error'];
	
		$ext = explode('.', $filename);
		$ext = strtolower(end($ext));
		
		$allowed_extensions  = array('jpg', 'png', 'jpeg');
	
		if (in_array($ext, $allowed_extensions)) {
			if ($errors === 0) {
				if ($fileSize <= 2097152) { // 2MB
					$root = $path . '/' . $filename;
					$destination = $_SERVER['DOCUMENT_ROOT'] . '/GradPortal/' . $root; // Correct path
					move_uploaded_file($fileTmp, $destination);
					return $root;
				} else {
					$GLOBALS['imgError'] = "File size is too large";
				}
			}
		} else {
			$GLOBALS['imgError'] = "Only allowed JPG, PNG, JPEG extensions";
		}
	}
	


    public function updateProfileImage($user_id, $file){
        $path = 'users';
        $fileRoot = $this->uploadImage($file, $path);
        if ($fileRoot) {
            $this->update('users', $user_id, array('profileImage' => $fileRoot));
        }
    }

    public function updateProfileCover($user_id, $file){
        $path = 'users';
        $fileRoot = $this->uploadImage($file, $path);
        if ($fileRoot) {
            $this->update('users', $user_id, array('profileCover' => $fileRoot));
        }
    }

	//////-------------social module------------/////
	public function sendFriendRequest($user_id, $friend_id) {
		$stmt = $this->pdo->prepare("INSERT INTO friendships (user_id, friend_id, status) VALUES (:user_id, :friend_id, 'pending')");
		$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->bindParam(':friend_id', $friend_id, PDO::PARAM_INT);
		return $stmt->execute(); // Return the result of execute()
	}

	public function checkFriendshipStatus($user_id, $friend_id) {
		$stmt = $this->pdo->prepare("SELECT status FROM friendships WHERE (user_id = :user_id AND friend_id = :friend_id) OR (user_id = :friend_id AND friend_id = :user_id)");
		$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->bindParam(':friend_id', $friend_id, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchColumn(); // Fetch the status ('pending', 'accepted', 'rejected')
	}
	public function getFriends($user_id) {
		$stmt = $this->pdo->prepare("
			SELECT u.user_id, u.username, u.screenName, u.profileImage 
			FROM friendships f
			JOIN users u ON (f.user_id = u.user_id OR f.friend_id = u.user_id)
			WHERE (f.user_id = :user_id OR f.friend_id = :user_id)
			AND f.status = 'accepted'
			AND u.user_id != :user_id
		");
		$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}
	

    public function acceptFriendRequest($friendship_id) {
        $stmt = $this->pdo->prepare("UPDATE friendships SET status = 'accepted' WHERE id = :friendship_id");
        $stmt->bindParam(':friendship_id', $friendship_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function rejectFriendRequest($friendship_id) {
        $stmt = $this->pdo->prepare("UPDATE friendships SET status = 'rejected' WHERE id = :friendship_id");
        $stmt->bindParam(':friendship_id', $friendship_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function sendMessage($sender_id, $receiver_id, $message) {
        $stmt = $this->pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (:sender_id, :receiver_id, :message)");
        $stmt->bindParam(':sender_id', $sender_id, PDO::PARAM_INT);
        $stmt->bindParam(':receiver_id', $receiver_id, PDO::PARAM_INT);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function getFriendRequests($user_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users 
                                     JOIN friendships ON users.user_id = friendships.user_id 
                                     WHERE friendships.friend_id = :user_id AND friendships.status = 'pending'");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }	

	public function timeAgo($datetime){
		$time    = strtotime($datetime);
 		$current = time();
 		$seconds = $current - $time;
 		$minutes = round($seconds / 60);
		$hours   = round($seconds / 3600);
		$months  = round($seconds / 2600640);

		if($seconds <= 60){
			if($seconds == 0){
				return 'now';
			}else{
				return $seconds.'s';
			}
		}else if($minutes <= 60){

			return $minutes.'m';

		}else if($hours <= 24){

			return $hours.'h';

		}else if($months <= 12){

			return date('M j', $time);

		}else{
			return date('j M Y', $time);
		}
	}
     
}
?>