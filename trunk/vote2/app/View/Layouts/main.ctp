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
				<a class="touch" href="/questions/index">=</a>
				<a class="touch" href="/questions/create">+</a>
				<a class="touch" href="/users/login">?</a>
				<a class="touch" href="/users/register">*</a>
			</nav>
		</header>
		
<?php echo $content_for_layout; ?>

	</body>
</html>