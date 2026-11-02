<?php

  // scenario - `model=` is an A1-addressed scalar/formula map. `scenarios=` maps names to input overrides; `inputs=` and `outputs=` are explicit address lists. Each scenario copies and recalculates the model using the existing server formula engine and same seed. Error codes remain visible; base state is unchanged. Overrides cannot add formulas or undeclared cells. Bounds: 50 scenarios, 100 outputs, 20000 model-cell evaluations across scenarios. Existing evaluator bounds also apply.
  //
  // ## Example
  //
  // The paired PHP page supplies the application data:
  //
  // ```php
  // <?php
  // $scenarioModel=['A1'=>10,'B1'=>3,'C1'=>'=A1*B1'];$scenarioCases=['Low'=>['A1'=>5],'Base'=>[],'High'=>['A1'=>20]];$scenarioInputs=['A1'];$scenarioOutputs=['C1'];
  // ```
  //
  // The PAD template renders it:
  //
  // ```pad
  // {sheet:scenario model='scenarioModel', scenarios='scenarioCases', inputs='scenarioInputs', outputs='scenarioOutputs'/}
  // ```
  //
  // Run the focused fixture with `php tests/subsystems/sheet-scenario.php`.
  //
  // Array-valued options accept a literal array or the quoted name of an application array/store.

include_once PAD.'subs/sheets/_lib/scenario.php';
$padScenarioValue=function($v){if(is_string($v)&&padArrayCheck($v))return padArrayValue($v);return $v;};
try{$model=$padScenarioValue(padTagParm('model',[]));$inputs=$padScenarioValue(padTagParm('inputs',[]));$outputs=$padScenarioValue(padTagParm('outputs',[]));$rows=padSheetScenario($model,$padScenarioValue(padTagParm('scenarios',[])),$inputs,$outputs,(int)padTagParm('seed',0));$h='<table class="pad-sheet-scenario"><caption>'.padChartAttr((string)padTagParm('caption','Scenarios')).'</caption><thead><tr><th>Scenario</th>';foreach(['Input'=>$inputs,'Output'=>$outputs] as $label=>$cells)foreach($cells as $cell)$h.='<th scope="col">'.$label.' '.padChartAttr($cell).'</th>';$h.='</tr></thead><tbody>';foreach($rows as $row){$h.='<tr><th scope="row">'.padChartAttr($row['name']).'</th>';foreach(array_merge(array_values($row['inputs']),array_values($row['outputs'])) as $v)$h.='<td>'.padChartAttr($v===NULL?'Empty':(is_bool($v)?($v?'TRUE':'FALSE'):(string)$v)).'</td>';$h.='</tr>';}return $h.'</tbody></table>';}catch(Exception $e){padError($e->getMessage());return '';}
