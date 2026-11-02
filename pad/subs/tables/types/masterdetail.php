<?php

  // masterdetail - Visible `columns=` comma list, stable `id=` field (default id), scalar `detail=` field (default detail). Optional `template=` is trusted application HTML with `[[field]]` slots; every substituted scalar is escaped. Native details controls provide keyboard disclosure and nested markup. `filter=` searches visible values, `sort=` orders parent records, `descending` reverses it. Filtering/sorting run before `page`/`pagesize` (25 default). Pass request page explicitly with application links/forms. Bounds: 10000 rows, 50 columns, pagesize <=1000.
  //
  // ## Example
  //
  // The paired PHP page supplies the application data:
  //
  // ```php
  // <?php
  // $detailRecords=[['id'=>'a','name'=>'Ada','description'=>'Maintains the calendar'],['id'=>'b','name'=>'Bo','description'=>'Reviews the reports']];
  // ```
  //
  // The PAD template renders it:
  //
  // ```pad
  // {table:masterdetail data='detailRecords', columns='id,name', detail='description'/}
  // ```
  //
  // Run the focused fixture with `php tests/subsystems/table-masterdetail.php`.

include_once PAD.'subs/tables/_lib/masterdetail.php';
try{$rows=array_values(array_map('padChartRow',(array)padChartRows($padContent,'table')));$padContent='';$padData[$pad]=padDefaultData();$opts=[];foreach(['id'=>'id','filter'=>'','sort'=>'','descending'=>FALSE,'pagesize'=>25,'page'=>1,'caption'=>'Records','template'=>NULL,'detail'=>'detail'] as $k=>$v)$opts[$k]=padTagParm($k,$v);$columns=padTagParm('columns','id,name');if(is_string($columns))$columns=array_map('trim',explode(',',$columns));return padTableMasterDetail($rows,$columns,$opts);}catch(Exception $e){padError($e->getMessage());return '';}
