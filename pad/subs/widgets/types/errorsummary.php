<?php
// errors= maps stable field IDs to plain messages. No automatic focus stealing.
$errors=padTagParm('errors',$GLOBALS['padFormErrors']??[]);$fields=padTagParm('fields',[]);if(!is_array($fields))$fields=[];if(!is_array($errors)){padError('Error summary needs an ID to message map');return '';}
if(!$errors)return '';$e=fn($v)=>htmlspecialchars((string)$v,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');$items='';
foreach($errors as $id=>$message){if(!is_scalar($message))continue;$text=$e($message);$id=$fields[$id]??padFormId((string)$id,[]);if(preg_match('/^[A-Za-z][A-Za-z0-9_:.-]*$/D',(string)$id))$text='<a href="#'.$e($id).'">'.$text.'</a>';$items.='<li>'.$text.'</li>';}
return padWidgetSheet(padWidgetFile('widgets/errorsummary.css')).'<section class="pad-errorsummary" role="alert" tabindex="-1"><h2>'.$e(padWidgetText('title','Please correct these errors')).'</h2><ul>'.$items.'</ul></section>';
?>
