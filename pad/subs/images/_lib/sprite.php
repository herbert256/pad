<?php
function padImageSpriteBox($manifest,$name,$width,$height){
 if(!is_array($manifest)||!is_array($manifest['sprites']??NULL)||count($manifest['sprites'])>512||!is_string($name)||!isset($manifest['sprites'][$name]))throw new InvalidArgumentException('Sprite needs a manifest with at most 512 named crops');
 $box=$manifest['sprites'][$name];if(!is_array($box)||count($box)!==4)throw new InvalidArgumentException('Sprite crop is [x,y,width,height]');$box=array_values($box);
 foreach($box as $v)if(!is_int($v))throw new InvalidArgumentException('Sprite crop coordinates must be integers');
 [$x,$y,$w,$h]=$box;if($x<0||$y<0||$w<1||$h<1||$x+$w>$width||$y+$h>$height)throw new InvalidArgumentException('Sprite crop lies outside atlas');
 $dpr=$manifest['dpr']??1;if(!is_numeric($dpr)||!is_finite((float)$dpr)||$dpr<1||$dpr>4)throw new InvalidArgumentException('Sprite DPR must be 1..4');return [...$box,(float)$dpr];
}
function padImageSprite($manifest,$name,$alt){
 if(!is_string($alt))throw new InvalidArgumentException('Sprite alt must be text, empty for decoration');
 if(!is_array($manifest)||!is_string($manifest['source']??NULL))throw new InvalidArgumentException('Sprite manifest needs a local source');
 $source=padImgSource($manifest['source']);if(!$source)return '';[$path,$file,$type,$sw,$sh,$turn]=$source;
 if($turn!==1)throw new InvalidArgumentException('Sprite atlas must have normalized orientation');
 [$x,$y,$w,$h,$dpr]=padImageSpriteBox($manifest,$name,$sw,$sh);$label=padImgAttr($alt);$role=$alt===''?'aria-hidden="true"':'role="img" aria-label="'.$label.'"';
 return '<svg xmlns="http://www.w3.org/2000/svg" class="pad-image-sprite" '.$role.' width="'.($w/$dpr).'" height="'.($h/$dpr).'" viewBox="'.$x.' '.$y.' '.$w.' '.$h.'" overflow="hidden">'.($alt!==''?'<title>'.$label.'</title>':'').'<image href="'.padImgAttr(padAsset($file)).'" x="0" y="0" width="'.$sw.'" height="'.$sh.'"/></svg>';
}
