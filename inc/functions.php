<?php

function loadData($dataName) {
	$filePath = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'sources' . DIRECTORY_SEPARATOR . $dataName . '.php';
	if (file_exists($filePath)) {
		require $filePath;
		if (isset($data)) {
			return $data;
		}
	}
	return false;
}

function loadDataContent($dataName) {
	$filePath = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'sources' . DIRECTORY_SEPARATOR . $dataName . '.php';
	if (file_exists($filePath)) {
		return file_get_contents($filePath);
	}
	return false;
}

function debug($var) {
	echo '<pre style="background:black;color:white;padding:8px 10px;">' . print_r($var, 1) . '</pre>';
}

function countdim($array) {
	if (is_array(reset($array))) {
		$return = countdim(reset($array)) + 1;
	}

	else {
		$return = 1;
	}

	return $return;
}

function graphicalRepresentation($var, $dimension = 0, $pointed = false) {
	global $pointerArray;

	$str = '';
	$pointedCss = $pointed ? ' pointer' : '';
	if (is_array($var)) {
		$str .= '<table cellpadding="0" class="array">
			<thead>
				<tr>
					<th>Index/Key</th>
					<th>Value</th>
				</tr>
			</thead>
			<tbody>
		';

		$i = 0;
		foreach ($var as $key => $value) {
			$pointedNew = isset($pointerArray[$dimension]) && ($pointed || $dimension==0) && $i == $pointerArray[$dimension];
			$str .= '<tr>
				<td>' . graphicalRepresentation($key, $dimension + 1, $pointedNew) . ' </td>
				<td>' . graphicalRepresentation($value, $dimension + 1, $pointedNew) . '</td>
			</tr>';
			$i++;
		}

		$str .= '
			</tbody>
		</table>';
	}
	else if (is_object($var)) {
		$str = '<div class="object' . $pointedCss . '">object</div>';
	}
	else if (is_numeric($var)) {
		if (is_float($var)) {
			$str = '<div class="float' . $pointedCss . '">' . $var . '</div>';
		}
		else {
			$str = '<div class="int' . $pointedCss . '">' . $var . '</div>';
		}
	}
	else if (is_bool($var)) {
		if ($var === false) {
			$str = '<div class="boolean' . $pointedCss . '">false</div>';
		}
		elseif ($var === true) {
			$str = '<div class="boolean' . $pointedCss . '">true</div>';
		}
		else {
			$str = '<div class="boolean' . $pointedCss . '">invalid boolean</div>';
		}
	}
	else if (is_string($var)) {
		$str = '<div class="string' . $pointedCss . '">' . $var . '</div>';
	}
	else {
		$str = $var . ' (unknown)';
	}

	return $str;
}

function getSelectedValue($var, $dimension = 0) {
	global $pointerArray;

	if (is_array($var) && isset($pointerArray[$dimension])) {
		$i = 0;
		foreach ($var as $key => $value) {
			if ($i == $pointerArray[$dimension]) {
				return getSelectedValue($value, $dimension+1);
			}
			$i++;
		}
	}
	else {
		return $var;
	}
}

function getSelectedArray($var, $dimension = 0) {
	global $pointerArray;

	if (is_array($var) && isset($pointerArray[$dimension]) && $dimension < (sizeof($pointerArray)-1)) {
		$i = 0;
		foreach ($var as $key => $value) {
			if ($i == $pointerArray[$dimension]) {
				return getSelectedArray($value, $dimension+1);
			}
			$i++;
		}
	}
	else {
		return $var;
	}
}

function getSelectedPhpCode($var, $dimension = 0) {
	global $pointerArray;

	$code = '';
	if ($dimension == 0) {
		$code = '$myArray';
	}

	if (is_array($var) && isset($pointerArray[$dimension])) {
		$i = 0;
		foreach ($var as $key => $value) {
			if ($i == $pointerArray[$dimension]) {
				$phpKey = '';
				if (is_numeric($key)) {
					$phpKey = $key;
				}
				else if (is_string($key)) {
					$phpKey = '\''.stripslashes($key).'\'';
				}
				$code .= '['.$phpKey.']'. getSelectedPhpCode($value, $dimension+1);
			}
			$i++;
		}
	}
	else {
		return '';
	}

	return $code;
}