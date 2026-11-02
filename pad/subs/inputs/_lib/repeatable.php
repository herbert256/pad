<?php
function padRepeatableError($name,$message){
 $GLOBALS['padFormErrors'][$name]=$message;return [$name=>$message];
}
// Explicitly expand bracketed row addresses, so each message belongs to its real control.
function padValidateRows($name,$rules,$data=NULL,$limit=100){
 $data??=$_POST; [$found,$rows]=padFormFind($data,$name);
 if(!$found)$rows=[];
 if(!is_array($rows)||count($rows)>$limit)return padRepeatableError($name,'Too many or invalid rows.');
 $expanded=[];
 foreach($rows as $index=>$row){
  if(!ctype_digit((string)$index)||!is_array($row))return padRepeatableError($name,'Invalid row.');
  foreach($rules as $field=>$rule){
   if(!preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/D',$field))throw new InvalidArgumentException('Invalid repeatable column');
   $expanded[$name.'['.$index.']['.$field.']']=$rule;
  }
 }
 return padValidate($expanded,$data);
}
?>
