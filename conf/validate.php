<?php
if(isset($_POST['name']))
{
	$len=strlen($_POST['name']);
if(empty($_POST['name']))
{
    echo '* Please fill You name';
}
else if($len<3)
	{
		echo '* Your name is too short';
	}
}
if(isset($_POST['lname']))
{
if(empty($_POST['lname']))
{
    echo '* Please fill You surname';
}
}
echo 'fdfdssdf';



?>
