<?php
include_once PAD.'subs/codes/_lib/pharmacode.php';
try{return padPharmacodeSvg(padCodeText(FALSE),padTagParm('height',8),padParmText(padTagParm('title','')));}
catch(InvalidArgumentException $e){if($padCheckSyntax)padError($e->getMessage());return '';}
