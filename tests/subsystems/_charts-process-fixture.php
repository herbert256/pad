<?php
define('PAD',dirname(__DIR__,2).'/pad/');function padDirList($p){return scandir($p);}function padTagParm($k,$d=''){return $GLOBALS['opts'][$k]??$d;}function padError($m){throw new InvalidArgumentException($m);}function padWidgetOnce($k){return FALSE;}function padNonce(){return '';}function padStyleNonce(){return '';}function padFileGet($p){return file_get_contents($p);}
require PAD.'lib/encoding.php';require PAD.'lib/chart.php';require PAD.'subs/charts/_lib/chart.php';
$input=json_decode(stream_get_contents(STDIN),TRUE);$opts=$input['params']??[];$padCheckSyntax=TRUE;
try{echo call_user_func('padChart'.ucfirst($input['kind']),$input['rows'],600,320);}catch(InvalidArgumentException $e){echo 'ERROR:'.$e->getMessage();}
