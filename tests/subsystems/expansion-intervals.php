<?php
  require __DIR__ . '/_expansion_boot.php';
  require PAD . 'lib/intervals.php';
  $choices = [ [0,1], [1,2], [2,4], [0,3], [1,4], [3,4] ];
  $make = function ( $mask ) use ( $choices ) { $out = []; foreach ( $choices as $i => [$a,$b] ) if ( $mask & (1 << $i) ) $out [] = ['start'=>$a,'end'=>$b]; return $out; };
  $coverage = function ( $rows ) { $out=[]; for($i=0;$i<4;$i++){ $out[$i]=FALSE; foreach($rows as $r) if($r['start']<=$i+.5 && $r['end']>$i+.5) $out[$i]=TRUE; } return $out; };
  for ( $a=0; $a<64; $a++ ) for ( $b=0; $b<64; $b++ ) {
    $x=$make($a);$y=$make($b);$cx=$coverage($x);$cy=$coverage($y);
    foreach(['union','intersection','subtract'] as $kind){
      $want=[];foreach($cx as $i=>$v)$want[$i]=match($kind){'union'=>$v||$cy[$i],'intersection'=>$v&&$cy[$i],'subtract'=>$v&&!$cy[$i]};
      $got=padIntervals($kind,$x,$y);subCheck($coverage($got)===$want,'discrete oracle '.$kind);
      for($i=1;$i<count($got);$i++)subCheck($got[$i-1]['end']<$got[$i]['start'],'canonical separated ranges');
    }
  }
  subCheck(padIntervals('intersection',[['start'=>0,'end'=>1]],[['start'=>1,'end'=>2]])===[],'touching is not overlap');
  $pairs=padIntervals('overlaps',[['start'=>0,'end'=>2,'id'=>'a']],[['start'=>1,'end'=>3,'id'=>'b']]);
  subCheck($pairs[0]['left']['id']==='a'&&$pairs[0]['right']['id']==='b'&&$pairs[0]['start']===1,'overlap identities');
  foreach(['left','right'] as $reserved)subReject(fn()=>padIntervals('overlaps',[['start'=>0,'end'=>2,$reserved=>'room']],[['start'=>1,'end'=>3,$reserved=>'room']],['group'=>$reserved]));
  $named=padIntervals('overlaps',[['start'=>0,'end'=>2,'room'=>'a','id'=>'source-a']],[['start'=>1,'end'=>3,'room'=>'a','id'=>'source-b']],['group'=>'room']);
  subCheck($named[0]['room']==='a'&&$named[0]['left']['id']==='source-a'&&$named[0]['right']['id']==='source-b','group and provenance coexist');
  $groups=padIntervals('union',[['start'=>0,'end'=>1,'resource'=>1],['start'=>0,'end'=>1,'resource'=>'1']]);subCheck(count($groups)===2,'typed groups');
  $fold=padIntervals('subtract',[['start'=>'2026-10-25T01:00:00+02:00','end'=>'2026-10-25T04:00:00+01:00']],[['start'=>'2026-10-25T02:00:00+02:00','end'=>'2026-10-25T02:00:00+01:00']],['timezone'=>'Europe/Amsterdam']);
  subCheck(count($fold)===2&&$fold[1]['start']==='2026-10-25T02:00:00+01:00','DST distinct instants');
  foreach([
    fn()=>padIntervals('union',[['start'=>INF,'end'=>INF]]),
    fn()=>padIntervals('union',[['start'=>2,'end'=>1]]),
    fn()=>padIntervals('union',[['start'=>'2026-02-30T00:00Z','end'=>'2026-03-02T00:00Z']]),
    fn()=>padIntervals('union',[['start'=>'2026-02-01T00:00+00:99','end'=>'2026-03-02T00:00Z']]),
    fn()=>padIntervals('union',[['start'=>0,'end'=>1]],[],['timezone'=>'bad']),
    fn()=>padIntervals('union',[['start'=>0,'end'=>'2026-01-01T00:00Z']]),
    fn()=>padIntervals('union',[['start'=>0,'end'=>1],['start'=>2,'end'=>3]],[],['maxResults'=>1]),
    fn()=>padIntervals('overlaps',$make(63),$make(63),['maxResults'=>1])
  ] as $bad)subReject($bad);
  echo "intervals: 4096 independent set cases, identities, typed groups, DST and bounds passed\n";
?>
