<?php

require_once('blog.php');
$dbFunc = new CreatePosts();
$posts = $dbFunc->createMainPosts();
$posts = $dbFunc->createRenewPosts();
$posts = $dbFunc->createCategoryPosts();
$dbFunc->navLoopCount;

foreach ($posts as $post) {
	echo $post."<br>";
}


?>