<?php
  function padJsoncRead($text,$options=[]) {
    if (strlen($text)>1048576) padFormatFail(0,'JSONC exceeds 1 MiB');
    if (array_diff(array_keys($options),['trailing'])) padFormatFail(0,'JSONC accepts only trailing');
    $trailing=$options['trailing']??TRUE;
    if (!is_bool($trailing)) padFormatFail(0,'trailing must be boolean');
    $out=$text; $n=strlen($text); $string=FALSE;
    for($i=0;$i<$n;$i++) {
      if($string) { if($text[$i]==='\\') $i++; elseif($text[$i]==='"') $string=FALSE; continue; }
      if($text[$i]==='"') {$string=TRUE;continue;}
      if($text[$i]!=='/'||$i+1>=$n)continue;
      $start=$i;
      if($text[$i+1]==='/') { $i+=2;while($i<$n&&$text[$i]!=="\n"&&$text[$i]!=="\r")$out[$i++]=' ';$out[$start]=$out[$start+1]=' ';$i--; }
      elseif($text[$i+1]==='*') {
        $end=strpos($text,'*/',$i+2);if($end===FALSE)padFormatFail(padFormatLineAt($text,$i),'Unterminated JSONC comment');
        for($j=$i;$j<$end+2;$j++)if($text[$j]!=="\n"&&$text[$j]!=="\r")$out[$j]=' ';
        $i=$end+1;
      }
    }
    if($trailing) {
      $string=FALSE;
      for($i=0;$i<$n;$i++) {
        if($string){if($out[$i]==='\\')$i++;elseif($out[$i]==='"')$string=FALSE;continue;}
        if($out[$i]==='"'){$string=TRUE;continue;}
        if($out[$i]===',') { $j=$i+1;while($j<$n&&str_contains(" \t\r\n",$out[$j]))$j++;
          $k=$i-1;while($k>=0&&str_contains(" \t\r\n",$out[$k]))$k--;
          if($j<$n&&($out[$j]===']'||$out[$j]==='}')&&$k>=0&&!str_contains('[{:,' ,$out[$k]))$out[$i]=' ';
        }
      }
    }
    try{return json_decode($out,TRUE,64,JSON_THROW_ON_ERROR);}
    catch(JsonException $e){padFormatFail(0,'Invalid JSONC: '.$e->getMessage());}
  }
?>
