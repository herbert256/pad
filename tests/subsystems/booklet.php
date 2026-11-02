<?php
require __DIR__.'/../../pad/subs/printables/_lib/booklet.php';
if(padBookletOrder(8)!==[7,0,1,6,5,2,3,4])throw new Exception('Eight-page order');
if(padBookletOrder(5)!==[NULL,0,1,NULL,NULL,2,3,4])throw new Exception('Padding');
if(padBookletOrder(4,'right')!==[0,3,2,1])throw new Exception('Right binding');
foreach([1,2,3,4,7,16,999] as $n){$order=array_filter(padBookletOrder($n),fn($x)=>$x!==NULL);sort($order);if($order!==range(0,$n-1))throw new Exception('Missing or repeated logical page');}
echo "booklet: imposition, both bindings and complete page coverage passed\n";
