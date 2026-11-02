<?php

  // imagecrop - `image=` is trusted server metadata `{id,width,height,orientation}` (EXIF 1..8); `src=` is the preview URL. Coordinates use integer pixels after orientation; orientations 5..8 swap dimensions. Native number controls support keyboard, reset and no-JS submission. JS previews the crop on canvas without reading/uploading pixels. `padInputCrop($posted,$trustedImage)` revalidates image identity and complete bounds and rejects extra fields. Obtain dimensions/EXIF from the real image, never posted metadata; normalize orientation in the image pipeline before applying the returned crop. Persistence is application-owned.
  //
  // ## Example
  //
  // The paired PHP page supplies the application data:
  //
  // ```php
  // <?php
  // $cropImage=['id'=>'pad-logo','width'=>240,'height'=>240,'orientation'=>1];
  // $cropSrc=$padRoot.'showcase/photos/logo.jpg';
  // ```
  //
  // The PAD template renders it:
  //
  // ```pad
  // {input:form 'imagecrop'}{input:imagecrop 'crop', image=$cropImage, src=$cropSrc, label='Crop image'/}<button type='submit'>Apply</button><button type='reset'>Reset</button>{/input:form}
  // ```
  //
  // Run the focused fixture with `php tests/subsystems/input-imagecrop.php`.

include_once PAD.'subs/inputs/_lib/imagecrop.php';
$image=padInputTake('image',[]);$url=padInputTake('src','');try{$oriented=padInputCropImage($image);if(!is_string($url)||!preg_match('~^(?:https?://[^\s]+|/(?!/)[^\s]*)$~D',$url))throw new InvalidArgumentException('imagecrop src must be HTTP(S) or a local root URL');}catch(Exception $e){padError($e->getMessage());return '';}
$given=padInputTake('value',['image'=>$oriented['id'],'x'=>0,'y'=>0,'width'=>$oriented['width'],'height'=>$oriented['height']]);$source=padFormSource();if($source!==NULL){[$found,$posted]=padFormFind($source,padInputName());if($found)$given=$posted;}$invalid='';try{padInputCrop($given,$image);}catch(Exception $e){$invalid=$e->getMessage();}if(!is_array($given))$given=[];
return padInputField(function($head,$rest,$label,$error,$id,$worded)use($given,$invalid,$oriented,$url){$name=padInputName();$disabled=isset($GLOBALS['padInputNow']['attrs']['disabled']);$h='<fieldset id="'.padInputEscape($id).'"'.($disabled?' disabled':'').'><legend>'.padInputEscape($worded).'</legend><img data-crop-image src="'.padInputEscape($url).'" alt="Image to crop"><canvas hidden aria-label="Crop preview"></canvas><input type="hidden" name="'.padInputEscape($name.'[image]').'" value="'.padInputEscape(is_scalar($given['image']??NULL)?$given['image']:'').'">';foreach(['x','y','width','height'] as $key){$cid=$id.'-'.$key;$max=$oriented[in_array($key,['x','width'],TRUE)?'width':'height'];$h.='<label for="'.padInputEscape($cid).'">'.ucfirst($key).' (pixels)</label><input data-crop-axis="'.$key.'" id="'.padInputEscape($cid).'" type="number" min="'.(in_array($key,['x','y'],TRUE)?0:1).'" max="'.$max.'" step="1" name="'.padInputEscape($name.'['.$key.']').'" value="'.padInputEscape(is_scalar($given[$key]??NULL)?$given[$key]:'').'">';}$h.='<p>Coordinates refer to the image after EXIF orientation.</p><span role="status">'.padInputEscape($invalid).'</span></fieldset>';return padInputFrame('imagecrop',$worded,$h,$error,$oriented);});
