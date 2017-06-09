<html>
<head>
	<title>Graphical Representation of variables</title>
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/styles/default.min.css">
	<link class="codestyle" rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/styles/idea.min.css">
	<script type="text/javascript" src="//code.jquery.com/jquery-1.12.4.min.js"></script>
	<style>
		* {
			font-family: Monospace, Helvetica, Arial, sans-serif;
			font-size: 12px;
		}
		a {
			color : blue;
		}
		h3 {
			font-size: 30px;
			margin-bottom: 6px;
		}
		#graphicRepresentation, #graphicRepresentation * {
			font-size:16px;
		}
		#currentValue {
			font-weight: bold;
			cursor: pointer;
		}
		#currentValueBloc {
			display: none;
			position: absolute;
			background: white;
			padding: 0;
		}
		.int, .float, .object, .string, .boolean, .array, .pointer {
			padding: 4px;
		}
		.int {
			background : #ffA500;
		}
		.float {
			background : #aa9900;
		}
		.array {
			background : #555555;
			border: 2px solid black;
		}
		.object {
			background : #808080;
		}
		.string {
			background : #008000;
			color: white;
		}
		.boolean {
			background : #000080;
			color: white;
		}
		.pointer {
			border: 4px solid white;
			animation: colorchange 2s infinite;
			-webkit-animation: colorchange 2s infinite;
		}
		@keyframes colorchange
		{
			0%   {border-color: white;}
			50%  {border-color: red;}
			100% {border-color: white;}
		}

		@-webkit-keyframes colorchange /* Safari and Chrome - necessary duplicate */
		{
			0%   {border-color: white;}
			50%  {border-color: red;}
			100% {border-color: white;}
		}
		table.array {
			border-spacing: 4px;
		}
		table.array th {
			color: white;
		}
		table.array td {
			vertical-align: top;
		}
		.navdisabled {
			color : grey;
		}
		pre.xdebug-var-dump {
			border: 4px solid black;
			padding:8px 10px;
			margin-top:4px;
		}
	</style>
</head>
<body>
<?php

require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'functions.php';

$pointer = isset($_GET['pointer']) ? strip_tags($_GET['pointer']) : 0;
$pointerArray = strpos($pointer, '-') ? explode('-', $pointer) : array(intval($pointer));
$data = isset($_GET['data']) ? strip_tags($_GET['data']) : '';

if ($data != '') {
	echo '<a href="?">back</a><br><br>';
	$arr = loadData($data);
	$source = loadDataContent($data);

	$nbDimensions = countdim($arr);
	$currentValue = getSelectedValue($arr);
	$currentArray = getSelectedArray($arr);
	$phpcode = getSelectedPhpCode($arr);

	// SOURCE
	echo '<div style="float:left;width:40%;">';
	echo '<span style="border: 4px solid black;padding:4px 10px;border-bottom: 0;">Source</span><br>';
	echo '<pre style="border: 4px solid black;margin-top:4px;"><code class="php">'.htmlentities($source).'</code></pre>';
	echo '</div>';

	echo '<div style="float:left;width:4%;">&nbsp;</div>';

	// PRINT_R
	echo '<div style="float:left;width:40%;">';
	echo '<span style="border: 4px solid black;padding:4px 10px;border-bottom: 0;">print_r</span><br>';
	echo '<pre style="background:black;color:white;padding:8px 10px;margin-top:4px;">'.print_r($arr, 1).'</pre>';
	echo '</div>';

	echo '<div style="float:left;width:4%;">&nbsp;</div>';
	echo '<div style="float:left;width:12%;">';
	echo '<span style="border: 4px solid black;padding:4px 10px;border-bottom: 0;">Legend</span><br>';
	echo '<pre style="background:black;padding:8px 10px;margin-top:4px;">';
	echo '<div class="string">string</div>';
	echo '<div class="int">int</div>';
	echo '<div class="float">float</div>';
	echo '<div class="boolean">boolean</div>';
	echo '<div class="array" style="color:white;">array</div>';
	echo '<div class="object">object</div>';
	echo '<div class="pointer" style="color:red;">pointer</div>';
	echo '</pre>';
	echo '</div>';

	echo '<div style="clear:both;"></div>';

	echo '<a name="graphic">&nbsp;</a><br>';

	// Navigation
	echo '<div style="position: relative">';
	echo '<span style="border: 4px solid black;padding:4px 10px;border-bottom: 0;">Navigation</span><br>';
	echo '<pre style="border: 4px solid black;padding:8px 10px;margin-top:4px;">';
	echo '<span id="currentValue">&nbsp;Current Value&nbsp;</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<strong>Pointer</strong>&nbsp;&nbsp;';
	// for reset
	$pointerArrayReset = $pointerArray;
	$lastPointer = array_pop($pointerArrayReset);
	array_push($pointerArrayReset, 0);
	// for end
	$pointerArrayEnd = $pointerArray;
	$lastPointer = array_pop($pointerArrayEnd);
	array_push($pointerArrayEnd, count($currentArray)-1);
	// for next
	$pointerArrayNext = $pointerArray;
	$lastPointer = array_pop($pointerArrayNext);
	array_push($pointerArrayNext, $lastPointer+1);
	// for prev
	$pointerArrayPrev = $pointerArray;
	$lastPointer = array_pop($pointerArrayPrev);
	array_push($pointerArrayPrev, $lastPointer-1);
	echo '<a href="?data='.$data.'&pointer=' . join('-', $pointerArrayReset) . '#graphic">reset</a>&nbsp;&nbsp;';
	if ($lastPointer > 0) {
		echo '<a href="?data='.$data.'&pointer=' . join('-', $pointerArrayPrev) . '#graphic">prev</a>&nbsp;&nbsp;';
	}
	else {
		echo '<span class="navdisabled">prev</span>&nbsp;&nbsp;';
	}
	if ($lastPointer < count($currentArray)-1) {
		echo '<a href="?data='.$data.'&pointer=' . join('-', $pointerArrayNext) . '#graphic">next</a>&nbsp;&nbsp;';
	}
	else {
		echo '<span class="navdisabled">next</span>&nbsp;&nbsp;';
	}
	echo '<a href="?data='.$data.'&pointer=' . join('-', $pointerArrayEnd) . '#graphic">end</a>&nbsp;&nbsp;';

	if (sizeof($pointerArray) > 1 || is_array($currentValue)) {
		echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Dimension/Level</strong>&nbsp;&nbsp;';
		$pointerArrayCopy = $pointerArray;
		array_pop($pointerArrayCopy);
		if (sizeof($pointerArray) > 1) {
			echo '<a href="?data=' . $data . '&pointer=' . join('-', $pointerArrayCopy) . '#graphic">up</a>&nbsp;&nbsp;';
		}
		else {
			echo '<span class="navdisabled">up</span>&nbsp;&nbsp;';
		}
		if (is_array($currentValue)) {
			echo '<a href="?data=' . $data . '&pointer=' . $pointer . '-0#graphic">down</a>&nbsp;&nbsp;';
		}
		else {
			echo '<span class="navdisabled">down</span>&nbsp;&nbsp;';
		}
	}

	// CURRENT VALUE
	echo '<div id="currentValueBloc">';
	echo '<pre style="border: 4px solid black;margin-top:4px;"><code class="php">'.htmlentities($phpcode).'</code></pre>';
	var_dump($currentValue);
	echo '</div>';

	echo '</div>';

	// GRAPHIC
	echo '<div id="graphicRepresentation">';
	echo graphicalRepresentation($arr);
	echo '</div>';
}
else {
	$dir = dirname(__FILE__).DIRECTORY_SEPARATOR.'sources';
	$availableArrays = array();
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if ($file != '.' && $file != '..') {
					$availableArrays[] = str_replace('.php', '', $file);
				}
			}
			closedir($dh);
		}
	}
	echo '<h3>Sources</h3>';
	echo '<span>in this directory : '.dirname(__FILE__).DIRECTORY_SEPARATOR.'sources'.DIRECTORY_SEPARATOR.'</span>';
	echo '<ul>';
	foreach ($availableArrays as $currentData) {
		echo '<li><a href="?data='.$currentData.'">'.$currentData.'</a></li>';
	}
	echo '</ul>';
}

?>
	<br><br><br>
	<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/highlight.min.js"></script>
	<script type="text/javascript">
		hljs.initHighlightingOnLoad();
		$('#currentValue').mouseover(function (e) {
			e.preventDefault();
			$('#currentValueBloc').show();
		}).mouseout(function (e) {
			e.preventDefault();
			$('#currentValueBloc').hide();
		});
	</script>
</body>
</html>