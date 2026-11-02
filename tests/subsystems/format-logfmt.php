<?php
  define ( 'PAD', dirname ( __DIR__, 2 ) . '/pad/' );
  function padDirList ( $p ) { return scandir ( $p ); }
  function padError ( $m ) { throw new InvalidArgumentException ( $m ); }
  function padMakeSafe ( $s, $n ) { return substr($s,0,$n); }
  $padCheckSyntax = TRUE;
  require PAD . 'lib/subsystem.php'; require PAD . 'lib/format.php';
  function check ( $v ) { if ( ! $v ) throw new Exception ( 'format fixture failed' ); }
  $rows = [['empty'=>'','message'=>"hello \"world\"\nnext",'path'=>'a\\b','status'=>'ok']];
  $text = padFormatWrite('logfmt',$rows);
  check(padFormatRead('logfmt',$text) === $rows);
  check(padFormatRead('logfmt',"a=1 b=two\na=3 b=\n") === [['a'=>'1','b'=>'two'],['a'=>'3','b'=>'']]);
  foreach (['a="unterminated','a=1 a=2',"a=1\nb=\"bad\\q\"",'bad key=x'] as $bad) {
    try { padFormatRead('logfmt',$bad); throw new Exception('invalid logfmt accepted'); } catch(InvalidArgumentException $e) { check(str_contains($e->getMessage(),'line')); }
  }
  echo "format:logfmt round trips and invalid inputs passed\n";
?>
