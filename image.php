<?php
header("Access-Control-Allow-Origin: *"); 
echo file_exists("temp/puzzle_" . $_GET['path'] . ".png");
?>
