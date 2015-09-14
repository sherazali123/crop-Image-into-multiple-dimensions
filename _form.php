<?php
/*
 *  _form.php
 *  This file contain the upload form. Select file from here and post data to upload.php
 */
 ?>
<!-- Container -->
<div class="well text-center">
  <h3>Select image to crop</h3>
  <!-- form -->
  <form id="uploadImage" action="upload.php" method="post" enctype="multipart/form-data">
    <!-- file component -->
    <input type="file" name="fileToUpload" id="fileToUpload">
    <!-- submit button: click here to select file-->
    <input type="submit" value="Upload Image" name="submit">
  </form>
  <!-- form end -->
</div>
