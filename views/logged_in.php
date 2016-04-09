<!-- if you need user information, just put them into the $_SESSION variable and output them here -->
Hey, <?php echo $_SESSION['user_name']; ?>. You are logged in.
Try to close this browser tab and open it again. Still logged in! ;)
<div class="row">
	<div id="notifications" class="center-block bg-success"></div>
</div>
<div id="eval-form">
</div>
	<button id="generate" class="btn btn-default">Generate</button>
<!-- because people were asking: "index.php?logout" is just my simplified form of "index.php?logout=true" -->
<a href="index.php?logout">Logout</a>

<script type="text/javascript">
	//Event binders
	$("#generate").click(getNewEvalForm);
	$("#eval-form").submit(submitMarks);
	
	//Config
	var routesURL = "routes/routes.php";

	//Event handlers
	function getNewEvalForm() {
		$.get(routesURL, {command : "generateEval"},
			function(data){
				$("#eval-form").html(data);
			});

	}

	function submitMarks(event) {
		event.preventDefault();
		console.log('muie');
		$.post({
				url : routesURL,
				data : {
							command : "submitMarks", 
							marks : {
								notaFormular : $("#nota-formular").val(),
								notaRecomandare : $("#nota-recomandare").val(),
								notaVoluntariat : $("#nota-voluntariat").val()
							}
						},
				success : function(data){
						$("#notifications").text(data);
					}
				})
	}
</script>

