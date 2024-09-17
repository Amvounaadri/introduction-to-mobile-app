<?php

class Tweet extends User {
    protected $message;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Method to fetch jobs from the database
    public function jobs($user_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM `jobs` 
                                      LEFT JOIN `users` ON `jobs`.`user_id` = `users`.`user_id` 
                                      ORDER BY `created_at` DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

	
	public function tweets($user_id, $num) {
		// Fetch tweets
		$stmt = $this->pdo->prepare("SELECT 'tweet' as type, tweetID as id, status, tweetBy, tweetImage as image, postedOn, retweetCount, likesCount 
										FROM `tweets` 
										WHERE `retweetID` = '0' 
										ORDER BY `postedOn` DESC 
										LIMIT :num");
		$stmt->bindParam(":num", $num, PDO::PARAM_INT);
		$stmt->execute();
		$tweets = $stmt->fetchAll(PDO::FETCH_OBJ);
	
		// Fetch jobs
		$stmt = $this->pdo->prepare("SELECT 'job' as type, job_id as id, job_title as title, job_description as description, 
										skills_required as skills, user_id, created_at as postedOn 
										FROM `jobs` 
										ORDER BY `created_at` DESC 
										LIMIT :num");
		$stmt->bindParam(":num", $num, PDO::PARAM_INT);
		$stmt->execute();
		$jobs = $stmt->fetchAll(PDO::FETCH_OBJ);
	
		// Merge tweets and jobs by posted time
		$posts = array_merge($tweets, $jobs);
	
		// Sort by posted time in descending order
		usort($posts, function($a, $b) {
			return strtotime($b->postedOn) - strtotime($a->postedOn);
		});
	
		// Display tweets and jobs alternately
		foreach ($posts as $post) {
			if ($post->type == 'tweet') {
				// Fetch additional tweet data
				$tweet = $post;
				$likes = $this->likes($user_id, $tweet->id);
				$retweet = $this->checkRetweet($tweet->id, $user_id);
				$user = $this->userData($tweet->tweetBy);
	
				echo '<div class="all-tweet">
						<div class="t-show-wrap">
							<div class="t-show-inner">
							'.((isset($retweet['retweetID']) ? $retweet['retweetID'] === $tweet->id || $tweet->retweetID > 0 : '') ? '
								<div class="t-show-banner">
									<div class="t-show-banner-inner">
										<span><i class="fa fa-retweet" aria-hidden="true"></i></span><span>'.$user->screenName.' Retweeted</span>
									</div>
								</div>' : '').'
								<div class="t-show-popup" data-tweet="'.$tweet->id.'">
									<div class="t-show-head">
										<div class="t-show-img">
											<a href="viewuserdetail.php?username='.$user->username.'">
												<img src="'.BASE_URL.$user->profileImage.'"/>
											</a>
										</div>
										<div class="t-s-head-content">
											<div class="t-h-c-name">
												<span><a href="viewuserdetail.php?username='.$user->username.'">'.$user->screenName.'</a></span>
												<span>@'.$user->username.'</span>
												<span>'.$this->timeAgo($tweet->postedOn).'</span>
											</div>
											<div class="t-h-c-dis">
												'.$this->getTweetLinks($tweet->status).'
											</div>
										</div>
									</div>'.
									((!empty($tweet->image)) ?
										'<div class="t-show-body">
										  <div class="t-s-b-inner">
											<div class="t-s-b-inner-in">
											  <img src="'.BASE_URL.$tweet->image.'" class="imagePopup" data-tweet="'.$tweet->id.'"/>
											</div>
										  </div>
										</div>' : '').'
								</div>                    
							</div>
						</div>
					</div>';
			} else {
				// Display job
				$job = $post;
				$jobUser = $this->userData($job->user_id); // Get user details for the job poster
	
				echo '<div class="job-post">
						<div class="job-post-header">
							<div class="job-post-img">
								<a href="viewuserdetail.php?username='.$jobUser->username.'">
									<img src="'.BASE_URL.$jobUser->profileImage.'" />
								</a>
							</div>
							<div class="t-h-c-name">
								<span><a href="viewuserdetail.php?username='.$jobUser->username.'">'.$jobUser->screenName.'</a></span>
								<span>@'.$jobUser->username.'</span>
								<span>'.$this->timeAgo($job->postedOn).'</span>
							</div>
						</div>
						<div class="job-post-body">
							<h3>'.htmlspecialchars($job->title).'</h3>
							<p>'.htmlspecialchars($job->description).'</p>
							<p><strong>Skills Required:</strong> '.htmlspecialchars($job->skills).'</p>
						</div>';
	
				// Check if the user has already applied for the job
				$applicationExists = $this->checkApplicationExists($job->id, $user_id);
				if ($applicationExists) {
					// If already applied, show a disabled button
					echo '<button class="apply-btn applied" disabled>Applied</button>';
				} else {
					// If not applied, show the apply button
					echo '<button class="apply-btn" data-job="'.$job->id.'" data-user="'.$jobUser->user_id.'">Apply</button>';
				}
	
				echo '</div>';
			}
		}
	}
	
	
	
  		// Method to apply for a job
		public function applyForJob($job_id, $graduate_id, $company_id) {
			$stmt = $this->pdo->prepare("INSERT INTO applications (job_id, graduate_id, company_id, applied_at) 
										 VALUES (:job_id, :graduate_id, :company_id, NOW())");
			$stmt->bindParam(":job_id", $job_id, PDO::PARAM_INT);
			$stmt->bindParam(":graduate_id", $graduate_id, PDO::PARAM_INT);
			$stmt->bindParam(":company_id", $company_id, PDO::PARAM_INT);
			$stmt->execute();
		}		
		public function checkApplicationExists($job_id, $graduate_id) {
			$stmt = $this->pdo->prepare("SELECT * FROM applications WHERE job_id = :job_id AND graduate_id = :graduate_id");
			$stmt->bindParam(':job_id', $job_id, PDO::PARAM_INT);
			$stmt->bindParam(':graduate_id', $graduate_id, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->rowCount() > 0; // Returns true if an application exists
		}
		
	
	public function getUserTweets($user_id){
		$stmt = $this->pdo->prepare("SELECT * FROM `tweets` LEFT JOIN `users` ON `tweetBy` = `user_id` WHERE `tweetBy` = :user_id AND `retweetID` = '0' OR `retweetBy` = :user_id ORDER BY `tweetID` DESC");
		$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}

	public function displayPostedJobs($user_id) {
        // SQL query to fetch job posts from the database
        $stmt = $this->pdo->prepare("SELECT * FROM jobs WHERE user_id = :user_id ORDER BY created_at DESC");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $jobs = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Loop through each job and display it
        foreach ($jobs as $job) {
            echo '<div class="job-post">';
            echo '<h3>' . htmlspecialchars($job->job_title) . '</h3>';
            echo '<p>' . htmlspecialchars($job->job_description) . '</p>';
            echo '<p><strong>Skills Required:</strong> ' . htmlspecialchars($job->skills_required) . '</p>';
            echo '<button class="apply-btn" onclick="applyJob(' . $job->job_id . ')">Apply</button>';
            echo '</div>';
        }
    }

	    // Method to get job details by job ID
		public function getJobDetails($job_id) {
			$stmt = $this->pdo->prepare("SELECT jobs.*, users.screenName FROM jobs 
										 JOIN users ON jobs.user_id = users.user_id 
										 WHERE job_id = :job_id");
			$stmt->bindParam(":job_id", $job_id, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_OBJ);
		}
	
		public function sendNotification($company_id, $message, $job_id) {
			$stmt = $this->pdo->prepare("INSERT INTO notifications (user_id, message, job_id, created_at) 
										 VALUES (:company_id, :message, :job_id, NOW())");
			$stmt->bindParam(":company_id", $company_id, PDO::PARAM_INT);
			$stmt->bindParam(":message", $message, PDO::PARAM_STR);
			$stmt->bindParam(":job_id", $job_id, PDO::PARAM_INT);
			$stmt->execute();
		}
		

		// Method to get notifications for a company
		public function getNotifications($company_id) {
			$stmt = $this->pdo->prepare("SELECT applications.*, users.screenName, jobs.job_title FROM applications 
										 JOIN users ON applications.graduate_id = users.user_id 
										 JOIN jobs ON applications.job_id = jobs.job_id 
										 WHERE applications.company_id = :company_id");
			$stmt->bindParam(":company_id", $company_id, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_OBJ);
		}

	public function addLike($user_id, $tweet_id, $get_id){
		$stmt = $this->pdo->prepare("UPDATE `tweets` SET `likesCount` = `likesCount`+1 WHERE `tweetID` = :tweet_id");
		$stmt->bindParam(":tweet_id", $tweet_id, PDO::PARAM_INT);
		$stmt->execute();

		$this->create('likes', array('likeBy' => $user_id, 'likeOn' => $tweet_id));
	
		if($get_id != $user_id){
			$this->message->sendNotification($get_id, $user_id, $tweet_id, 'like');
		}
	}

	public function unLike($user_id, $tweet_id, $get_id){
		$stmt = $this->pdo->prepare("UPDATE `tweets` SET `likesCount` = `likesCount`-1 WHERE `tweetID` = :tweet_id");
		$stmt->bindParam(":tweet_id", $tweet_id, PDO::PARAM_INT);
		$stmt->execute();

		$stmt = $this->pdo->prepare("DELETE FROM `likes` WHERE `likeBy` = :user_id and `likeOn` = :tweet_id");
		$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$stmt->bindParam(":tweet_id", $tweet_id, PDO::PARAM_INT);
		$stmt->execute(); 
	}

	public function likes($user_id, $tweet_id){
		$stmt = $this->pdo->prepare("SELECT * FROM `likes` WHERE `likeBy` = :user_id AND `likeOn` = :tweet_id");
		$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$stmt->bindParam(":tweet_id", $tweet_id, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}
	
	public function getTrendByHash($hashtag){
		$stmt = $this->pdo->prepare("SELECT * FROM `trends` WHERE `hashtag` LIKE :hashtag LIMIT 5");
		$stmt->bindValue(":hashtag", $hashtag.'%');
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}

	public function getMension($mension){
		$stmt = $this->pdo->prepare("SELECT `user_id`,`username`,`screenName`,`profileImage` FROM `users` WHERE `username` LIKE :mension OR `screenName` LIKE :mension LIMIT 5");
		$stmt->bindValue("mension", $mension.'%');
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);

	}

	public function addTrend($hashtag){
		preg_match_all("/#+([a-zA-Z0-9_]+)/i", $hashtag, $matches);
		if($matches){
			$result = array_values($matches[1]);
		}
		$sql = "INSERT INTO `trends` (`hashtag`, `createdOn`) VALUES (:hashtag, CURRENT_TIMESTAMP)";
		foreach ($result as $trend) {
			if($stmt = $this->pdo->prepare($sql)){
				$stmt->execute(array(':hashtag' => $trend));
			}
		}
	}

	public function addMention($status,$user_id, $tweet_id){
		if(preg_match_all("/@+([a-zA-Z0-9_]+)/i", $status, $matches)){
			if($matches){
				$result = array_values($matches[1]);
			}
			$sql = "SELECT * FROM `users` WHERE `username` = :mention";
			foreach ($result as $trend) {
				if($stmt = $this->pdo->prepare($sql)){
					$stmt->execute(array(':mention' => $trend));
					$data = $stmt->fetch(PDO::FETCH_OBJ);
				}
			}

			if($data->user_id != $user_id){
				$this->message->sendNotification($data->user_id, $user_id, $tweet_id, 'mention');
			}
		}
	}

	public function getTweetLinks($tweet){
		$tweet = preg_replace("/(https?:\/\/)([\w]+.)([\w\.]+)/", "<a href='$0' target='_blink'>$0</a>", $tweet);
        
        //$tweet = preg_replace("/#([\w]+)/", "<a href='http://localhost/GradPortal/hashtag/$1'>$0</a>", $tweet);		
        
		$tweet = preg_replace("/#([\w]+)/", "<a href='http://localhost/GradPortal/$1'>$0</a>", $tweet);	
        
		$tweet = preg_replace("/@([\w]+)/", "<a href='http://localhost/GradPortal/$1'>$0</a>", $tweet);
		return $tweet;		
	}

	public function getPopupTweet($tweet_id){
		$stmt = $this->pdo->prepare("SELECT * FROM `tweets`,`users` WHERE `tweetID` = :tweet_id AND `tweetBy` = `user_id`");
		$stmt->bindParam(":tweet_id", $tweet_id, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_OBJ);
	}

	public function retweet($tweet_id, $user_id, $get_id, $comment){
		$stmt = $this->pdo->prepare("UPDATE `tweets` SET `retweetCount` = `retweetCount`+1 WHERE `tweetID` = :tweet_id AND `tweetBy` = :get_id");
		$stmt->bindParam(":tweet_id", $tweet_id, PDO::PARAM_INT);
		$stmt->bindParam(":get_id", $get_id, PDO::PARAM_INT);
		$stmt->execute();

		$stmt = $this->pdo->prepare("INSERT INTO `tweets` (`status`,`tweetBy`,`retweetID`,`retweetBy`,`tweetImage`,`postedOn`,`likesCount`,`retweetCount`,`retweetMsg`) SELECT `status`,`tweetBy`,`tweetID`,:user_id,`tweetImage`,`postedOn`,`likesCount`,`retweetCount`,:retweetMsg FROM `tweets` WHERE `tweetID` = :tweet_id");
		$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$stmt->bindParam(":retweetMsg", $comment, PDO::PARAM_STR);
		$stmt->bindParam(":tweet_id", $tweet_id, PDO::PARAM_INT);
		$stmt->execute();

		$this->message->sendNotification($get_id, $user_id, $tweet_id, 'retweet');

 	}

	public function checkRetweet($tweet_id, $user_id){
		$stmt = $this->pdo->prepare("SELECT * FROM `tweets` WHERE `retweetID` = :tweet_id AND `retweetBy` = :user_id or `tweetID` = :tweet_id and `retweetBy` = :user_id");
		$stmt->bindParam(":tweet_id", $tweet_id, PDO::PARAM_INT);
		$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	public function tweetPopup($tweet_id){
		$stmt = $this->pdo->prepare("SELECT * FROM `tweets`,`users` WHERE `tweetID` = :tweet_id and `user_id` = `tweetBy`");
		$stmt->bindParam(":tweet_id", $tweet_id, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_OBJ);
	}

	public function comments($tweet_id){
		$stmt = $this->pdo->prepare("SELECT * FROM `comments` LEFT JOIN `users` ON `commentBy` = `user_id` WHERE `commentOn` = :tweet_id");
		$stmt->bindParam(":tweet_id", $tweet_id, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}

	public function countTweets($user_id){
		$stmt = $this->pdo->prepare("SELECT COUNT(`tweetID`) AS `totalTweets` FROM `tweets` WHERE `tweetBy` = :user_id AND `retweetID` = '0' OR `retweetBy` = :user_id");
		$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$stmt->execute();
		$count = $stmt->fetch(PDO::FETCH_OBJ);
		echo $count->totalTweets;
	}

	public function countLikes($user_id){
		$stmt = $this->pdo->prepare("SELECT COUNT(`likeID`) AS `totalLikes` FROM `likes` WHERE `likeBy` = :user_id");
		$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$stmt->execute();
		$count = $stmt->fetch(PDO::FETCH_OBJ);
		echo $count->totalLikes;
	} 

	public function trends(){
		$stmt = $this->pdo->prepare("SELECT *, COUNT(`tweetID`) AS `tweetsCount` FROM `trends` INNER JOIN `tweets` ON `status` LIKE CONCAT('%#',`hashtag`,'%') OR `retweetMsg` LIKE CONCAT('%#',`hashtag`,'%') GROUP BY `hashtag` ORDER BY `tweetID` LIMIT 2");
		$stmt->execute();	
		$trends = $stmt->fetchAll(PDO::FETCH_OBJ);
		echo '<div class="trends_container"><div class="trends_box"><div class="trends_header"><p>Trends for you</p></div><!-- trend title end-->';
		foreach ($trends as $trend) {
			echo '<div class="trends_body">
					<div class="trend">
                    <span>Trending</span>
						<p>
							<a style="color: #000;">#'.$trend->hashtag.'</a>
						</p>
						<div class="trend-tweets">
							
						</div>
					</div>
                </div>
                <div>
				</div>';
		}
		echo '<div class="trends_show-more">
                    <a href="">Show more</a>
                </div></div></div>';		
	} 

	public function getTweetsByHash($hashtag){
		$stmt = $this->pdo->prepare("SELECT * FROM `tweets` LEFT JOIN `users` ON `tweetBy` = `user_id` WHERE `status` LIKE :hashtag OR `retweetMsg` LIKE :hashtag");
		$stmt->bindValue(":hashtag", '%#'.$hashtag.'%', PDO::PARAM_STR);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}

	public function getUsersByHash($hashtag){
		$stmt = $this->pdo->prepare("SELECT DISTINCT * FROM `tweets` INNER JOIN `users` ON `tweetBy` = `user_id` WHERE `status` LIKE :hashtag OR `retweetMsg` LIKE :hashtag GROUP BY `user_id`");
		$stmt->bindValue(":hashtag", '%#'.$hashtag.'%', PDO::PARAM_STR);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}
}
?>
