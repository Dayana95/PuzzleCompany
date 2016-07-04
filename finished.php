<!DOCTYPE html>
<html>
<body>
<p>Great Job, it's uploaded</p> <br>


<?php echo "<img width=100% height=100% src='https://puzzlecompany.senorcoders.com/orig/" . $_GET['path'] . ".png" . "'>"; ?>


<?php
echo file_exists("https://puzzlecompany.senorcoders.com/" . $_GET['imagename']);
?>

<style type="text/css">
	body{

		background: url('assets/img/solid.jpg');
	}


	p{
		color: #fff;
		font: 30px;
		text-align: center;
	}
</style>
</body>
</html>
