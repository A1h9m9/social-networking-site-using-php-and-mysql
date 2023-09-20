<?php
session_start();
if (isset($_SESSION['username'])) {
	include 'connect.php';
	$username1 = $_SESSION['username'];

	// select all users from database
	if (isset($_SESSION['username'])) {
		$username = $_SESSION['username'];
		$user_id = $_SESSION['user_id'];
		// قم بإعداد استعلام SQL للبحث عن القيمة في الجدول
		$sql = "SELECT * FROM friend_requests WHERE  receiver_id = :username OR sender_id = :user_id";

		// قم بتحضير الاستعلام وتنفيذه
		$stmt = $con->prepare($sql);
		$stmt->bindParam(':username', $username);
		$stmt->bindParam(':user_id', $user_id);
		$stmt->execute();

		// قم بفحص إذا كان هناك نتائج
		if ($stmt->rowCount() > 0) {
			$stmt = $con->prepare('SELECT users.user_id, users.username, posts.post_id, posts.post, posts.likes
	FROM users
	LEFT JOIN posts ON users.user_id = posts.id
	LEFT JOIN friend_requests AS sender_fr ON users.user_id = sender_fr.sender_id
	LEFT JOIN friend_requests AS receiver_fr ON users.username = receiver_fr.receiver_id
	WHERE sender_fr.status = 1 or receiver_fr.status = 1
	ORDER BY posts.post_id DESC');
			$stmt->execute();
			$row = $stmt->fetchAll();
		} else {
		}
	}
	$stmt = $con->prepare('SELECT users.user_id, users.username, image_page.file_type, image_page.file_data
    FROM users
    LEFT JOIN image_page ON users.user_id = image_page.id');
	$stmt->execute();
	$row123 = $stmt->fetch();
	//// Use prepared statement to prevent SQL injection  // Fetch user_id form database details
	$loggedInUsername = $_SESSION['username'];
	$stmt1 = $con->prepare('SELECT user_id FROM users WHERE username = ?');
	$stmt1->execute([$loggedInUsername]);
	$user = $stmt1->fetch();
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {

		$post = $_POST['post_id'];
		$user_id = $_SESSION['user_id'];
		// Check if the user has already liked the post
		$stmt4 = $con->prepare('SELECT * FROM like_post WHERE user_id = ? AND post_id = ?');
		$stmt4->execute([$user_id, $post]);
		if ($stmt4->rowCount() > 0) {
			// User has already liked the post, so remove the like (dislike)
			$stmt5 = $con->prepare('DELETE FROM like_post WHERE user_id = ? AND post_id = ?');
			$stmt5->execute([$user_id, $post]);
		} else {
			// Insert a new like
			$stmt6 = $con->prepare('INSERT INTO like_post(user_id ,post_id)VALUES(:user_id, :post_id)');
			$stmt6->bindParam(':user_id', $user_id, PDO::PARAM_INT);
			$stmt6->bindParam(':post_id', $post, PDO::PARAM_INT);
			$stmt6->execute();
			if ($stmt6->rowCount() > 0) {
				header("Location: home.php?id=" . $post);
				exit;
			}
		}
	}

	$stmt3 = $con->prepare("SELECT * FROM users WHERE user_id <> :session_user_id");
	$stmt3->bindParam(":session_user_id", $_SESSION['user_id'], PDO::PARAM_INT);
	$stmt3->execute();
	$row123 = $stmt3->fetchAll();

	$loggedInUserId = $_SESSION['username'];
	$stmt2 = $con->prepare('SELECT u.username, fr.status
    FROM friend_requests AS fr
    INNER JOIN users AS u ON fr.sender_id = u.user_id
    WHERE fr.receiver_id = ?');
	$stmt2->execute([$loggedInUserId]);
	$frient_row = $stmt2->fetchAll();

	$stmt2 = $con->prepare('SELECT * FROM posts');
    $stmt2->execute();
    $posts = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    if ($posts) {
        foreach ($posts as $post) {
            // Fetch comments for this post
            $post_id123 = $post['post_id'];
            $stmt = $con->prepare('SELECT *
            FROM comment  ');
            $stmt->execute();
            $row553 = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
	$loggedInUsername = $_SESSION['username'];
	$stmt1 = $con->prepare('SELECT user_id FROM users WHERE username = ?');
    $stmt1->execute([$loggedInUsername]);
    $user = $stmt1->fetch();
?>
	<?php
	include 'head.php';
	?>

	<body>
		
		<!--<div class="se-pre-con"></div>-->
		<div class="theme-layout">

			<?php


			include 'responsive_header.php';


			?>
        <div class="topbar stick">
            <div class="logo">
                <a title="" href="timeline.php">
                    <h3>Deer code</h3>
            </div>

            <div class="top-area">

                <ul class="setting-area">
                    <li>
                        <a href="#" title="Home" data-ripple=""><i class="ti-search"></i></a>
                        <div class="searched">
                            <form method="post" class="form-search">
                                <input type="text" placeholder="Search Friend">
                                <button data-ripple><i class="ti-search"></i></button>
                            </form>
                        </div>
                    </li>
                    <li><a href="newsfeed.html" title="Home" data-ripple=""><i class="ti-home"></i></a></li>

                    <li>
                        <a href="#" title="Notification" data-ripple="">
                            <i class="ti-bell"></i><span><?php
                                                            $stmt7 = $con->prepare('SELECT COUNT(post_id) FROM posts where id=?');
                                                            $stmt7->execute([$_SESSION['user_id']]);
                                                            echo $stmt7->fetchColumn();

                                                            ?></span>
                        </a>
                        <!-- notifications -->

                        <div class="dropdowns">
                            <span>2 New posts</span>
                            <?php
                            if (isset($row) && (is_array($row) || is_object($row))) {
                                foreach ($row as $rowws) {
                            ?>
                                    <ul class="drops-menu">
                                        <li>
                                            <a href="notifications.php">
                                                <img src="images/resources/thumb-1.jpg" alt="">
                                                <div class="mesg-meta">
                                                    <h6><?php echo $rowws['username'] ?></h6>
                                                    <span><?php echo $rowws['post'] ?></span>
                                                    <i>2 min ago</i>
                                                </div>
                                            </a>
                                            <span class="tag green">New</span>
                                        </li>
                                    </ul>
                            <?php
                                }
                            } else {
                                echo 'no posts yet';
                            }
                            ?>
                        </div>
                    <li>
                        <!-- friend request -->
                        <a href="#" title="Notification" data-ripple="">
                            <i class="ti-user"></i><span><?php
                                                            $receiverId =  $_SESSION['username'];

                                                            // Prepare a query to check if a friend request already exists
                                                            $stmt45 = $con->prepare('SELECT COUNT(friend_id) FROM friend_requests WHERE receiver_id = ?');
                                                            $stmt45->execute([$receiverId]);
                                                            echo $stmt45->fetchColumn()

                                                            ?></span>
                        </a>
                        <!-- notifications -->

                        <div class="dropdowns">
                            <span>2 New posts</span>
                            <?php
                            $stmt42 = $con->prepare('SELECT * FROM friend_requests WHERE status = 1');
                            $stmt42->execute();
                            if ($stmt42->rowCount() >  0) {
                            } else {
                                foreach ($frient_row as $frient_rows) {
                            ?>
                                    <ul class="drops-menu">
                                        <li>
                                            <img src="images/resources/thumb-1.jpg" alt="">
                                            <div class="mesg-meta">
                                                <h6><?php echo $frient_rows['username'] ?></h6>

                                            </div>
                                            <span class="tag green">New</span>
                                        </li>
                                    </ul>
                            <?php
                                }
                            }
                            ?>
                        </div>
                    </li>

                    <li>
                        <!-- Messages -->
                        <a href="#" title="Messages" data-ripple=""><i class="ti-comment"></i><span>12</span></a>
                        <div class="dropdowns">
                            <span>5 New Messages</span>
                            <ul class="drops-menu">
                                <li>
                                    <a href="notifications.html" title="">
                                        <img src="images/resources/thumb-1.jpg" alt="">
                                        <div class="mesg-meta">
                                            <h6>sarah Loren</h6>
                                            <span>Hi, how r u dear ...?</span>
                                            <i>2 min ago</i>
                                        </div>
                                    </a>
                                    <span class="tag green">New</span>
                                </li>


                            </ul>
                            <a href="messages.html" title="" class="more-mesg">view more</a>
                        </div>
                    </li>
                    <li><a href="#" title="Languages" data-ripple=""><i class="fa fa-globe"></i></a>
                        <div class="dropdowns languages">
                            <a href="#" title=""><i class="ti-check"></i>English</a>
                            <a href="#" title="">Arabic</a>
                            <a href="#" title="">Dutch</a>
                            <a href="#" title="">French</a>
                        </div>
                    </li>
                </ul>

                <span class="ti-menu main-menu" data-ripple=""></span>
            </div>
        </div>
			</div><!-- topbar -->

			<div class="fixed-sidebar left">
				<div class="menu-left">
					<ul class="left-menu">
						<li><a href="newsfeed.html" title="Newsfeed Page" data-toggle="tooltip" data-placement="right"><i class="ti-magnet"></i></a></li>
						<li><a href="fav-page.html" title="favourit page" data-toggle="tooltip" data-placement="right"><i class="fa fa-star-o"></i></a></li>
						<li><a href="insights.html" title="Account Stats" data-toggle="tooltip" data-placement="right"><i class="ti-stats-up"></i></a></li>
						<li><a href="inbox.html" title="inbox" data-toggle="tooltip" data-placement="right"><i class="ti-import"></i></a></li>
						<li><a href="messages.html" title="Messages" data-toggle="tooltip" data-placement="right"><i class="ti-comment-alt"></i></a></li>
						<li><a href="edit-account-setting.html" title="Setting" data-toggle="tooltip" data-placement="right"><i class="ti-panel"></i></a></li>
						<li><a href="faq.html" title="Faq's" data-toggle="tooltip" data-placement="right"><i class="ti-light-bulb"></i></a></li>
						<li><a href="timeline-friends.html" title="Friends" data-toggle="tooltip" data-placement="right"><i class="ti-themify-favicon"></i></a></li>
						<li><a href="widgets.html" title="Widgets" data-toggle="tooltip" data-placement="right"><i class="ti-eraser"></i></a></li>
						<li><a href="notifications.html" title="Notification" data-toggle="tooltip" data-placement="right"><i class="ti-bookmark-alt"></i></a></li>
					</ul>
				</div>
			</div><!-- left sidebar menu -->
			<section>
				<div class="gap2 gray-bg">
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-12">
								<div class="row merged20" id="page-contents">
									<div class="col-lg-3">
										<aside class="sidebar static left">
											
											<div class="widget">
												<h4 class="widget-title">Recent Activity</h4>
												<ul class="followers">
													<?php foreach ($frient_row as $frient_rows) {
													?>


														<?php

														if ($frient_rows['status'] === 0) {
														?>
															<li>
																<div class="mesg-meta">
																	<h6><?php echo $frient_rows['username'] ?></h6>
																	<form action="friend_list.php" method="POST">
																		<button type="submit" class="btn btn-info" name="accept_friend">Active</button>
																	</form>
																</div>
															</li>
														<?php
														} elseif ($frient_rows['status'] === 1) {
														?>
															<li>
																<div class="mesg-meta">
																	<h6><?php echo $frient_rows['username'] ?></h6>
																	<form action="friend_Unfriend.php" method="POST">
																		<button type="submit" class="btn btn-info" name="accept_friend">Unfriend</button>
																	</form>
																</div>
															</li>
													<?php
														}
													}

													?>


												</ul>

											</div><!-- recent activites -->
											<div class="widget stick-widget">
												<h4 class="widget-title">Who's follownig</h4>
												<ul class="followers">
													<?php
													foreach ($row123 as $rows123) { ?>
														<li>
															<figure></figure>
															<div class="friend-meta">
																<?php

																$receiverId =  $rows123['username'];
																$loggedInUserId = $_SESSION['user_id'];

																// Prepare a query to check if a friend request already exists
																$stmt2 = $con->prepare('SELECT * FROM friend_requests WHERE sender_id = ? AND receiver_id = ?');
																$stmt2->execute([$loggedInUserId, $receiverId]);

																// Check if a row exists
																$row33 = $stmt2->rowCount() > 0;

																if ($row33) { ?>

																	<form action="delete_friend.php" method="POST">
																		<input type="hidden" name="receiver_id" value="<?php echo $rows123['username'] ?>"> <!-- Replace with the receiver's user ID -->
																		<h6><a href="time-line.html" title=""><?php echo $rows123['username'] ?></a></h6>
																		<button type="submit" name="add_friend" class="btn btn-success">cancel request</button>
																	</form>

																<?php
																} else {
																?>
																	<form action="add_friend.php" method="POST">
																		<input type="hidden" name="receiver_id" value="<?php echo $rows123['username'] ?>"> <!-- Replace with the receiver's user ID -->
																		<h6><a href="time-line.html" title=""><?php echo $rows123['username'] ?></a></h6>
																		<button type="submit" name="add_friend" class="btn btn-success">Add Friend</button>
															</div>
															</form>
														</li>
												<?php
																}
															}


												?>

												</ul>
												</ul>
											</div><!-- who's following -->
										</aside>
									</div><!-- sidebar -->
									<div class="col-lg-6">
										<div class="central-meta">
											<div class="new-postbox">
												<figure>
													<img src="image.php" alt="">
												</figure>
												<div class="newpst-input">
													<form action="home2.php" method="post">
														<input type="hidden" name="id" value="<?php echo $user['user_id'] ?>">
														<textarea rows="2" placeholder="write something" name="post"></textarea>
														<div class="attachments">
															<ul>
																<li>
																	<i class="fa fa-music"></i>
																	<label class="fileContainer">
																		<input type="file">
																	</label>
																</li>
																<li>
																	<i class="fa fa-image"></i>
																	<label class="fileContainer">
																		<input type="file">
																	</label>
																</li>
																<li>
																	<i class="fa fa-video-camera"></i>
																	<label class="fileContainer">
																		<input type="file">
																	</label>
																</li>
																<li>
																	<i class="fa fa-camera"></i>
																	<label class="fileContainer">
																		<input type="file">
																	</label>
																</li>
																<li>
																	<button type="submit">Publish</button>
																</li>
															</ul>
														</div>
													</form>
												</div>
											</div>
										</div><!-- add post new box -->
										<?php
										if (isset($row) && (is_array($row) || is_object($row))) {
											foreach ($row as $rows) {
										?>
												<div class="loadMore">
													<div class="central-meta item">
														<div class="user-post">
															<div class="friend-info">
																<figure>
																	<img src="image.php" alt="">
																</figure>
																<div class="friend-name">

																	<ins><a href="timelinesend.html" title=""><?php echo $rows['username']; ?></a></ins>
																	<span>published: june,2 2018 19:PM</span>
																</div>
																<div class="post-meta">
																	<h3><?php echo $rows['post'] ?></h3>
																	<div class="we-video-info">
																		<ul>
																			<li>
																				<span class="views" data-toggle="tooltip" title="views">
																					<i class="fa fa-eye"></i>
																					<ins>12</ins>
																				</span>
																			</li>
																			<li>
																				<span class="comment" data-toggle="tooltip" title="Comments">
																					<i class="fa fa-comments-o"></i>
																					<ins>52</ins>
																				</span>
																			</li>
																			<li>
																				<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" class="like" data-toggle="tooltip" title="like">
																					<input type="hidden" name="post_id" value="<?php echo $rows['post_id'] ?>"> <!-- Replace with the actual post ID -->
																					<button type="submit" name="like" class="ti-heart" id="loadData">like</button>
																					<span class="like-status"><?php
																												$stmt7 = $con->prepare('SELECT COUNT(like_id) FROM like_post WHERE post_id = ? ');
																												$stmt7->execute(array($rows['post_id']));
																												echo $stmt7->fetchColumn();

																												?></span>
																				</form>
																				</span>
																			</li>

																			<li class="social-media">
																				<div class="menu">
																					<div class="btn trigger"><i class="fa fa-share-alt"></i></div>
																					<div class="rotater">
																						<div class="btn btn-icon"><a href="#" title=""><i class="fa fa-html5"></i></a></div>
																					</div>
																					<div class="rotater">
																						<div class="btn btn-icon"><a href="#" title=""><i class="fa fa-facebook"></i></a></div>
																					</div>
																					<div class="rotater">
																						<div class="btn btn-icon"><a href="#" title=""><i class="fa fa-google-plus"></i></a></div>
																					</div>
																					<div class="rotater">
																						<div class="btn btn-icon"><a href="#" title=""><i class="fa fa-twitter"></i></a></div>
																					</div>
																					<div class="rotater">
																						<div class="btn btn-icon"><a href="#" title=""><i class="fa fa-css3"></i></a></div>
																					</div>
																					<div class="rotater">
																						<div class="btn btn-icon"><a href="#" title=""><i class="fa fa-instagram"></i></a>
																						</div>
																					</div>
																					<div class="rotater">
																						<div class="btn btn-icon"><a href="#" title=""><i class="fa fa-dribbble"></i></a>
																						</div>
																					</div>
																					<div class="rotater">
																						<div class="btn btn-icon"><a href="#" title=""><i class="fa fa-pinterest"></i></a>
																						</div>
																					</div>

																				</div>
																			</li>
																		</ul>
																	</div>

																	<p>
																		World's most beautiful car in Curabitur <a href="#" title="">#test drive booking !</a> the most beatuiful car available in america and the saudia arabia, you can book your test drive by our official website
																	</p>
																</div>
															</div>
														</div>
														<div class="coment-area">
                                                        <ul class="we-comet">
                                                            <?php foreach ($row553 as $tt) { ?>
                                                                <li>
                                                                    <div class="comet-avatar">
                                                                        <img src="image.php" alt="">
                                                                    </div>
                                                                    <div class="we-comment">
                                                                        <div class="coment-head">
                                                                            <h5><a href="time-line.html" title=""><?php echo $_SESSION['username']?></a></h5>
                                                                            <span>1 year ago</span>
                                                                            <a class="we-reply" href="#" title="Reply"><i class="fa fa-reply"></i></a>
                                                                        </div>
                                                                        <p><?php echo $tt['comment'] ?></p>
                                                                    </div>
                                                                    <ul>


                                                                    </ul>
                                                                <?php } ?>
                                                                </li>

                                                                <li>
                                                                    <a href="#" title="" class="showmore underline">more comments</a>
                                                                </li>
                                                                <li class="post-comment">
                                                                    <div class="comet-avatar">
                                                                        <img src="image.php" alt="">
                                                                    </div>
                                                                    <div class="post-comt-box">
                                                                        <form action="add_comment.php" method="POST">
                                                                            <input type="hidden" name="user_id" value="<?php echo $user['user_id'] ?>">
                                                                            <input type="hidden" name="post_id" value="<?php echo $rows['post'] ?>">
                                                                            <textarea placeholder="Post your comment" name="comment"></textarea>
                                                                            <button type="submit" class="btn btn-outline-success">Publish</button>
                                                                        </form>
                                                                    </div>
                                                                </li>
                                                        </ul>

                                                    </div>
													</div>
                                            </div>

                                        <?php
											}
									}else{
										$stmt=$con->prepare('SELECT post FROM posts WHERE id=?');
										$stmt->execute([$_SESSION['user_id']]);
										$row= $stmt->fetchAll();
										foreach($row as $rows){
											echo "this the your posts only:". $rows['post'];
										}

									}
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

		<div class="bottombar">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<span class="copyright"><a target="_blank" href="https://www.templateshub.net">Templates Hub</a></span>
						<i><img src="images/credit-cards.png" alt=""></i>
					</div>
				</div>
			</div>
		</div>
		</div>
		<div class="side-panel">
			<h4 class="panel-title">General Setting</h4>
			<form method="post">
				<div class="setting-row">
					<span>use night mode</span>
					<input type="checkbox" id="nightmode1" />
					<label for="nightmode1" data-on-label="ON" data-off-label="OFF"></label>
				</div>
				<div class="setting-row">
					<span>Notifications</span>
					<input type="checkbox" id="switch22" />
					<label for="switch22" data-on-label="ON" data-off-label="OFF"></label>
				</div>
				<div class="setting-row">
					<span>Notification sound</span>
					<input type="checkbox" id="switch33" />
					<label for="switch33" data-on-label="ON" data-off-label="OFF"></label>
				</div>
				<div class="setting-row">
					<span>My profile</span>
					<input type="checkbox" id="switch44" />
					<label for="switch44" data-on-label="ON" data-off-label="OFF"></label>
				</div>
				<div class="setting-row">
					<span>Show profile</span>
					<input type="checkbox" id="switch55" />
					<label for="switch55" data-on-label="ON" data-off-label="OFF"></label>
				</div>
			</form>
			<h4 class="panel-title">Account Setting</h4>
			<form method="post">
				<div class="setting-row">
					<span>Sub users</span>
					<input type="checkbox" id="switch66" />
					<label for="switch66" data-on-label="ON" data-off-label="OFF"></label>
				</div>
				<div class="setting-row">
					<span>personal account</span>
					<input type="checkbox" id="switch77" />
					<label for="switch77" data-on-label="ON" data-off-label="OFF"></label>
				</div>
				<div class="setting-row">
					<span>Business account</span>
					<input type="checkbox" id="switch88" />
					<label for="switch88" data-on-label="ON" data-off-label="OFF"></label>
				</div>
				<div class="setting-row">
					<span>Show me online</span>
					<input type="checkbox" id="switch99" />
					<label for="switch99" data-on-label="ON" data-off-label="OFF"></label>
				</div>
				<div class="setting-row">
					<span>Delete history</span>
					<input type="checkbox" id="switch101" />
					<label for="switch101" data-on-label="ON" data-off-label="OFF"></label>
				</div>
				<div class="setting-row">
					<span>Expose author name</span>
					<input type="checkbox" id="switch111" />
					<label for="switch111" data-on-label="ON" data-off-label="OFF"></label>
				</div>
			</form>
		</div><!-- side panel -->

		<script src="js/main.min.js"></script>
		<script src="js/script.js"></script>
		<script src="js/map-init.js"></script>
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA8c55_YHLvDHGACkQscgbGLtLRdxBDCfI"></script>

	</body>

	</html>
<?php

} else {
	header('Location: login2.php');
}
?>