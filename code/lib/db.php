<?php
$salt = "this is a salty string";
$dbconn = pg_pconnect("host=$pg_host port=$pg_port dbname=$pg_dbname user=$pg_dbuser password=$pg_dbpassword") or die("Could not connect");
if ($debug) {
	echo "host=$pg_host, port=$pg_port, dbname=$pg_dbname, user=$pg_dbuser, password=$pg_dbpassword<br>";
	$stat = pg_connection_status($dbconn);
	if ($stat === PGSQL_CONNECTION_OK) {
		echo 'Connection status ok';
	} else {
		echo 'Connection status bad';
	}    
}

function run_query($dbconn, $query) {
	if ($debug) {
		echo "$query<br>";
	}
	$result = pg_query($dbconn, $query);
	if ($result == False and $debug) {
		echo "Query failed<br>";
	}
	return $result;
}

//database functions

function get_article_list($dbconn,$username){
	
	/*
	$role = pg_prepare($dbconn,"","select role from authors where username =$1");
	$role = pg_execute($dbconn,"",array($username));
	
	
	
	if ($role =='admin'){
		$result = pg_prepare($dbconn,"","select articles.created_on as date,
		articles.aid as aid,
		articles.title as title,
		authors.username as author,
		articles.stub as stub
		FROM articles INNER JOIN authors ON
		articles.author = authors.id
		ORDER BY date DESC");
		$result = pg_execute($dbconn,"",array());
		return $result;
	}
	else {
		$result = pg_prepare($dbconn,"","select articles.created_on as date,
		articles.aid as aid,
		articles.title as title,
		articles.author as author,
		articles.stub as stub
		FROM articles INNER JOIN authors ON
		articles.author = authors.id
		where authors.role = $1
		ORDER BY date DESC");
		$result = pg_execute($dbconn,"",array($role));
		return $result;
	}
	*/
	
	$query= 
		"SELECT 
		articles.created_on as date,
		articles.aid as aid,
		articles.title as title,
		authors.username as author,
		articles.stub as stub
		FROM
		articles
		INNER JOIN
		authors ON articles.author=authors.id
		ORDER BY
		date DESC";
	return run_query($dbconn, $query);
}
function get_article_listIndex($dbconn){
	$query= 
		"SELECT 
		articles.created_on as date,
		articles.aid as aid,
		articles.title as title,
		authors.username as author,
		articles.stub as stub
		FROM
		articles
		INNER JOIN
		authors ON articles.author=authors.id
		ORDER BY
		date DESC";
	return run_query($dbconn, $query);
}
function create_account($dbconn,$username,$password){
	
	$passwordhash = sha1($password,$salt);
	$result = pg_prepare($dbconn,"","insert into authors(username,password) values ($1,$2)");
	$result = pg_execute($dbconn,"",array($username,$passwordhash));
	
	$result  = pg_prepare($dbconn,"","select * from authors where username=$1");
	$result  = pg_execute($dbconn,"",array($username));
	
	return $result;
}
function get_article($dbconn, $aid) {
			
	$result = pg_prepare($dbconn,"","select articles.created_on as date,
		articles.aid as aid,
		articles.title as title,
		authors.username as author,
		articles.stub as stub,
		articles.content as content FROM articles INNER JOIN authors ON articles.author=authors.id
		WHERE aid=$1 LIMIT 1");
	$result = pg_execute($dbconn,"",array(htmlspecialchars($aid)));
	return $result;
}

function delete_article($dbconn, $aid) {
	$username = $_SESSION['username'];
	$result = articleLog($dbconn,"deleted article",$username,$aid);
	$result = pg_prepare($dbconn,"","DELETE FROM articles WHERE aid=$1");
	$result = pg_execute($dbconn,"",array(htmlspecialchars($aid)));
	return $result;
}

function add_article($dbconn, $title, $content, $author) {
	$username = $_SESSION['username'];
	$result = articleLog($dbconn,"article created",$username,$aid);
	$stub = substr($content, 0, 30);
	$aid = str_replace(" ", "-", strtolower($title));
	$result = pg_prepare($dbconn,"","insert into articles(aid, title, author, stub, content)
		values($1,$2,$3,$4,$5)");
	$result = pg_execute($dbconn,"",array(htmlspecialchars($aid),htmlspecialchars($title),
		htmlspecialchars($author),htmlspecialchars($stub),htmlspecialchars($content)));
	return $result;
}

function update_article($dbconn, $title, $content, $aid) {
	$username = $_SESSION['username'];
	$result = articleLog($dbconn,"updated article",$username,$aid);
	$query=
		"UPDATE articles
		SET 
		title='$title',
		content='$content'
		WHERE
		aid='$aid'";
	return run_query($dbconn, $query);
}
function articleLog($dbconn,$a,$username,$aid){
	$aid = str_replace(" ", "-", strtolower($title));
	$result = pg_prepare($dbconn,"","insert into articlelog(action, username, aid) values ($1,$2,$3)");
	$result = pg_execute($dbconn,"",array($a,$username,$aid));
	return $result;
	
}
function adminLog($dbconn,$a, $username){
	$result = pg_prepare($dbconn,"","insert into adminlog(action, username) values ($1,$2)");
	$result = pg_execute($dbconn,"",array($a,$username));
	return $result;
}

function authenticate_user($dbconn, $username, $password) {
	
	$passwordhash = sha1($password,$salt);
	$result = adminLog($dbconn,"login",$username);
	$result = pg_prepare($dbconn,"",'select authors.id as id,
        authors.username as username,
        authors.password as password,
        authors.role as role from authors where username = $1 AND password=$2');
    return     pg_execute($dbconn,"",array($username,$passwordhash));
	
}	
?>
