<?php 

	class Tweet extends User
	{
		
		function __construct($pdo)
		{
			$this->pdo = $pdo;
		}

		public function tweets($user_id)
		{
			$stmt = $this->pdo->prepare("SELECT * FROM tweets, users
										 WHERE tweetby = user_id");
			$stmt->execute();
			$tweets = $stmt->fetchAll(PDO::FETCH_OBJ);



			foreach ($tweets as $tweet) 
			{
				$likes = $this->likes($user_id, $tweet->tweetid);				

				echo'
					<div class="all-tweet">
						<div class="t-show-wrap">	
							<div class="t-show-inner">
								<!-- this div is for retweet icon 
								<div class="t-show-banner">
									<div class="t-show-banner-inner">
										<span><i class="fa fa-retweet" aria-hidden="true"></i></span>
										<span>Screen-Name Retweeted</span>
									</div>
								</div>-->
								<div class="t-show-popup">
									<div class="t-show-head">
										<div class="t-show-img">
											<img src="'.$tweet->profileimage.'"/>
										</div>
										<div class="t-s-head-content">
											<div class="t-h-c-name">
												<span><a href="'.$tweet->username.'">'.$tweet->screenname.'</a></span>
												<span>@'.$tweet->username.'</span>
												<span>'.$tweet->postedon.'</span>
											</div>
											<div class="t-h-c-dis">
												'.$this->getTweetLinks($tweet->status).'
											</div>
										</div>
									</div>

									';

									if (!empty($tweet->tweetimage)) 
									{
										echo' 
											<!--tweet show head end-->
											<div class="t-show-body">
											  <div class="t-s-b-inner">
											   <div class="t-s-b-inner-in">
											     <img src="'.$tweet->tweetimage.'" class="imagePopup"/>
											   </div>
											  </div>
											</div>
											<!--tweet show body end-->
										';
									}

									echo ' 
								</div>
								<div class="t-show-footer">
									<div class="t-s-f-right">
										<ul> 
											<li><button><a href="#"><i class="fa fa-share" aria-hidden="true"></i></a></button></li>	
											
											<li>
												<button class="retweet" data-tweet="'.$tweet->tweetid.'" data-user="'.$tweet->tweetby.'">
													<a href="#">
														<i class="fa fa-retweet" aria-hidden="true"></i>
													</a>
													<span class="retweetsCount"></span>
												</button>
											</li>
											
											<li>'.(($likes['likeon'] === $tweet->tweetid) ? 
												'<button class="unlike-btn" data-tweet="'.$tweet->tweetid.'" data-user="'.$tweet->tweetby.'">
												 	<a href="#">
												 		<i class="fa fa-heart" aria-hidden="true"></i>
												 	</a>
												 	<span class="likesCounter">'.$tweet->likescount.'</span>
												 </button>' : 
												'<button class="like-btn" data-tweet="'.$tweet->tweetid.'" data-user="'.$tweet->tweetby.'">
												 	<a href="#">
												 		<i class="fa fa-heart-o" aria-hidden="true"></i>
												 	</a>
												 	<span class="likesCounter">'.(($tweet->likescount > 0) ? $tweet->likescount : '').'</span>
												 </button>').'
											</li>
																						
											<li><a href="#" class="more"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></a>
												<ul> 
											  		<li><label class="deleteTweet">Delete Tweet</label></li>
												</ul>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
				';
			}	
		}

		public function getTrendByHash($hashtag)
		{
			$stmt = $this->pdo->prepare("SELECT * FROM trends
										 WHERE hashtag
										 LIKE :hashtag LIMIT 5");
			$stmt->bindValue(':hashtag', $hashtag.'%');
			$stmt->execute();

			return $stmt->fetchAll(PDO::FETCH_OBJ);
		}

		public function getMention($mention)
		{
			$stmt = $this->pdo->prepare("SELECT user_id, username, screenname, profileimage
										 FROM users
										 WHERE username
										 LIKE :mention OR screenname
										 LIKE :mention LIMIT 5");
			$stmt->bindValue(':mention', $mention.'%');
			$stmt->execute();

			return $stmt->fetchAll(PDO::FETCH_OBJ);
		}

		public function addTrend($hashtag)
		{
			preg_match_all("/#+([a-zA-Z0-9_]+)/i", $hashtag, $metches);

			if ($metches) 
			{
				$result = array_values($metches[1]);				
			}

			$sql = "INSERT INTO trends (hashtag, createdon)
					VALUES (:hashtag, CURRENT_TIMESTAMP)";
			foreach ($result as $trend) 
			{
				if ($stmt = $this->pdo->prepare($sql)) 
				{
					$stmt->execute(array(':hashtag' => $trend));
				}
			}
		}

		public function getTweetLinks($tweet)
		{
			$tweet = preg_replace("/(https?:\/\/)([\w]+.)([\w\.]+)/", "<a href='$0' target='_blink'>$0</a>", $tweet);
			$tweet = preg_replace("/#([\w]+)/", "<a href='".BASE_URL."hashtag/$1'>$0</a>", $tweet);
			$tweet = preg_replace("/@([\w]+)/", "<a href='".BASE_URL."hashtag/$1'>$0</a>", $tweet);

			return $tweet;
		}

		public function getPopupTweet($tweet_id, $user_id)
		{
			$stmt = $this->pdo->prepare("SELECT * FROM tweets, users
										 WHERE tweetid = :tweet_id
										 AND tweetby = :user_id"); 
			$stmt->bindParam(':tweet_id', $tweet_id, PDO::PARAM_INT);
			$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
			$stmt->execute();

			return $stmt->fetch(PDO::FETCH_OBJ);
		}

		public function retweet($tweet_id,$user_id,$get_id,$comment)
		{
			$stmt = $this->pdo->prepare('UPDATE tweets 
										 SET retweetcount = retweetcount + 1
										 WHERE tweetid = :tweet_id');
			$stmt->bindParam(':tweet_id', $tweet_id, PDO::PARAM_INT);
			$stmt->execute();

			$stmt = $this->pdo->prepare('INSERT INTO tweets (status, tweetby, tweetimage, retweetid, retweetby, postedon, likescount, retweetcount, retweetmsg)
										 SELECT status, tweetby, tweetimage, tweetid, :user_id, CURRENT_TIMESTAMP, likescount, retweetcount,:retweetMsg
										 FROM tweets
										 WHERE tweetid = :tweet_id');
			$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
			$stmt->bindParam(':retweetMsg', $comment, PDO::PARAM_STR);
			$stmt->bindParam(':tweet_id', $tweet_id, PDO::PARAM_INT);
			$stmt->execute();

		}

		public function addLike($user_id, $tweet_id, $get_id)
		{
			$stmt = $this->pdo->prepare("UPDATE tweets 
										 SET likescount = likescount + 1
										 WHERE tweetid = :tweet_id");
			$stmt->bindParam(":tweet_id", $tweet_id, PDO::PARAM_INT );
			$stmt->execute();

			$this->create('likes', array('likeby' => $user_id, 'likeon' => $tweet_id));
		}

		public function unlike($user_id, $tweet_id, $get_id)
		{
			$stmt = $this->pdo->prepare("UPDATE tweets  
										 SET likescount = likescount - 1
										 WHERE tweetid = :tweet_id");
			$stmt->bindParam(":tweet_id", $tweet_id, PDO::PARAM_INT );
			$stmt->execute();

			$stmt = $this->pdo->prepare("DELETE FROM likes
										 WHERE likeby = :user_id
										 AND likeon = :tweet_id");
			$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
			$stmt->bindParam(":tweet_id", $tweet_id, PDO::PARAM_INT);
			$stmt->execute();

		}

		public function likes($user_id, $tweet_id)
		{
			$stmt = $this->pdo->prepare("SELECT * FROM likes
										 WHERE likeby = :user_id
										 AND likeon = :tweet_id");
			$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
			$stmt->bindParam(":tweet_id", $tweet_id, PDO::PARAM_INT);
			$stmt->execute();

			return $stmt->fetch(PDO::FETCH_ASSOC);
		}
	}
?>