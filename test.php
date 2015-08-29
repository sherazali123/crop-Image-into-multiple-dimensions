<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

include('_config.php');
include('class.upload.php');
$x = 100;
$y = 200;
$w = 200;
$h = 300;

$dir = dirname(__FILE__);

$frame = new \Imagick($dir .'/d.jpg');
$frame->cropImage($w, $h, $x, $y);
$frame->writeImage($dir.'/uploads/d.jpg');
var_dump($frame);die;
  var_dump('end');die;
