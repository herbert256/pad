<?php
$manifest=padTagParm('manifest',NULL);
if(is_string($manifest)&&preg_match('/^[A-Za-z][A-Za-z0-9_]*$/D',$manifest)&&!str_starts_with(strtolower($manifest),'pad'))$manifest=$GLOBALS[$manifest]??NULL;
try{return padImageSprite($manifest,padParmText($padParm),padTagParm('alt',NULL));}
catch(InvalidArgumentException $e){if($padCheckSyntax)padError($e->getMessage());return '';}
