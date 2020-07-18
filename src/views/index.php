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
          <a href="#" class="active">Upload icons</a>
          <a href="#">Describe files</a>
          <a href="#">Finalize</a>
        </div>
        <div class="steps-blocks">
          <div class="uploader-container step active">
            <!-- We'll transform this input into a pond -->
            <input class="filepond" name="filepond" multiple data-allow-reorder="true" data-max-file-size="1MB"
            data-max-files="10" type="file">
          </div>
          <div class="details step">           
            <div id="accordion">
              <!-- first block --> 
              <div class="card">
                <div class="card-header" id="headingOne">
                  <h5 class="mb-0">
                    <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                      Icon 1
                    </button>
                  </h5>
                </div>

                <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                  <div class="card-body">
                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                  </div>
                </div>
              </div>

              <!-- second block -->
              <div class="card">
                <div class="card-header" id="headingTwo">
                  <h5 class="mb-0">
                    <button class="btn btn-link" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                      Icon 2
                    </button>
                  </h5>
                </div>

                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                  <div class="card-body">
                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                  </div>
                </div>
              </div>
            </div>
          </div>
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
    <script src="public/js/script.js"></script>
<?php require_once 'footer.php';?>