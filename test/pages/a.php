<?php 

    $csv = "volume,edition,file
  55,3,file1.xml
  33,1,file2.xml
  55,2,file11.xml
  33,3,file12.xml
  55,1,file21.xml
  33,2,file8.xml";

    $result = [];
  
	$enc = preg_replace_callback(
	  '/"(.*?)"/s',
	  function ($field) {
	      return urlencode(utf8_encode($field[1]));
	  },
	  preg_replace('/(?<!")""/', '!!Q!!', trim($csv))
	);

	$lines  = preg_split('/( *\R)+/s', $enc);
	$header = explode(',', $lines [0]);

	foreach ($header as $key1 => $val1)
	$header [$key1] = trim(str_replace('!!Q!!', '"', urldecode($val1)));

	foreach ($lines as $key1 => $val1)
	if ($key1 > 0)
	  foreach (explode(',', $val1) as $key2 => $val2)
	    $result [$key1] [$header[$key2]] = trim(str_replace('!!Q!!', '"', urldecode($val2)));

$result2 = xxx($csv);

echo '<pre>';
print_r($result);
echo '<hr>';
print_r($result2);
echo '<hr>';

function xxx ( $csv ) {

    $result = [];
  
	$enc = preg_replace_callback(
	  '/"(.*?)"/s',
	  function ($field) {
	      return urlencode(utf8_encode($field[1]));
	  },
	  preg_replace('/(?<!")""/', '!!Q!!', trim($csv))
	);

	$lines  = preg_split('/( *\R)+/s', $enc);
	$header = explode(',', $lines [0]);

	foreach ($header as $key1 => $val1)
	$header [$key1] = trim(str_replace('!!Q!!', '"', urldecode($val1)));

	foreach ($lines as $key1 => $val1)
	if ($key1 > 0)
	  foreach (explode(',', $val1) as $key2 => $val2)
	    $result [$key1] [$header[$key2]] = trim(str_replace('!!Q!!', '"', urldecode($val2)));

	return $result;

}


?>