<?php
  define ( 'PAD', dirname ( __DIR__, 2 ) . '/pad/' );
  function padDirList ( $p ) { return scandir ( $p ); }
  function padError ( $m ) { throw new InvalidArgumentException ( $m ); }
  function padMakeSafe ( $s, $n ) { return substr($s,0,$n); }
  $padCheckSyntax = TRUE;
  require PAD . 'lib/subsystem.php'; require PAD . 'subs/filters/_lib/filter.php';
  function check ( $v ) { if ( ! $v ) throw new Exception ( 'filter fixture failed' ); }
  function pixelImage($pixel) { $i=imagecreatetruecolor(1,1); imagealphablending($i,false); imagesetpixel($i,0,0,$pixel); return $i; }
  $def=padFilterKind('colormatrix'); $identity=$def['options']['matrix'][1]; $pixel=(63<<24)|0x4080ff;
  foreach([1,2] as $scale) check(imagecolorat($def['apply'](pixelImage($pixel),['matrix'=>$identity],$scale),0,0)===$pixel);
  $swap=[0,0,1,0,0, 0,1,0,0,0, 1,0,0,0,0, 0,0,0,.5,0];
  $out=imagecolorat($def['apply'](pixelImage($pixel),['matrix'=>$swap],1),0,0);
  check(($out&0xffffff)===0xff8040 and ($out>>24)===95);
  check(padFilterOption('colormatrix','matrix',$def['options']['matrix'],implode(',',$identity)) === array_map('floatval',$identity));
  foreach([[1],array_fill(0,20,INF),array_fill(0,20,11)] as $bad) { try { padFilterOption('colormatrix','matrix',$def['options']['matrix'],$bad); throw new Exception('invalid matrix accepted'); } catch(InvalidArgumentException $e) {} }
  check(padFilterKey([['colormatrix',$def,['matrix'=>$identity]]])!==padFilterKey([['colormatrix',$def,['matrix'=>$swap]]]));
  echo "filter:colormatrix pixel, alpha and option fixtures passed\n";
?>
