<?php

  // repeatable - Editable rows of named fields with add and remove controls and per-field validation.
  //

$columns=padInputTake('columns',['description'=>'Description','quantity'=>'Quantity']);
if(!is_array($columns)||!$columns||count($columns)>12){padError('repeatable requires 1 to 12 named columns');return '';}
foreach($columns as $key=>$title) if(!preg_match('/^[A-Za-z][A-Za-z0-9_]*$/D',(string)$key)||!is_scalar($title)){padError('Invalid repeatable column');return '';}
$max=padInputNumber('maxrows',20,1,100,TRUE);
$min=padInputNumber('minrows',1,0,$max,TRUE);
$rows=padInputTake('value',[]);$source=padFormSource();
if($source!==NULL){[$found,$posted]=padFormFind($source,padInputName());if($found)$rows=$posted;elseif(padInputSent($source,padInputName()))$rows=[];}
if(!is_array($rows))$rows=[];
$rows=array_slice(array_filter($rows,fn($k)=>ctype_digit((string)$k),ARRAY_FILTER_USE_KEY),0,$max,TRUE);
while(count($rows)<$min)$rows[]=[];
return padInputField(function($head,$rest,$label,$error,$id,$worded)use($columns,$rows,$min,$max){
 $name=padInputName();$disabled=isset($GLOBALS['padInputNow']['attrs']['disabled']);
 $row=function($values,$index,$template=FALSE)use($columns,$name,$id){
  $out='<fieldset class="pi-repeat-row"><legend>Row <span>'.($template?'new':$index+1).'</span></legend>';
  foreach($columns as $key=>$title){
   $field=$name.'['.$index.']['.$key.']';$inputId=$id.'-'.$index.'-'.$key;
   $message=$template?'':padFormError($field,(string)$title);
   $val=is_array($values)&&is_scalar($values[$key]??'')?(string)($values[$key]??''):'';
   $out.='<label for="'.padInputEscape($inputId).'">'.padInputEscape($title).'</label><input type="text" id="'.padInputEscape($inputId).'" name="'.padInputEscape($field).'" value="'.padInputEscape($val).'"'.($message!==''?' aria-invalid="true" aria-describedby="'.padInputEscape($inputId).'-error"':'').'>';
   if($message!=='')$out.='<span id="'.padInputEscape($inputId).'-error">'.padInputEscape($message).'</span>';
  }
  return $out.'<button type="button" data-remove hidden>Remove row</button></fieldset>';
 };
 $html='<fieldset'.($disabled?' disabled':'').'><legend>'.padInputEscape($worded).'</legend><div data-rows>';
 foreach($rows as $i=>$values)$html.=$row($values,$i);
 $html.='</div><template>'.$row([],'__INDEX__',TRUE).'</template><button type="button" data-add hidden>Add row</button><span role="status"></span>'.padInputSentField($name,$disabled).'</fieldset>';
 return padInputFrame('repeatable',$worded,$html,$error,['min'=>$min,'max'=>$max,'next'=>$rows?max(array_keys($rows))+1:0]);
});
?>
