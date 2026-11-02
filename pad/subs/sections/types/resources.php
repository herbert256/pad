<?php

  // resources - Curated resource directory, grouped by consecutive category labels in caller order. Sort input by category to consolidate groups. Each entry has name, link, text, category. Uses existing section:item or data rows. All descriptions are scalar escaped text; links use the section URL policy. Explicit empty state; max 1000 entries. Native links need no script.
  //
  // ## Example
  //
  // ```pad
  // {section:resources title='Documentation'}{section:item 'Getting started', link='/tags/', category='Guides'}A practical introduction.{/section:item}{/section:resources}
  // ```
  //
  // Run the focused fixture with `php tests/subsystems/section-resources.php`.

include_once PAD.'subs/sections/_lib/resources.php';
padSectionParts(['eyebrow','title','text']);$padSecHead=padSectionHead();$padSecItems=padSectionItems('name',['name','link','text','category'],'text','entries');$padSecRawItems=[];foreach($padSecItems as $item){$row=[];foreach(['name','link','text','category'] as $field)$row[$field]=padSectionRaw($item,$field);$padSecRawItems[]=$row;}
try{return padSection('resources',$padSecHead.padSectionResources($padSecRawItems));}catch(Exception $e){padError($e->getMessage());return '';}
