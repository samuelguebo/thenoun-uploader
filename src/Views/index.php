<?php
/*
 * File displaying UI
 * when logged in
 */
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title> <?php echo APP_NAME; ?> | <?php echo APP_DESCRIPTION; ?></title>

  <!-- Bootstrap -->
  <link rel="stylesheet" href="//tools-static.wmflabs.org/cdnjs/ajax/libs/twitter-bootstrap/4.0.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="//tools-static.wmflabs.org/cdnjs/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <!-- Filepond stylesheet -->
  <link href="//tools-static.wmflabs.org/cdnjs/ajax/libs/filepond/4.18.0/filepond.min.css" rel="stylesheet">
  <!-- Default stylesheet -->
  <link href="public/css/style.css" rel="stylesheet">
</head>

<body>
  <section class="container boxed">
	<section class="jumbotron">

	  <h1>Akwaba, <?php echo $user->name; ?>. 
	  <a href="./logout" class="btn btn-success btn-logout"><i class="fa fa-sign-out"></i> Logout</a></h1>
	  <p class="lead"><?php echo APP_DESCRIPTION; ?></p>
		<div class="steps-pagination">
		  <a href="#" class="active">1. Import</a>
		  <a href="#">2. Describe</a>
		  <a href="#">3. Finalize</a>
		</div>
		<div class="steps-blocks">
		  <div class="uploader-container step active">
			<!-- We'll transform this input into a pond -->
			<input class="filepond" name="filepond" multiple data-allow-reorder="true" data-max-file-size="500KB"
			data-max-files="10" type="file">
		  </div>
		  <!-- filled automagically through JS -->
		  <div class="details step"></div>

		  <!-- confirmation step -->
		  <div class="confirm step">
			<div class="card">
			  <div class="card-body">
				<h5 class="card-title">Last verifications</h5>
				<p class="card-text">Please double check the 
					details below and press the confirmation button 
					once you are ready to complete the upload process.</p>
				<ul class="files"></ul>
				<div class="alert alert-danger" role="alert">This is a danger alert—check it out!</div>
			  </div>
			</div>
		  </div>

		  <!-- Let user know result from server -->
		  <div class="notify step">
			<div class="card">
			  <div class="card-body">
				<h5 class="card-title">Notification</h5>
				<ul class="files"></ul>
			  </div>
			</div>
			<br>
			<a href="./" class="btn btn-success">Restart <i class="fa fa-refresh"></i> </a>
		  </div>
		</div>

		</div>
		<div class="steps-buttons">
		  <button class="btn btn-primary" id="prev-button"><i class="fa fa fa-angle-left"></i> Back</button>
		  <button class="btn btn-primary" id="next-button">Next <i class="fa fa-angle-right"></i> </button>
		</div>
	  </section>

	</section>
	<script src="//tools-static.wmflabs.org/cdnjs/ajax/libs/jquery/3.2.1/jquery.slim.min.js"></script>
	<script src="//tools-static.wmflabs.org/cdnjs/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
	<script src="//tools-static.wmflabs.org/cdnjs/ajax/libs/twitter-bootstrap/4.0.0/js/bootstrap.min.js"></script>
	<!-- Load FilePond library -->
	<script src="//tools-static.wmflabs.org/cdnjs/ajax/libs/filepond/4.18.0/filepond.min.js"></script>
	<!-- Turn all file input elements into ponds -->
	<script src="public/js/icon.js"></script>
	<script src="public/js/script.js"></script>
<?php require_once 'footer.php';
