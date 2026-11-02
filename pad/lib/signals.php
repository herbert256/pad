<?php
function padSignal($kind,$input,$options=[]){
 include_once PAD.'subs/signals/_lib/signals.php';
 try{if(!is_string($kind)||!padKind('signals',$kind)||!is_array($options))throw new InvalidArgumentException('expected a signal kind and options array');$type=include PAD."subs/signals/types/$kind.php";if(array_diff(array_keys($options),array_merge(['maxWork'],$type['options'])))throw new InvalidArgumentException('unknown signal option');$max=$options['maxWork']??1000000;if(!is_int($max)||$max<1||$max>10000000)throw new InvalidArgumentException('maxWork must be 1..10000000');$state=['work'=>0,'maxWork'=>$max];try{$out=$type['call']($input,$options,$state);}catch(PadSignalLimit $e){$out=['status'=>'limit','values'=>[]];}padSignalResultFinite($out);return $out+['work'=>$state['work'],'maxWork'=>$max];}catch(InvalidArgumentException $e){padError('padSignal: '.$e->getMessage());return NULL;}
}
