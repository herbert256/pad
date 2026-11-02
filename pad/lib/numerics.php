<?php
// Bounded deterministic methods; only trusted PHP callables reach the root solver.
function padNumeric($kind,$input,$options=[]){
 include_once PAD.'subs/numerics/_lib/numerics.php';
 try{if(!is_string($kind)||!padKind('numerics',$kind)||!is_array($options))throw new InvalidArgumentException('expected a numeric kind and options array');$definition=include PAD."subs/numerics/types/$kind.php";if(array_diff(array_keys($options),array_merge(['maxWork'],$definition['options'])))throw new InvalidArgumentException('unknown numeric option');$max=$options['maxWork']??1000000;if(!is_int($max)||$max<1||$max>10000000)throw new InvalidArgumentException('maxWork must be 1..10000000');$state=['work'=>0,'maxWork'=>$max];try{$result=$definition['call']($input,$options,$state);}catch(PadNumericLimit $e){$result=['status'=>'limit','value'=>NULL,'iterations'=>NULL];}return $result+['work'=>$state['work'],'maxWork'=>$max];}catch(InvalidArgumentException $e){padError('padNumeric: '.$e->getMessage());return NULL;}
}
