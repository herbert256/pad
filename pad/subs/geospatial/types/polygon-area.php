<?php

  // polygon-area - Sphere radius 6371008.8 m; result includes model, radius and boundary semantics. Computes the exact surface integral for edges linear in unwrapped longitude/latitude (parallels/meridians for boxes), rather than geodesic great-circle boundaries. Ring orientation is ignored; holes subtract. Reuses Polygon validation (≤1000 vertices, closed, nonintersecting rings, strict holes); excludes pole vertices, pole-enclosing and ≥180° longitude-span rings. Narrow antimeridian crossing works via unwrapping. Units m2, km2 or ha.
  //
  // Runnable example: `apps/tags/examples/geo/polygon-area.pad`. Independent fixture: `php tests/subsystems/expansion-geospatial-polygon-area.php`.

return ['options'=>['unit'],'call'=>function($a,$b,$options){
 if($b!==NULL)throw new InvalidArgumentException('polygon-area accepts one polygon');$unit=$options['unit']??'m2';$scales=['m2'=>1,'km2'=>1000000,'ha'=>10000];if(!is_string($unit)||!isset($scales[$unit]))throw new InvalidArgumentException('area unit is m2, km2 or ha');$rings=padGeoPolygon($a);$areas=[];
 foreach($rings as $ring){$longitudes=array_column($ring,0);if(max($longitudes)-min($longitudes)>=180)throw new InvalidArgumentException('area requires rings narrower than 180 degrees after unwrapping');$sum=0.0;$correction=0.0;for($i=1;$i<count($ring);$i++){[$lon1,$lat1]=array_map('deg2rad',$ring[$i-1]);[$lon2,$lat2]=array_map('deg2rad',$ring[$i]);$half=($lat2-$lat1)/2;$average=sin(($lat1+$lat2)/2)*($half==0?1:sin($half)/$half);$term=($lon2-$lon1)*$average-$correction;$next=$sum+$term;$correction=($next-$sum)-$term;$sum=$next;}$areas[]=abs($sum)*padGeoRadius**2;}
 $area=$areas[0]-array_sum(array_slice($areas,1));if($area<0)throw new InvalidArgumentException('holes exceed outer area');return ['area'=>$area/$scales[$unit],'unit'=>$unit,'model'=>'sphere','radiusMetres'=>padGeoRadius,'boundary'=>'linear in unwrapped longitude/latitude; not geodesic','holes'=>count($rings)-1];
}];
