<?php
/*
* !!! THIS IS JUST AN EXAMPLE !!!, PLEASE USE ImageMagick or some other quality image processing libraries
*/
$imgUrl = $_POST['imgUrl'];
// original sizes
$imgInitW = $_POST['imgInitW'];
$imgInitH = $_POST['imgInitH'];

// resized sizes
$imgW = $_POST['imgW'];
$imgH = $_POST['imgH'];
// offsets
$imgY1 = $_POST['imgY1'];
$imgX1 = $_POST['imgX1'];

// Large Image Offsets based on the small image

$multiplierX = $imgInitW/$imgW;
$multiplierY = $imgInitH/$imgH;
$lgImgX1 = $imgX1*$multiplierX;
$lgImgY1 = $imgY1*$multiplierY;

// crop box
$cropW = $_POST['cropW'];
$cropH = $_POST['cropH'];
$cropW = $_GET['w'];
$cropH = $_GET['h'];

// rotation angle
$angle = $_POST['rotation'];

$jpeg_quality = 100;

$output_filename = "temp/" . $_GET['filename'];
$output_filename_orig = "orig/" . $_GET['filename'];

// uncomment line below to save the cropped image in the same location as the original image.
//$output_filename_orig = dirname($imgUrl). "/kharron_croppedImg_".rand();

$what = getimagesize($imgUrl);

switch(strtolower($what['mime']))
{
    case 'image/png':
        $img_r = imagecreatefrompng($imgUrl);
    $source_image = imagecreatefrompng($imgUrl);
    $type = '.png';
        break;
    case 'image/jpeg':
        $img_r = imagecreatefromjpeg($imgUrl);
    $source_image = imagecreatefromjpeg($imgUrl);
    error_log("jpg");
    $type = '.png';
        break;
    case 'image/gif':
        $img_r = imagecreatefromgif($imgUrl);
    $source_image = imagecreatefromgif($imgUrl);
    $type = '.png';
        break;
    default: die('image type not supported');
}


//Check write Access to Directory

if(!is_writable(dirname($output_filename))){
  $response = Array(
      "status" => 'error',
      "message" => 'Can`t write cropped File'
    );
}else{
  // original sizes
  /*$imgInitW = $_POST['imgInitW'];
  $imgInitH = $_POST['imgInitH'];

  // resized sizes
  $imgW = $_POST['imgW'];
  $imgH = $_POST['imgH'];*/
  if ($imgInitW < $imgW || $imgInitH <$imgH) {


    $response = Array(
        "status" => 'error',
        "message" => 'The dimesion of the image is not correct'.$ww.'-'.$imgInitW
      );
  }else{

    // resize the original image to size of editor
    $resizedImage = imagecreatetruecolor($imgW, $imgH);
    $originalSize = imagecreatetruecolor($imgInitW, $imgInitH);

    imagecopyresampled($resizedImage, $source_image, 0, 0, 0, 0, $imgW, $imgH, $imgInitW, $imgInitH);
    imagecopyresampled($originalSize, $source_image, 0, 0, 0, 0, $imgInitW, $imgInitH, $imgInitW, $imgInitH);

    // rotate the rezized image
    $rotated_image = imagerotate($resizedImage, -$angle, 0);
    // rotate the original imagename
    $rotated_original_image = imagerotate($originalSize, -$angle, 0);

    // find new width & height of rotated image
    $rotated_width = imagesx($rotated_image);
    $rotated_height = imagesy($rotated_image);
    // find new width & height of rotated original image
    $rotated_width_original = imagesx($rotated_original_image);
    $rotated_height_original = imagesy($rotated_original_image);

    // diff between rotated & original sizes
    $dx = $rotated_width - $imgW;
    $dy = $rotated_height - $imgH;
    // diff between rotated & original sizes of the original imagename
    $odx = $rotated_width_original - $imgInitW;
    $ody = $rotated_height_original - $imgInitH;

    // crop rotated image to fit into original rezized rectangle
  $cropped_rotated_image = imagecreatetruecolor($imgW, $imgH);
  imagecolortransparent($cropped_rotated_image, imagecolorallocate($cropped_rotated_image, 0, 0, 0));
  imagecopyresampled($cropped_rotated_image, $rotated_image, 0, 0, $dx / 2, $dy / 2, $imgW, $imgH, $imgW, $imgH);

  // crop rotated image to fit into original rezized rectangle of the original imagename
  //$cropped_rotated_image_original = imagecreatetruecolor($imgInitw, $imgInitH);
  //imagecolortransparent($cropped_rotated_image_original, imagecolorallocate($cropped_rotated_image_original,0,0,0));
  //imagecopyresampled($cropped_rotated_image_original, $rotated_original_image,0,0,$odx/2, $ody/2, $imgInitW, $imgInitH,$imgInitW, $imgInitH);


  // crop image into selected area
  $final_image = imagecreatetruecolor($cropW, $cropH);
  $final_image_large = imagecreatetruecolor($cropW, $cropH);

  imagecolortransparent($final_image, imagecolorallocate($final_image, 0, 0, 0));
  imagecolortransparent($final_image_large, imagecolorallocate($final_image_large, 0, 0, 0));

  imagecopyresampled($final_image, $cropped_rotated_image, 0, 0, $imgX1, $imgY1, $cropW, $cropH, $cropW, $cropH);
  imagecopyresampled($final_image_large, $rotated_original_image, 0, 0, $lgImgX1, $lgImgY1, $cropW, $cropH, $cropW, $cropH);
  //imagecopyresampled($final_image_large, $originalSize, 0, 0, $lgImgX1, $lgImgY1, $cropW, $cropH, $cropW, $cropH);


  // finally output png image
  //imagepng($final_image, $output_filename.$type, $png_quality);
  imagejpeg($final_image_large, $output_filename_orig.$type, $jpeg_quality);
  imagejpeg($final_image, $output_filename.$type, $jpeg_quality);
  $response = Array(
      "status" => 'success',
      "url" => $output_filename.$type
    );
}
}
print json_encode($response);
