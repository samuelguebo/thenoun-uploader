<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo APP_NAME; ?> | <?php echo APP_DESCRIPTION; ?></title>

  <!-- Bootstrap -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <!-- Filepond stylesheet -->
  <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
  <!-- Default stylesheet -->
  <link href="public/css/style.css" rel="stylesheet">
</head>

<body>
  <section class="container boxed">
    <section class="jumbotron">

      <h1>Akwaba, <?php echo $user->name; ?>. <a href="./logout" class="btn btn-success btn-logout"><i class="fa fa-sign-out"></i> Logout</a></h1>
      <p class="lead"><?php echo APP_DESCRIPTION; ?></p>
        <div class="steps-pagination">
          <a href="#" class="active">1. Upload icons</a>
          <a href="#">2. Describe files</a>
          <a href="#">3. Finalize</a>
        </div>
        <div class="steps-blocks">
          <div class="uploader-container step active">
            <!-- We'll transform this input into a pond -->
            <input class="filepond" name="filepond" multiple data-allow-reorder="true" data-max-file-size="1MB"
            data-max-files="10" type="file">
          </div>
          <!-- filled automagically through JS -->
          <div class="details step"></div>
          <div class="finalize step">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Card title</h5>   
                <p class="card-text">Lorem ipsum dolor, sit amet consectetur adipisicing elit. Dolore omnis dolorem assumenda quasi dicta distinctio exercitationem quod id a asperiores sequi voluptate saepe illum beatae dolor soluta est, reiciendis laboriosam?</p>
                <button class="btn btn-warning" id="upload-button"><i class="fa fa-paper-plane" aria-hidden="true"></i> Finish upload</button>
              </div>
            </div> 
          </div>
        </div>
        <div class="steps-buttons">
          <button class="btn btn-primary" id="prev-button"><i class="fa fa fa-angle-left"></i> Back</button>
          <button class="btn btn-primary" id="next-button">Next <i class="fa fa-angle-right"></i> </button>
        </div>
      </section>

    </section>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <!-- Load FilePond library -->
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <!-- Turn all file input elements into ponds -->
    <script src="public/js/icon.js"></script>
    <script src="public/js/script.js"></script>
<?php require_once 'footer.php';?>