<?php

  // The keys of an array are data, often a visitor's: the directives with which Alpine, Vue,
  // htmx, Angular, Livewire, hyperscript and Knockout run their value as script - in every
  // spelling those libraries read - are left out, by {attrs} and by an {input} item alike.

  $j = '{"x-bind:title":"a","v-bind:title":"b","data-hx-on:click":"c","data-hx-on-click":"d",'
     . '"x-init":"e","x-data":"{f:1}","x-effect":"g","x-html":"h","v-html":"i","ng-click":"j",'
     . '"data-ng-click":"k","ng:click":"l","wire:click":"m","hx-vals":"js:{n:1}","_":"on click o",'
     . '"script":"r","data-script":"p","data-bind":"q","title":"ok","data-id":"7","aria-label":"fine","class":"c1"}';

  $x = json_decode ( $j, TRUE );

?>
