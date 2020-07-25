<?php
/*
 * File displaying UI
 * when logged out
 */
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo APP_NAME; ?> | <?php echo APP_DESCRIPTION; ?></title>
	<!-- Bootstrap -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"
		integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<!-- Default stylesheet -->
  <link href="public/css/style.css" rel="stylesheet">
</head>

<body>
	<br>
		<div class="container boxed">
				<div class="jumbotron">
					<h1 class="mt-5"><?php echo APP_NAME; ?></h1>
					<p class="lead"><?php echo APP_SLOGAN; ?></p>
					<p class="lead">
						<a class="btn btn-primary btn-lg btn-login md-opjjpmhoiojifppkkcdabiobhakljdgm_doc" href="/login"
							role="button"><i class="fa fa-sign-in"></i> Login with Wikimedia</a>
					</p>
					<p class="tou-description">By logging in, you agree to the <a href="https://wikitech.wikimedia.org/wiki/Wikitech:Labs_Terms_of_use">Wikimedia Labs Terms of Use</a></p>
				</div>
		<br>
		<br>
	</div>

	<?php require_once 'footer.php';
