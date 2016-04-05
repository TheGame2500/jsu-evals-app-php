<!-- if you need user information, just put them into the $_SESSION variable and output them here -->
Hey, <?php echo $_SESSION['user_name']; ?>. You are logged in.
Try to close this browser tab and open it again. Still logged in! ;)

<form method="post" action="index.php">
<input name="go" type="submit" value="Generate" />
</form>


<form action="#" method="post" onsubmit="return false;">

  <input type="button" onclick="xajax_doAdd(10,600);" id="btnAdd" value="Click Me" />

  <input type="button" onclick="xajax_doReset();" id="btnReset" value="Reset" />

  <p id="answer"></p>

</form>


<?php 
if(isset($_POST['go']))
{
	$evaluation->randomGenerator();



$xajax = new xajax();

$xajax->register(XAJAX_FUNCTION, 'doAdd');
$xajax->register(XAJAX_FUNCTION, 'doReset');

$xajax->processRequest();

function doAdd($a, $b)
{
    $response = new xajaxResponse();
    $response->assign('answer', 'innerHTML', $a + $b);
    $response->assign('reset', 'style.display', 'block');
    return $response;
}

function doReset()
{
    $response = new xajaxResponse();
    $response->clear('answer', 'innerHTML');
    $response->assign('reset', 'style.display', 'none');
    return $response;
}
	
}



?>




<script type="text/javascript">
	$('#submitEval').click(function(event){
		event.preventDefault();
		$.post('evaluation.php')
	})
</script>
<!-- because people were asking: "index.php?logout" is just my simplified form of "index.php?logout=true" -->
<a href="index.php?logout">Logout</a>


