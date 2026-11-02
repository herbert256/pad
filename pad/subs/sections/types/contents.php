<?php

  // contents - Curated chapter index in an ordered navigation list. It does not scan document headings; use widget:toc for that. Each entry has name, link, text. Uses existing section:item or data rows. All descriptions are scalar escaped text; links use the section URL policy. Explicit empty state; max 1000 entries. Native links need no script.
  //
  // ## Example
  //
  // ```pad
  // {section:contents title='Documentation'}{section:item 'Getting started', link='/tags/', category='Guides'}A practical introduction.{/section:item}{/section:contents}
  // ```
  //
  // Run the focused fixture with `php tests/subsystems/section-contents.php`.

include_once PAD.'subs/sections/_lib/contents.php';
padSectionParts(['eyebrow','title','text']);$padSecHead=padSectionHead();$padSecItems=padSectionItems('name',['name','link','text','category'],'text','entries');$padSecRawItems=[];foreach($padSecItems as $item){$row=[];foreach(['name','link','text','category'] as $field)$row[$field]=padSectionRaw($item,$field);$padSecRawItems[]=$row;}
try{return padSection('contents',$padSecHead.padSectionContents($padSecRawItems));}catch(Exception $e){padError($e->getMessage());return '';}
