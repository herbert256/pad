<?php
  include_once PAD.'subs/diffs/_lib/diff.php';
  // Context hunks preserve final-newline state. CRLF is normalized explicitly to LF.
  function padDiffUnifiedHunks($before,$after,$context=3) {
    if(!is_string($before)||!is_string($after)||strlen($before)+strlen($after)>1048576||!preg_match('//u',$before)||!preg_match('//u',$after))throw new InvalidArgumentException('Unified diff needs UTF-8 strings, at most 1 MiB combined');
    if(!is_int($context)||$context<0||$context>100)throw new InvalidArgumentException('Unified diff context must be 0..100');
    $split=function($s){$s=str_replace("\r\n","\n",$s);if($s==='')return [];preg_match_all('/[^\n]*\n|[^\n]+$/',$s,$m);return $m[0];};
    $a=$split($before);$b=$split($after);
    if(count($a)+count($b)>6000)throw new LengthException('Unified diff exceeds 6000 combined lines');
    $ops=padDiffOps($a,$b);$ranges=[];
    foreach($ops as $i=>$op)if($op[0]!=='='){
      $start=max(0,$i-$context);$end=min(count($ops),$i+$context+1);$last=count($ranges)-1;
      if($last>=0&&$start<=$ranges[$last][1])$ranges[$last][1]=max($end,$ranges[$last][1]);else $ranges[]=[$start,$end];
    }
    $old=$new=1;$positions=[];foreach($ops as $op){$positions[]=[$old,$new];if($op[0]!=='+')$old++;if($op[0]!=='-')$new++;}
    $out=[];foreach($ranges as [$start,$end]){
      [$old,$new]=$positions[$start];$lines=array_slice($ops,$start,$end-$start);$oldCount=$newCount=0;
      foreach($lines as $op){$oldCount+=$op[0]!== '+';$newCount+=$op[0]!=='-';}
      $out[]=['old'=>$oldCount?$old:$old-1,'oldCount'=>$oldCount,'new'=>$newCount?$new:$new-1,'newCount'=>$newCount,'lines'=>$lines];
    }return $out;
  }
  function padDiffUnified($before,$after,$options=[]) {
    $hunks=padDiffUnifiedHunks($before,$after,$options['context']??3);
    if(!$hunks)return '<p class="pad-diff">No changes</p>';
    $text='--- '.(($options['from']??'')?:'Before')."\n".'+++ '.(($options['to']??'')?:'After')."\n";
    foreach($hunks as $h){$text.="@@ -{$h['old']},{$h['oldCount']} +{$h['new']},{$h['newCount']} @@\n";
      foreach($h['lines'] as [$op,$line]){$text.=($op==='='?' ':$op).rtrim($line,"\n")."\n";if(!str_ends_with($line,"\n"))$text.="\\ No newline at end of file\n";}
    }
    return padDiffStyle().'<pre class="pad-diff pad-diff-unified" tabindex="0">'.padDiffEscape($text).'</pre>';
  }
?>
