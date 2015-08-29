<?php
/*
 * Upload  form
 */
 ?>

<div class="well text-center">
  <h3>Select image to crop</h3>
  <form id="uploadImage" action="upload.php" method="post" enctype="multipart/form-data">

    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="Upload Image" name="submit">
  </form>
</div>
