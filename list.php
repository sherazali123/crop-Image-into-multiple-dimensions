<?php
/*
 * list.php
 * Show all upload files and options like delete
 */

 // general config +  global variables
include '_config.php';

// error array: push error into this if found any while excecution
$_dty_error = array();

// check if the file is deleted
$_dty_deleted = !empty($_GET['deleted']) ? $_GET['deleted'] : "";

// root directory real path as Imagak, a php extension to crop images, use real path to access and save file on server
$dir = dirname(__FILE__);

// path where orginal files exists
$_dty_originalDir = $dir.'/original';

// scan folder for exisiting images
$_dty_originalFiles = array_diff(scandir($_dty_originalDir), array('..', '.'));

// files holding all information about the files
$_dty_files = [];

// if found any original file
if (!empty($_dty_originalFiles)) {

  // traverse each file step by step
  foreach ($_dty_originalFiles as $file) {

    // find original image dimensions e.g. image width, image height, image type and others attributes from uploaded file
    list($_dty_width, $_dty_height, $_dty_type, $_dty_attr) = getimagesize($_dty_originalDir.'/'.$file);

      $_dty_files[] = array('name' => $file, 'width' => $_dty_width, 'height' => $_dty_height);
  }
}
 ?>
<!DOCTYPE html>
<html>
<head>
<!-- Header: Including all the css content here -->
<?php include '_header.php'; ?>
</head>
<body class="list">
<div class="row text-center">
  <a href="<?php echo $_dty_baseUrl.'index.php'; ?>" class="btn btn-primary text-center">Upload more</a>
</div>
<!-- Container -->
  <!-- Print errors if found any -->
  <?php if (!empty($_dty_error)): ?>
    <?php foreach ($_dty_error as $e): ?>
      <div class="container">
          <div class="row">
            <div class="alert alert-danger">
              <?php echo $e; ?>
            </div>
          </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

<div class="row text-center">
  <div class="container" style="padding: 20px 0;">
    <!-- Print success if found any -->
    <?php if (!empty($_dty_deleted) && $_dty_deleted === "ok"): ?>
        <div class="container">
            <div class="row">
              <div class="alert alert-danger">
                File has been removed successfully.
              </div>
            </div>
        </div>
    <?php endif; ?>
        <?php if (!empty($_dty_files)): ?>
          <?php foreach ($_dty_files as $file): ?>
            <div class="row well " href="<?php echo $file['name']; ?>" id="<?php echo $file['name']; ?>">
                    <div class="col-md-2">
                      <a href="#" class="thumbnail">
                        <img src="<?php echo $_dty_baseUrl.'original/'.$file['name']; ?>" alt="<?php echo $file['name']; ?>">
                      </a>
                    </div>
                    <div class="col-md-10 center">

                    </div>
                    <div class="col-md-1 actions">
                      <a href="<?php echo $_dty_baseUrl.'delete.php?file='.$file['name'].'&w='.$file['width'].'&h='.$file['height']; ?>" class="btn btn-danger" aria-label="Left Align">
                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                      </a>
                    </div>
            </div>
          <?php endforeach; ?>
            <?php endif; ?>
  </div>
</div>
 <div class="row text-center">
 <a href="<?php echo $_dty_baseUrl.'index.php'; ?>" class="btn btn-primary text-center">Upload more</a></div>
</body>
</html>
