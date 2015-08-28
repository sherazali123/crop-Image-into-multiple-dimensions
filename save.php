<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

include '_config.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $dir = dirname(__FILE__);
    if (!empty($_POST['images'])) {
        $images = $_POST['images'];
        foreach ($images as $key => $image) {
            foreach ($image as $k => $d) {
                if ($d['name'] === 'imageSource') {
                    $src = $d['value'];
                }
                if ($d['name'] === 'imageDest') {
                    $imageDest = $d['value'];
                }
                // if ($d['name'] === 'folderName') {
                //     $folderName = $d['value'];
                // }
                if ($d['name'] === 'filename') {
                    $filename = $d['value'];
                }
                if ($d['name'] === '_x') {
                    $x = $d['value'];
                }
                if ($d['name'] === '_y') {
                    $y = $d['value'];
                }
                if ($d['name'] === '_w') {
                    $w = $d['value'];
                }
                if ($d['name'] === '_h') {
                    $h = $d['value'];
                }
                if ($d['name'] === 'originalWidth') {
                    $originalWidth = $d['value'];
                }
                if ($d['name'] === 'originalHeight') {
                    $originalHeight = $d['value'];
                }
            }

            $frame = new \Imagick($dir.'/'.$src);
            $frame->cropImage($w, $h, $x, $y);
            $frame->writeImage($dir.'/'.$imageDest.$filename);
        }
        echo json_encode(array('success' => true, 'file' => $filename, 'width' => $originalWidth, 'height' => $originalHeight));
        die;
    } else {
      echo json_encode(array('success' => false));
      die;
    }

} else {
    echo json_encode(array('success' => false));
    die;
}
