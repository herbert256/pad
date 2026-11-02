<?php
  define ( 'PAD', dirname ( __DIR__, 2 ) . '/pad/' );
  function padDirList ( $p ) { return scandir ( $p ); }
  function padError ( $m ) { throw new InvalidArgumentException ( $m ); }
  function padMakeSafe ( $s, $n ) { return substr($s,0,$n); }
  $padCheckSyntax = TRUE;
  require PAD . 'lib/subsystem.php'; require PAD . 'lib/format.php';
  function check ( $v ) { if ( ! $v ) throw new Exception ( 'format fixture failed' ); }
  $data = ['a b'=>' value ', 'emoji'=>'😀', 'escaped'=>"line\nnext\t", '日本'=>'café', ''=>'empty key'];
  ksort($data,SORT_STRING);
  foreach ( [[],['ascii'=>true],['encoding'=>'ISO-8859-1']] as $options )
    check(padFormatRead('properties',padFormatWrite('properties',$data,$options),$options) === $data);
  check(padFormatRead('properties',"# comment\na\\ b : first\\\n  second\nx=1\nx=2\nflag\n") === ['a b'=>'firstsecond','x'=>'2','flag'=>'']);
  check(padFormatRead('properties',"x=caf\xe9",['encoding'=>'ISO-8859-1']) === ['x'=>'café']);
  foreach (['a=\\u12z4','a=\\uD800',"x=1\nx=2"] as $bad) {
    try { padFormatRead('properties',$bad,['duplicates'=>'error']); throw new Exception('invalid properties accepted'); } catch(InvalidArgumentException $e) {}
  }
  echo "format:properties round trips and invalid inputs passed\n";
?>
