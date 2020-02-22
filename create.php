<?php include("templates/page_header.php");?>
<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	$result = create_account($dbconn, $_POST['username'], $_POST['password']);
	if (pg_num_rows($result) == 1) {
		$_SESSION['username'] = $_POST['username'];
		$_SESSION['authenticated'] = True;
		$_SESSION['id'] = pg_fetch_array($result)['id'];
		//Redirect to admin area
		header("Location: /login.php");
	}
}
?>
<!doctype html>
<html lang="en">
<head>
	<title>Create Account</title>
	<?php include("templates/header.php"); ?>
<style>

.form-create {
  width: 100%;
  max-width: 330px;
  padding: 15px;
  margin: 0 auto;
}

.form-create .form-control {
  position: relative;
  box-sizing: border-box;
  height: auto;
  padding: 10px;
  font-size: 16px;
}

.form-create .form-control:focus {
  z-index: 2;
}

.form-create input[type="email"] {
  margin-bottom: -1px;
  border-bottom-right-radius: 0;
  border-bottom-left-radius: 0;
}

.form-create input[type="password"] {
  margin-bottom: 10px;
  border-top-left-radius: 0;
  border-top-right-radius: 0;
}
</style>
</head>

	<body>
	<?php include("templates/nav.php"); ?>
	<?php include("templates/contentstart.php"); ?>

<form class="form-create" action='#' method='POST'>
      <h1 class="h3 mb-3 font-weight-normal">Create Account</h1>
      <label for="inputUsername" class="sr-only">Username</label>
      <input type="text" id="inputUsername" class="form-control" placeholder="Username" required autofocus name='username'>
      <label for="inputPassword" class="sr-only">Password</label>
      <input type="password" id="inputPassword" class="form-control" placeholder="Password" required name='password'>
      <button class="btn btn-lg btn-primary btn-block" type="submit">Create Account</button>
    </form>
<br>
	<?php include("templates/contentstop.php"); ?>
	<?php include("templates/footer.php"); ?>
	</body>
</html>
