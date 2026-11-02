<?php
$exampleResult = padFormatRead('htmltable','<table id="sales"><tr><th>Name</th><th>Total</th></tr><tr><td>Ada</td><td>42</td></tr></table>',['table'=>'#sales']);
$exampleJson = json_encode($exampleResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
