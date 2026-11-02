<?php

  // concordance - Query is a normalized token/phrase (default token mode, 1..32 tokens) or literal substring. Reuses existing normalization and case folding. Returns original UTF-8 byteOffset/byteLength and unchanged matched text; context is bounded in extended grapheme clusters (0..200). Substring matches must begin/end at normalized grapheme boundaries, so case-fold expansion never splits an original grapheme. ≤1 MiB input, 100000 graphemes/tokens, 1024 query bytes and 1000 results; truncated signals further matches. Results are data: consuming HTML widgets must escape left/text/right before highlighting.
  //
  // Runnable example: `apps/tags/examples/textanalysis/concordance.pad`. Independent fixture: `php tests/subsystems/expansion-textanalysis-concordance.php`.

return ['options'=>['normalization','caseFold','locale','query','mode','context','limit'],'call'=>function($text,$options){
 [, $unused]=padTextAnalysisTokens($text,$options);$query=$options['query']??NULL;$mode=$options['mode']??'token';$context=$options['context']??30;$limit=$options['limit']??100;if(!is_string($query)||$query===''||strlen($query)>1024||!mb_check_encoding($query,'UTF-8')||!in_array($mode,['token','substring'],TRUE)||!is_int($context)||$context<0||$context>200||!is_int($limit)||$limit<1||$limit>1000)throw new InvalidArgumentException('query requires 1..1024 UTF-8 bytes, mode token/substring, context 0..200 graphemes, limit 1..1000');
 $normal=function($s)use($options){if(($options['normalization']??'nfc')==='nfc')$s=Normalizer::normalize($s,Normalizer::FORM_C);return ($options['caseFold']??TRUE)?mb_convert_case($s,MB_CASE_FOLD,'UTF-8'):$s;};
 preg_match_all('/\X/u',$text,$gm,PREG_OFFSET_CAPTURE);$graphemes=$gm[0];if(count($graphemes)>100000)throw new InvalidArgumentException('concordance exceeds 100000 graphemes');$starts=array_column($graphemes,1);$starts[]=strlen($text);$ranges=[];$truncated=FALSE;
 if($mode==='token'){
  [, $wanted]=padTextAnalysisTokens($query,$options);if(!$wanted||count($wanted)>32)throw new InvalidArgumentException('token query requires 1..32 tokens');preg_match_all("/[\p{L}\p{N}][\p{L}\p{N}\p{M}]*(?:['’][\p{L}\p{N}][\p{L}\p{N}\p{M}]*)*/u",$text,$tm,PREG_OFFSET_CAPTURE);$tokens=$tm[0];$normalized=array_map(fn($t)=>$normal($t[0]),$tokens);$size=count($wanted);
  for($i=0;$i+$size<=count($tokens);$i++){if(array_slice($normalized,$i,$size)!==$wanted)continue;if(count($ranges)===$limit){$truncated=TRUE;break;}$last=$tokens[$i+$size-1];$ranges[]=[$tokens[$i][1],$last[1]+strlen($last[0])];}
 }else{
  $normalized='';$boundaries=[0=>0];foreach($graphemes as [$g,$at]){$normalized.=$normal($g);$boundaries[strlen($normalized)]=$at+strlen($g);}$needle=$normal($query);if($needle==='')throw new InvalidArgumentException('normalized query is empty');$offset=0;while(($at=strpos($normalized,$needle,$offset))!==FALSE){$end=$at+strlen($needle);$offset=$at+1;if(!isset($boundaries[$at],$boundaries[$end]))continue;if(count($ranges)===$limit){$truncated=TRUE;break;}$ranges[]=[$boundaries[$at],$boundaries[$end]];}
 }
 $floor=function($byte)use($starts){$lo=0;$hi=count($starts);while($lo<$hi){$mid=intdiv($lo+$hi,2);if($starts[$mid]<=$byte)$lo=$mid+1;else $hi=$mid;}return max(0,$lo-1);};$matches=[];foreach($ranges as [$start,$end]){$left=$starts[max(0,$floor($start)-$context)];$right=$starts[min(count($starts)-1,$floor($end)+$context)];$matches[]=['byteOffset'=>$start,'byteLength'=>$end-$start,'left'=>substr($text,$left,$start-$left),'text'=>substr($text,$start,$end-$start),'right'=>substr($text,$end,$right-$end)];}
 return ['mode'=>$mode,'matches'=>$matches,'truncated'=>$truncated,'offsetUnit'=>'original UTF-8 bytes','contextUnit'=>'extended grapheme clusters'];
}];
