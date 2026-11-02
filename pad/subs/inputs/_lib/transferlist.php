<?php
function padInputTransferList($ids,$choices,$ordered=FALSE,$order=[]){
 if(!is_array($choices)||count($choices)>500||!is_array($ids)||!array_is_list($ids)||count($ids)>500||!is_array($order))throw new InvalidArgumentException('transferlist needs <=500 choices and a submitted ID list');$known=[];foreach($choices as $key=>$label){if(!preg_match('/^[A-Za-z0-9_-]{1,64}$/D',(string)$key)||!is_scalar($label))throw new InvalidArgumentException('transferlist IDs are 1..64 letters/digits/_/-');$known[(string)$key]=TRUE;}
 $out=[];foreach($ids as $id){if(!is_scalar($id)||!isset($known[(string)$id])||in_array((string)$id,$out,TRUE))throw new InvalidArgumentException('Unknown or duplicate transferlist ID');$out[]=(string)$id;}
 if($ordered&&$order){$ranks=[];foreach($out as $id){$rank=$order[$id]??NULL;if(filter_var($rank,FILTER_VALIDATE_INT)===FALSE||$rank<1||$rank>500||in_array((int)$rank,$ranks,TRUE))throw new InvalidArgumentException('Chosen positions must be unique integers 1..500');$ranks[$id]=(int)$rank;}usort($out,fn($a,$b)=>$ranks[$a]<=>$ranks[$b]);}elseif(!$ordered)sort($out,SORT_STRING);return $out;
}
