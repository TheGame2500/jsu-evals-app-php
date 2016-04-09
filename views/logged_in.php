<!-- if you need user information, just put them into the $_SESSION variable and output them here -->
Hey, <?php echo $_SESSION['user_name']; ?>. You are logged in.
Try to close this browser tab and open it again. Still logged in! ;)

<div id="eval-form">
</div>
	<button id="generate" class="btn btn-default">Generate</button>
<!-- because people were asking: "index.php?logout" is just my simplified form of "index.php?logout=true" -->
<a href="index.php?logout">Logout</a>

<script type="text/javascript">
	var routesURL = "routes/routes.php";
	$("#generate").click(function(){
		$.get(routesURL, {command : "generateEval"},
			function(data){
				$("#eval-form").html(data);
			});
	});
</script>

