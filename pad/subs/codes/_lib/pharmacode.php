<?php
// Original encoding from bijective base-two: n+1 in binary without its leading one.
function padPharmacode($text){
 if(!is_string($text)||!preg_match('/^[0-9]{1,6}$/D',$text)||(int)$text<3||(int)$text>131070)return 'Pharmacode needs an integer from 3 to 131070';
 $digits=substr(decbin((int)$text+1),1);$bars=implode('00',array_map(fn($d)=>$d==='0'?'1':'111',str_split($digits)));
 return [$bars,[],[],12,0];
}
function padPharmacodeSvg($text,$height=8,$title=''){
 $code=padPharmacode($text);if(is_string($code))throw new InvalidArgumentException($code);
 if(!is_numeric($height)||!is_finite((float)$height)||$height<8||$height>100)throw new InvalidArgumentException('Pharmacode height must be 8..100 millimetres');
 $svg=padBarcode($code,(float)$height,.5,$title?:'Pharmacode '.(int)$text,TRUE,'#000000','#ffffff');
 return preg_replace('/ (width|height)="([0-9.]+)"/', ' $1="$2mm"',$svg,2);
}
