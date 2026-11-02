<?php
function padInputCropImage($image){
 if(!is_array($image)||!is_string($image['id']??NULL)||$image['id']===''||strlen($image['id'])>200)throw new InvalidArgumentException('imagecrop needs an identified image');foreach(['width','height'] as $key)if(!is_int($image[$key]??NULL)||$image[$key]<1||$image[$key]>100000)throw new InvalidArgumentException('imagecrop dimensions must be 1..100000');$o=$image['orientation']??1;if(!is_int($o)||$o<1||$o>8)throw new InvalidArgumentException('imagecrop EXIF orientation must be 1..8');return ['id'=>$image['id'],'width'=>$image[$o>=5?'height':'width'],'height'=>$image[$o>=5?'width':'height'],'orientation'=>$o];
}
function padInputCrop($value,$image){
 $image=padInputCropImage($image);if(!is_array($value)||array_diff(array_keys($value),['image','x','y','width','height'])||($value['image']??NULL)!==$image['id'])throw new InvalidArgumentException('Crop image identity does not match');$crop=[];foreach(['x','y','width','height'] as $key){$v=$value[$key]??NULL;if(!is_scalar($v)||filter_var($v,FILTER_VALIDATE_INT)===FALSE)throw new InvalidArgumentException('Crop coordinates must be whole pixels');$crop[$key]=(int)$v;}
 if($crop['x']<0||$crop['y']<0||$crop['width']<1||$crop['height']<1||$crop['x']>$image['width']-$crop['width']||$crop['y']>$image['height']-$crop['height'])throw new InvalidArgumentException('Crop lies outside the oriented image');return ['image'=>$image['id']]+$crop;
}
