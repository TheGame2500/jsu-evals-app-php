<?php
// show potential errors / feedback (from login object)
if (isset($login)) {
    if ($login->errors) {
        foreach ($login->errors as $error) {
            echo $error;
        }
    }
    if ($login->messages) {
        foreach ($login->messages as $message) {
            echo $message;
        }
    }
}
?>

<!-- login form box -->
<form class="form-inline jumbotron row" method="post" action="index.php" name="loginform">

	<div class="center-block">
		<div class="form-group">
			<label for="login_input_username">Username</label>
			<input id="login_input_username" class="form-control" type="text" name="user_name" required />
		</div>

		<div class="form-group">
			<label for="login_input_password">Password</label>
			<input id="login_input_password" class="form-control" type="password" name="user_password" autocomplete="off" required />
		</div>

		<button type="submit" name="login" class="btn btn-success">Log in</button> 
	</div>

</form>

<div align=center>
	<a href="register.php" class="btn btn-warning">Register new account</a>
</div>
