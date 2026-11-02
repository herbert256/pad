<?php

  // transferlist - `choices=` maps stable IDs to labels (max 500; IDs letters/digits/_/-), value is an ID list. Native multiple select works without JS; ordered=1 adds numeric positions. JS supplies Available/Chosen lists and Add/Remove/Move buttons, all keyboard accessible. Submit IDs as name[] and positions as name_order[id]. Validate with `padInputTransferList($ids,$choices,$ordered,$positions)`; it rejects duplicate/unknown IDs and invalid/tied positions. Set selections sort IDs; ordered selections retain chosen order. Reset restores defaults.
  //
  // ## Example
  //
  // The paired PHP page supplies the application data:
  //
  // ```php
  // <?php
  // $memberChoices=['ada'=>'Ada','bo'=>'Bo','cy'=>'Cy'];$chosenMembers=['bo'];
  // ```
  //
  // The PAD template renders it:
  //
  // ```pad
  // {input:form 'transferlist'}{input:transferlist 'members', choices=$memberChoices, value=$chosenMembers, ordered=1, label='Members'/}<button type='submit'>Save</button><button type='reset'>Reset</button>{/input:form}
  // ```
  //
  // Run the focused fixture with `php tests/subsystems/input-transferlist.php`.

include_once PAD.'subs/inputs/_lib/transferlist.php';
$choices=padInputTake('choices',[]);$ordered=(bool)padInputTake('ordered',FALSE);$given=padInputValues(padInputTake('value',[]));$invalid='';$order=[];$source=padFormSource();if($source!==NULL){[$found,$postedOrder]=padFormFind($source,rtrim(padInputName(),'[]').'_order');if($found)$order=$postedOrder;}try{$given=padInputTransferList($given,$choices,$ordered,$order);}catch(Exception $e){$invalid=$e->getMessage();}if(!is_array($choices))$choices=[];
return padInputField(function($head,$rest,$label,$error,$id,$worded)use($choices,$given,$ordered,$invalid){$name=rtrim(padInputName(),'[]');$head=preg_replace('/(?:^|\s)name="[^"]*"/','',$head);$h=$label.'<select '.$head.$rest.' multiple name="'.padInputEscape($name.'[]').'" data-transfer-source>';foreach($choices as $key=>$text)$h.='<option value="'.padInputEscape($key).'"'.(in_array((string)$key,array_map('strval',$given),TRUE)?' selected':'').'>'.padInputEscape($text).'</option>';$h.='</select>'.padInputSentField($name,isset($GLOBALS['padInputNow']['attrs']['disabled']));if($ordered){$h.='<fieldset'.(isset($GLOBALS['padInputNow']['attrs']['disabled'])?' disabled':'').' data-transfer-order><legend>Chosen positions</legend>';foreach($choices as $key=>$text){$at=array_search((string)$key,array_map('strval',$given),TRUE);$h.='<label>'.padInputEscape($text).' <input type="number" min="1" max="500" name="'.padInputEscape($name.'_order['.$key.']').'" value="'.($at===FALSE?'':$at+1).'" data-id="'.padInputEscape($key).'"></label> ';}$h.='</fieldset>';}$h.='<p>Select items; use positions to order chosen IDs.</p><span role="status">'.padInputEscape($invalid).'</span>';return padInputFrame('transferlist',$worded,$h,$error,['ordered'=>$ordered]);});
