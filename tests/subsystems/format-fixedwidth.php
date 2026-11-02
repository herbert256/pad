<?php
  define ( 'PAD', dirname ( __DIR__, 2 ) . '/pad/' );
  function padDirList ( $p ) { return scandir ( $p ); }
  function padError ( $m ) { throw new InvalidArgumentException ( $m ); }
  function padMakeSafe ( $s, $n ) { return substr($s,0,$n); }
  $padCheckSyntax = TRUE;
  require PAD . 'lib/subsystem.php'; require PAD . 'lib/format.php';
  function check ( $v ) { if ( ! $v ) throw new Exception ( 'format fixture failed' ); }
  $options = ['columns'=>['name'=>4,'id'=>['width'=>3,'align'=>'right']]];
  $rows = [['name'=>' ab','id'=>'42'],['name'=>'é','id'=>'7']];
  $text = padFormatWrite('fixedwidth',$rows,$options);
  check($text === " ab  42\né    7\n");
  check(padFormatRead('fixedwidth',$text,$options) === $rows);
  check(padFormatRead('fixedwidth','', $options) === []);
  foreach ( [ ['abc', $options], ['abc', ['columns'=>['x'=>0]]] ] as [$text,$opts] ) {
    try { padFormatRead('fixedwidth',$text,$opts); throw new Exception('invalid record accepted'); } catch(InvalidArgumentException $e) {}
  }
  try { padFormatWrite('fixedwidth',[['name'=>'abcde','id'=>1]],$options); throw new Exception('overflow accepted'); } catch(InvalidArgumentException $e) {}
  echo "format:fixedwidth round trips and invalid inputs passed\n";
?>
