<!DOCTYPE html>
<html ><head>
<meta charset="UTF-8">
<script scr="js/conf.js"></script>
<script src="js/jquery-2.1.4.js" type="text/javascript"></script>
<script src="js/jquery-ui-1.11.4.custom/jquery-ui.js"></script>
<link rel="stylesheet" href="css/table.css"
<link rel="stylesheet" href="js/jquery-ui-1.11.4.custom/jquery-ui.css">
<link rel="stylesheet" href="js/jquery-ui-1.11.4.custom/jquery-ui.theme.css">
<script src="js/valform.js" type="text/javascript"></script>
<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="bootstrap/css/bootstrap-theme.min.css"> 
<script src="bootstrap/js/bootstrap.min.js"></script>

<style>
.body
{
	background-image:url('graphics/rbs.jpg');
}
table th
{
	background-color:black;
	font-size:25px;
	color:white;
}
table tr
{
	background-color:white;
	font-size:20x;
	font-weight:bold;

}
</style>
  <title>Protdukcja TimeLines</title>  

 <script src="js/prefixfree.min.js"></script>
 
    
  </head>

  <body>

    <div class="body"></div>
		<div class="grad"></div>
		<div class="header">
			<div>Produkcja  <span>TimeLines by GuruGozdek ver. 1.0</span><br>
          </div>
		
		</div>
        <div class="login">
<?php
require_once('conf/times.php');
?>

	<br>
	<section id="zero">
 	<input type="button" value="Czasy Dana Myjnia" name="submit" id="danaMyjnia">	<input type="button" value="Czasy Dany Pracownik" name="submit" id="danyPracownik"><br><br>
	
	<input type="button" value="Czasy Typ Myjni" name="submit" id="typMyjni">	<input type="button" value="Test" name="submit" id="test">
	</section>


	<section id="one" hidden="true">
	<?php
		$d=new times();
		$result=$d->czasyDanaMyjnia();
		$d->getView($result);
	?>
	<input type="button" value="Powrót" name="submit" id="back1" style="width: 550px;
	height: 70px; margin:0px">
	
	</section>

	<section id="two" hidden="true">
	<?php
		$d=new times();
		$result=$d->czasyTypMyjni();
		$d->getView($result);
	?>
	<input type="button" value="Powrót" name="submit" id="back2" style="width: 550px;
	height: 70px; margin:0px">
	
	</section>
	
	
	
	
	
	<script>
		$('#danaMyjnia').click(function()
		{
		$('#zero').hide('slow'); $('#one').show('slow');
		});
		$('#back1').click(function()
		{
		$('#one').hide('slow'); $('#zero').show('slow');
		});

		$('#typMyjni').click(function()
		{
		$('#zero').hide('slow'); $('#two').show('slow');
		});
		$('#back2').click(function()
		{
		$('#two').hide('slow'); $('#zero').show('slow');
		});
	</script>
	



</html>
