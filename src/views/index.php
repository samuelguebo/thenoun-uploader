<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Wishlist | Frontend</title>

  <!-- Bootstrap -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"
    integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <!-- Filepond stylesheet -->
  <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
  <!-- Default stylesheet -->
  <link href="public/css/style.css" rel="stylesheet">
</head>

<body>
  <section class="container">
    <section class="layout-centered">
      <h3>Akwaba, <?php echo $user->name;?>. <a href="./logout" class="btn btn-success">Logout</a></h3>
      <p class="lead">Upload icons originating from The Noun project </p>

        <input class="filepond" name="filepond" multiple data-allow-reorder="true" data-max-file-size="1MB"
        data-max-files="3" type="file">
        <button class="btn btn-warning" id="upload-button"><i class="fa fa-upload" aria-hidden="true"></i> Upload</button>
    </section>
  </section>
  <!-- We'll transform this input into a pond -->

  <!-- Load FilePond library -->
  <script src="https://unpkg.com/filepond/dist/filepond.js"></script>

  <!-- Turn all file input elements into ponds -->
  <script src="public/js/script.js"></script>


</body>

</html>