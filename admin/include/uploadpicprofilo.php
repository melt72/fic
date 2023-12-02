<?php
// import the Intervention Image Manager Class
use Intervention\Image\ImageManager;

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

  // include composer autoload
  require '../assets/vendor/autoload.php';

  // create an image manager instance with favored driver
  $manager = new ImageManager(['driver' => 'GD']);

  include('functions.php');
  // $annotateImageRequest1 = new AnnotateImageRequest();

  include('../../include/configpdo.php');
  // session_start();
  // $cod_utente = $_SESSION['cod_utente'];

  $dst = '../img_profilo';
  //$path_to_image_directory = '../images/original/';

  if (isset($_FILES['file'])) {
    $src = $_FILES["file"]["tmp_name"];
    $fileExtension = 'jpg';

    $dstx = 250;
    $dsty = 250;
    // to finally create image instances
    $image = $manager->make($src)->fit($dstx, $dsty, function ($constraint) {
      $constraint->upsize();
    });
    $filename = PasswordCasuale(8)  . '.' . $fileExtension;
    // save file as jpg with medium quality
    $img->save($dst . '/' . $filename, 60);

    // AGGIUNGO L'IMMAGINE AL PROFILO
    try {
      $query = "UPDATE `user` SET `imm_profilo`=:immagine WHERE `username`=:id_utente";
      $stmt = $db->prepare($query);
      $stmt->bindValue('id_utente', $_POST['id'], PDO::PARAM_STR);
      $stmt->bindParam('immagine', $filename, PDO::PARAM_STR);
      $stmt->execute();
    } catch (PDOException $e) {
      echo "Error : " . $e->getMessage();
    }
    echo '/admin/img_profilo/' . $filename;
  }
}
