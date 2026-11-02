<?php
include_once PAD.'subs/sections/_lib/contents.php';
padSectionParts(['eyebrow','title','text']);$padSecHead=padSectionHead();$padSecItems=padSectionItems('name',['name','link','text','category'],'text','entries');$padSecRawItems=[];foreach($padSecItems as $item){$row=[];foreach(['name','link','text','category'] as $field)$row[$field]=padSectionRaw($item,$field);$padSecRawItems[]=$row;}
try{return padSection('contents',$padSecHead.padSectionContents($padSecRawItems));}catch(Exception $e){padError($e->getMessage());return '';}
