<?php
include_once PAD.'subs/embeds/_lib/sandbox.php';
return ['name'=>'HTML demonstration','kind'=>'code','options'=>['height','scripts'],'match'=>fn($source,$named)=>$named?['html'=>$source]:NULL,'embed'=>fn($parts,$opts)=>['mode'=>'sandbox','src'=>$parts['html']]];
