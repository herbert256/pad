<?php

  // inspector - `record=` is an array; `fields=` explicitly maps property names to labels or metadata `{label,unit,action,actionlabel}`. Maximum 100 fields. Absent, null and empty values remain distinct; object properties are never introspected. Nested arrays use existing jsonview disclosures. Scalar values escape; actions accept http(s), local-root, query and fragment URLs. No editing is inferred.
  //
  // ## Example
  //
  // The paired PHP page supplies the application data:
  //
  // ```php
  // <?php
  // $record=['name'=>'Weather station','height'=>42.5,'status'=>NULL,'location'=>['city'=>'Amsterdam','country'=>'NL']];$fields=['name'=>'Name','height'=>['label'=>'Height','unit'=>'m'],'status'=>'Status','missing'=>'Unreported','location'=>'Location'];
  // ```
  //
  // The PAD template renders it:
  //
  // ```pad
  // {widget:inspector record='record', fields='fields'/}
  // ```
  //
  // Run the focused fixture with `php tests/subsystems/widget-inspector.php`.
  //
  // Array-valued options accept a literal array or the quoted name of an application array/store.

$padInspectorValue=function($v){if(is_string($v)&&padArrayCheck($v))return padArrayValue($v);return $v;};
try{return padWidgetInspector($padInspectorValue(padTagParm('record',[])),$padInspectorValue(padTagParm('fields',[])));}catch(Exception $e){padError($e->getMessage());return '';}
