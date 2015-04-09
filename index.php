<?php

require('init.php');

if(!isset($_GET["page"]))
	$page = 1;
else
	$page = $_GET["page"];

$page_size	= 5;
$page_start	= $page * $page_size - $page_size;
$page_end	= $page * $page_size;

$db = new SQLite3("blog.sqlite3");

$rows = $db->query('SELECT * FROM post ORDER BY no DESC');
$posts = array();

while ($row = $rows->fetchArray())
{
	$posts[] = $row;
}

$mainPosts = array();

for ($i = $page_start; $i < $page_end && $i < count($posts); $i++)
{
	$mainPosts[$i] = array();

	$mainPosts[$i]['no'] 		= $posts[$i]['no'];
	$mainPosts[$i]['title']		= $posts[$i]['title'];
	$mainPosts[$i]['date']		= $posts[$i]['date'];
	$mainPosts[$i]['category'] 	= explode('/',$posts[$i]['category'])[0];

	$text = $posts[$i]['content'];
	$text = str_replace("-","",$text);

	if(strlen($text) < 410) {
		$mainPosts[$i]['content'] = $text;
	} else {
		$text = resize( $text, 410 );
		$text = $text . "<span><a href=\"article?no=" . $posts[$i]['no'] . "\">続きを読む</a></span>";
		$mainPosts[$i]['content'] = $text;
	}
}

$renewPosts = array();

for($i = 0; $i < count($posts) && $i <= 9; $i++)
{

	$text = "<a href=\"article/?no=".$posts[$i]['no']."\">";

	if(strlen($text) > 40)
		$text .= resize( $posts[$i]['title'] , 40 ) . "</a>";
	else
		$text .= $posts[$i]['title'] . "</a>";

	$renewPosts[] = $text;
}


$sideCate = array();
$found = false;

foreach($posts as $post)
{
	$cates = explode('/',$post['category']);
	foreach($cates as $cate)
	{
		foreach($sideCate as $sCate)
		{
			$found = false;
			if(strstr($sCate,$cate))
			{
				$found = true;
				break;
			}
			else
			{
				$found = false;
			}
		}
		if(!$found)
		{
			$sideCate[] = $cate;
		}
	}
}


for($i = 0;$i < count($sideCate) && $i < 9;$i++)
{
	$sideCate[$i] = "<a href=\"category/?cate=".$sideCate[$i]."\">".$sideCate[$i]."</a>";
}


function resize($text,$size,$format='UTF-8')
{
	$text = substr($text,0,$size);
	$text = mb_substr($text,0,mb_strlen($text,$format)-5,$format) . "...";
	return $text;
}

$smarty->assign('navLoopCount', ceil(count($posts)/$page_size) );
$smarty->assign('sideCate', $sideCate);
$smarty->assign('mainPosts', $mainPosts);
$smarty->assign('renewPosts', $renewPosts);
$smarty->display('top.tpl');

?>