<!doctype html>
<html>
	<head>
		<style type="text/css">
			@import url(http://fonts.googleapis.com/css?family=Ubuntu+Condensed);
			
			* {
				margin: 0;
				padding: 0;
				font-family: 'Ubuntu Condensed', sans-serif;
				color: #fff;
				font-size: 20px;
			}
			
			body {
				background: #000;
			}
			
			a {
				text-decoration: none;
			}
			
			a.touch {
				width: 80px;
				height: 80px;
				background: #777;
				font-size: 60px;
				text-align: center;
				display: inline-block;
			}
			
			a.touch:hover {
				background: #888;
			}
			
			header {
				background: #aaa;
			}
			
			header h1 {
				font-size: 120px;
				display: block;
				vertical-align: bottom;
				text-align: left;
				height: 60px;
				padding-left: 20px;
			}
			
			header nav {
				display: block;
				vertical-align: bottom;
				text-align: right;
				padding-right: 20px;
			}
			
			article {
				background: #222;
			}
			
			article a#close {
				float: right;
				margin: 20px;
			}
			
			article h2 {
				margin-bottom: 20px;
			}
			
			article div {
				padding: 20px;
			}
			
			article p {
				margin: 3px 0;
			}
			
			article span {
				padding: 20px;
				vertical-align: 20px;
			}
			
			article div.result {
				margin: 3px 0;
				background: #000;
				padding: 0;
				width: 100%;
				height: 80px;
			}
			
			article div.result div{
				background: #777;
				padding: 0;
				height: 80px;
			}
			
			article div.result span{
				display: block;
				padding: 28px;
			}
			
			section {
				padding: 20px;
			}
			
			section a {
				display: block;
				background: #444;
				margin: 3px 0;
				padding: 20px;
				font-size: 20px;
			}
			
			section a:hover {
				background: #555;
			}
			
			form input {
				background: #fff;
				color: #000;
				border: none;
				padding: 20px;
				display: block;
				margin: 3px 0;
			}
			
			form input[type=submit] {
				color: #fff;
				padding: 0;
				margin: 20px 0 0;
				width: 80px;
				height: 80px;
				background: #777;
				font-size: 60px;
				text-align: center;
			}
			
			form input[type=submit]:hover {
				background: #888;
			}
		</style>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
		<script type="text/javascript">
			$(function() {
				
				$('section a').click(function(){
					$('article').show();
					return false;
				});
				
				$('article a#close').click(function(){
					$('article').hide();
					return false;
				});
				
				var answers = 2;
				$('a#addAnswer').click(function(){
					answers++;
					$(this).before('<input type="text" placeholder="Answer ' + answers + '" />');
					return false;
				});
				
			});
		</script>
	</head>
	<body>
		<header>
			<h1>Vote</h1>
			<nav>
				<a class="touch" href="">+</a>
				<a class="touch" href="">?</a>
				<a class="touch" href="">*</a>
			</nav>
		</header>
		<article>
			<a id="close" class="touch" href="">×</a>
			<div>
				<h2>What is Chris doing?</h2>
				<p><a class="touch" href="">✓</a><span>Eating</span></p>
				<p><a class="touch" href="">✓</a><span>Fishing</span></p>
				<p><a class="touch" href="">✓</a><span>Baking</span></p>
				<p><a class="touch" href="">✓</a><span>Brooding</span></p>
			</div>
			<div>
				<h2>What is Chris doing?</h2>
				<div class="result"><div style="width:30%;"><span>Eating</span></div></div>
				<div class="result"><div style="width:40%;"><span>Fishing</span></div></div>
				<div class="result"><div style="width:70%;"><span>Baking</span></div></div>
				<div class="result"><div style="width:10%;"><span>Brooding</span></div></div>
			</div>
			<div>
				<form>
					<h2>Pose a poll</h2>
					<input type="text" placeholder="Question" />
					<input type="text" placeholder="Answer 1" />
					<input type="text" placeholder="Answer 2" />
					<a id="addAnswer" class="touch" href="#">+</a>
					<input type="submit" value="✓" />
				</form>
			</div>
			<div>
				<form>
					<h2>Login</h2>
					<input type="text" placeholder="Email" />
					<input type="password" placeholder="Password" />
					<input type="submit" value="✓" />
				</form>
				<form>
					<h2>Register</h2>
					<input type="text" placeholder="Email" />
					<input type="password" placeholder="Password" />
					<input type="password" placeholder="Password" />
					<input type="text" placeholder="Display Name" />
					<input type="submit" value="✓" />
				</form>
			</div>
		</article>
		<section>
			<a href="">What is Chris doing?</a>
			<a href="">How many times has Chris seen this?</a>
			<a href="">Pick your favorite feature about Chris.</a>
			<a href="">What is Chris doing?</a>
			<a href="">How many times has Chris seen this?</a>
			<a href="">Pick your favorite feature about Chris.</a>
			<a href="">What is Chris doing?</a>
			<a href="">How many times has Chris seen this?</a>
			<a href="">Pick your favorite feature about Chris.</a>
			<a href="">What is Chris doing?</a>
			<a href="">How many times has Chris seen this?</a>
			<a href="">Pick your favorite feature about Chris.</a>
			<a href="">What is Chris doing?</a>
			<a href="">How many times has Chris seen this?</a>
			<a href="">Pick your favorite feature about Chris.</a>
			<a href="">What is Chris doing?</a>
			<a href="">How many times has Chris seen this?</a>
			<a href="">Pick your favorite feature about Chris.</a>
			<a href="">What is Chris doing?</a>
			<a href="">How many times has Chris seen this?</a>
			<a href="">Pick your favorite feature about Chris.</a>
			<a href="">What is Chris doing?</a>
			<a href="">How many times has Chris seen this?</a>
			<a href="">Pick your favorite feature about Chris.</a>
			<a href="">What is Chris doing?</a>
			<a href="">How many times has Chris seen this?</a>
			<a href="">Pick your favorite feature about Chris.</a>
			<a href="">What is Chris doing?</a>
			<a href="">How many times has Chris seen this?</a>
			<a href="">Pick your favorite feature about Chris.</a>
			<a href="">What is Chris doing?</a>
			<a href="">How many times has Chris seen this?</a>
			<a href="">Pick your favorite feature about Chris.</a>
		</section>
	</body>
</html>