<!-- if you need user information, just put them into the $_SESSION variable and output them here -->
Hey, <?php echo $_SESSION['user_name']; ?>. You are logged in.
Try to close this browser tab and open it again. Still logged in! ;)

<form method="post" action="index.php">
<input name="go" type="submit" value="Generate" />
</form>


<?php 
if(isset($_POST['go']))
{
	$evaluation->randomGenerator();
	
}


?>
<!-- because people were asking: "index.php?logout" is just my simplified form of "index.php?logout=true" -->
<a href="index.php?logout">Logout</a>


