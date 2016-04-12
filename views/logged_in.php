<!-- if you need user information, just put them into the $_SESSION variable and output them here -->
<div class="row bg-primary" style="margin: 10px 0px;padding:10px">
	<h3 style="margin:0"> Salut, <?php echo $_SESSION['user_name']; ?>! </h3>
	Instructiuni:
	<ul>
		<li>Apasa pe "Da-mi un formular" pentru a evalua un formular.</li>
		<li>Daca nu mai sunt formulare, petrece.</li>
		<li>Spor!</li>
	</ul>
</div>
<div id="notifications" class="row" style="margin: 10px 0px;padding:10px; margin-top:0px">
</div>
<div id="eval-form">
</div>
	<button id="generate" class="btn btn-success">Da-mi un formular</button>
<!-- because people were asking: "index.php?logout" is just my simplified form of "index.php?logout=true" -->
<a class="btn btn-danger" href="index.php?logout">Logout</a>

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
				$("#generate").hide();
				$("#eval-form").html(data);
			});
	}

	function submitMarks(event) {
		event.preventDefault();
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
						var response = JSON.parse(data);
						if(response.success){
							$('#eval-form').empty();
							$("#generate").show();
							$("#notifications").removeClass('bg-danger').addClass('bg-success');
						}
						else{
							$("#notifications").removeClass('bg-success').addClass('bg-danger');
						}
						$("#notifications").text(response.message);
					}
				})
		}
</script>

