<?php

  isset($pad_table_id) ? $pad_table_id++ : $pad_table_id = 0 ;

  if ( isset($pad_table_id))
    $pad_table_id++;
  else    
    $pad_table_id = 0;
 
  $pad_table_names  = 'pad_table_names_'  . $pad_table_id;
  $pad_tmp = 'pad_table_rows_'   . $pad_table_id;

  $$pad_table_names = $$pad_tmp = $pad_table_base = [];

  if ( ! $pad_parm )
    $pad_table = &$pad_data [$pad_lvl-1] [$pad_key[$pad_lvl-1]];
  elseif ($pad_parm == -1)
    $pad_table = &$pad_data [$pad_lvl-1];

  $pad_table_base ['row_base'] ['value'] = '';
  
  $pad_table_fields = FALSE;
  if ( is_array($pad_table) or is_object ($pad_table) )
    foreach ( $pad_table as $pad_table_key => $pad_table_val )
      if ( is_array($pad_table_val) or is_object ($pad_table_val) )
        foreach ( $pad_table_val as $pad_table_key2 => $pad_table_val2 ) {
          $$pad_table_names [$pad_table_key2] ['value'] = $pad_table_key2;
          $pad_table_base [$pad_table_key2] ['value'] = '';
        }
      else {
        $pad_table_fields = TRUE;
        $$pad_table_names [$pad_table_key] ['value'] = $pad_table_key;
        $pad_table_base [$pad_table_key] ['value'] = '';
      }

  if ($pad_table_fields)
    $$pad_tmp ['row_base'] ['fields'] = $pad_table_base;
  
  if ( is_array($pad_table) or is_object ($pad_table) ) {
    foreach ( $pad_table as $pad_table_key => $pad_table_val ) {
       if ( is_array($pad_table_val) or is_object ($pad_table_val) ) {
        $$pad_tmp [$pad_table_key] ['fields'] = $pad_table_base;
        $$pad_tmp [$pad_table_key] ['fields'] ['row_base'] ['value'] = $pad_table_key;
        foreach ( $pad_table_val as $pad_table_key2 => $pad_table_val2 )
          if ( is_array($pad_table_val2) or is_object ($pad_table_val2) )
            $$pad_tmp [$pad_table_key] ['fields'] [$pad_table_key2] ['value'] = '[...]';
          else
            $$pad_tmp [$pad_table_key] ['fields'] [$pad_table_key2] ['value'] = $pad_table_val2;
      } else {
          $$pad_tmp ['row_base'] ['fields'] [$pad_table_key] ['value'] = $pad_table_val;
      }
    }
  } else {
    $$pad_table_names [1] ['name']  = 'current';
    $$pad_tmp  [1] ['value'] = '';
    $$pad_tmp  [2] ['value'] = $pad_table;
  }

  if ($pad_table_id ==1)
    $pad_content = '
<style>
  #padTable {
      font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
      border-collapse: collapse;
  }
  #padTable td, #padTable th {
      border: 1px solid #ddd;
      padding: 5px;
  }
  #padTable tr:nth-child(even){background-color: #f2f2f2;}
  #padTable tr:hover {background-color: #ddd;}
  #padTable th {
      padding-top: 8px;
      padding-bottom: 8px;
      text-align: left;
      background-color: #4CAF50;
      color: white;
  }
</style>
';

$pad_content .= '
  <table id="padTable">
    <tr>
      <th></th>
      {tag \'$pad_table_names_{$pad_table_id}\'}
        <th>{$value}</th>
      {/tag}
    </tr>
    {tag \'$pad_table_rows_{$pad_table_id}\'}
      <tr>
        {tag \'$fields\'}
          <td>{$value}</td>
        {/tag}
      </tr>
    {/tag}
  </table>
';

  return TRUE;

?>