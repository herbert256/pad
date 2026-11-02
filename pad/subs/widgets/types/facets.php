<?php
$padFacetSelected=padTagParm('selected',[]);if(is_string($padFacetSelected)&&padArrayCheck($padFacetSelected))$padFacetSelected=padArrayValue($padFacetSelected);
try{return padWidgetFacets(padSearchInfo(),$padFacetSelected,(string)padTagParm('name','facet'));}catch(Exception $e){padError($e->getMessage());return '';}
