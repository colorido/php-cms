<?php

class DatabaseAccess
{
	private $db;

	function DatabaseAccess()
	{
		$this->db = new SQLite3("blog.sqlite3");
	}

	function exec($sql)
	{

		$inst = explode(' ',$sql);

		switch($inst[0])
		{
			case 'SELECT' :

				$rows = $this->db->query($sql);
				$posts = array();

				while ($row = $rows->fetchArray())
				{
					$posts[] = $row;
				}

				return $posts;
		}

	}

}


class CreatePosts
{

	private $dbAccess;
	private $url;
	public $navLoopCount;

	function CreatePosts()
	{
		$this->dbAccess 	= new DatabaseAccess();
		$this->url 			= new URL();
	}

	function createMainPosts($title="",$content="",$category="")
	{

		$page = $this->url->getParam();
		
		$posts = $this->dbAccess->exec("SELECT * FROM post where 
										title LIKE 	'%".$title."%' AND
										content LIKE '%".$content."%' AND
										category LIKE '%".$category."%'
										ORDER BY no DESC");

		$pageSize	= 5;
		$pageStart	= $page * $pageSize - $pageSize;
		$pageEnd	= $page * $pageSize;

		$mainPosts = array();

		for ($i = $pageStart; $i < $pageEnd && $i < count($posts); $i++)
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

		$this->navLoopCount = ceil(count($posts)/$pageSize);

		return $mainPosts;
	}

	function createRenewPosts($title="",$content="",$category="")
	{

		$posts = $this->dbAccess->exec("SELECT * FROM post where 
								title LIKE 	'%".$title."%' AND
								content LIKE '%".$content."%' AND
								category LIKE '%".$category."%'
								ORDER BY no DESC");

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

		return $renewPosts;

	}

	function createCategoryPosts($title="",$content="",$category="")
	{

		$posts = $this->dbAccess->exec("SELECT * FROM post where 
								title LIKE 	'%".$title."%' AND
								content LIKE '%".$content."%' AND
								category LIKE '%".$category."%'
								ORDER BY no DESC");

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

		return $sideCate;
	}

	function getNavLoopCount()
	{
		ceil(count($posts)/$page_size);
	}

	function resize($text,$size,$format='UTF-8')
	{
		$text = substr($text,0,$size);
		$text = mb_substr($text,0,mb_strlen($text,$format)-5,$format) . "...";
		return $text;
	}

}

class URL
{
	private $url;

	function URL()
	{
		$this->url = $_SERVER["REQUEST_URI"];
	}

	function getParam()
	{
		$url = parse_url($this->url);
		$path = explode('/',$url['path']);

		switch($path[2])
		{
			case 'article' :

			default :

				if(isset($url['query'])){
					return strval(explode('=',$url['query'])[1]);
				}else{
					return 1;
				}
		}
	}
}

?>