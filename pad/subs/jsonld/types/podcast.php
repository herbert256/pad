<?php
  // podcast - PodcastSeries metadata; a series, not an episode. https://schema.org/PodcastSeries
  return ['type'=>'PodcastSeries','fields'=>[
    'name'=>['name','text',TRUE], 'description'=>['description','text'],
    'url'=>['url','url',TRUE], 'feed'=>['webFeed','url'], 'image'=>['image','url'],
    'author'=>['author',['names','Person']], 'publisher'=>['publisher.name','text'],
    'language'=>['inLanguage','text'], 'start'=>['startDate','date'], 'end'=>['endDate','date'],
  ],'objects'=>['publisher'=>'Organization']];
?>
