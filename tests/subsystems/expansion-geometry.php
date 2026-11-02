<?php
  require __DIR__.'/_expansion_boot.php';require PAD.'lib/geometry.php';
  $square=[[0,0],[4,0],[4,4],[0,4]];$r=padGeometry('centroid',$square);subNear($r['area'],16);subNear($r['centroid'][0],2);subNear($r['centroid'][1],2);
  subNear(padGeometry('area',array_reverse($square))['signedArea'],-16);
  $shifted=array_map(fn($p)=>[$p[0]+1e15,$p[1]-1e15],$square);$r=padGeometry('centroid',$shifted);subNear($r['area'],16);subNear($r['centroid'][0]-1e15,2);subNear($r['centroid'][1]+1e15,2);
  $triangle=[[0,0],[6,0],[0,3]];$r=padGeometry('centroid',$triangle);subNear($r['area'],9);subNear($r['centroid'][0],2);subNear($r['centroid'][1],1);
  $rotate=array_map(fn($p)=>[-$p[1],$p[0]],$triangle);subNear(padGeometry('area',$rotate)['area'],9);
  $clip=padGeometry('clip',$square,['box'=>[1,1,3,5]]);subNear(padGeometry('area',$clip['points'])['area'],6);
  subCheck(padGeometry('clip',$square,['box'=>[4,0,5,2]])['status']==='empty');
  subCheck(padGeometry('clip',$square,['box'=>[1,1,1,3]])['points']===[]);
  subCheck(padGeometry('area',[[0,0],[1,0],[2,0]])['status']==='degenerate');
  $grid=[];for($x=0;$x<3;$x++)for($y=0;$y<3;$y++)$grid[]=[$x,$y];
  for($mask=0;$mask<512;$mask++){
    $set=[];foreach($grid as $i=>$p)if($mask&(1<<$i))$set[]=$p;
    $h=padGeometry('hull',$set)['points'];
    foreach($h as $p)subCheck(in_array($p,$set),'hull vertex from input');
    if(count($h)>=3)foreach($h as $i=>$a){$b=$h[($i+1)%count($h)];foreach($set as $p){$cross=($b[0]-$a[0])*($p[1]-$a[1])-($b[1]-$a[1])*($p[0]-$a[0]);subCheck($cross>=0,'independent support half-plane');}}
    elseif(count($h)===2)foreach($set as $p)subNear(($h[1][0]-$h[0][0])*($p[1]-$h[0][1])-($h[1][1]-$h[0][1])*($p[0]-$h[0][0]),0);
    else subCheck(count($set)<2,'degenerate point count');
  }
  foreach([
    fn()=>padGeometry('area',[[0,0],[2,2],[0,2],[2,0]]),
    fn()=>padGeometry('clip',[[0,0],[3,0],[1,1],[3,3],[0,3]],['box'=>[0,0,2,2]]),
    fn()=>padGeometry('hull',[[INF,0]]),fn()=>padGeometry('hull',[['1',0]]),
    fn()=>padGeometry('area',[[[0,0],[1,0],[1,1]]]),fn()=>padGeometry('area',[[0,0],[1e-200,0],[0,1e-200]]),
    fn()=>padGeometry('area',array_fill(0,2001,[0,0])),fn()=>padGeometry('clip',$square,['box'=>[3,0,1,2]])
  ] as $bad)subReject($bad);
  echo "geometry: analytic areas/centroids, clipping, large origins, 512 grid hull oracles and rejection boundaries passed\n";
?>
