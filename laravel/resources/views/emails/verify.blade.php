<h1>Hi <?php echo $user; ?>!</h1>
<p>Please click on the link to verify your email: <a href="{{ URL::to('register/verify/' . $code) }}">Email verification.</a></p>
