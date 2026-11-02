<?php
$padInspectorValue=function($v){if(is_string($v)&&padArrayCheck($v))return padArrayValue($v);return $v;};
try{return padWidgetInspector($padInspectorValue(padTagParm('record',[])),$padInspectorValue(padTagParm('fields',[])));}catch(Exception $e){padError($e->getMessage());return '';}
