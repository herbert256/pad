<?php
// Greece: mandatory nationwide holidays under Law 4808/2021, plus explicitly non-universal
// religious observances. Coverage stops before 2027, whose May Day transfer requires a decree.
// Orthodox dates use the existing Julian computus converted to Gregorian dates.
// Verified 2026-11-02; dated sources and fixtures: docs/subsystems/holidays/gr.md.
return [
 'name'=>'Greece', 'langs'=>['el'], 'years'=>[2022,2026],
 'days'=>[
  ['date'=>'01-01','name'=>"New Year's Day",'local'=>'Πρωτοχρονιά'],
  ['date'=>'01-06','name'=>'Epiphany','local'=>'Θεοφάνεια'],
  ['orthodox'=>-48,'name'=>'Clean Monday','local'=>'Καθαρά Δευτέρα','type'=>'observance'],
  ['date'=>'03-25','name'=>'Independence Day','local'=>'Εικοστή Πέμπτη Μαρτίου'],
  ['orthodox'=>-2,'name'=>'Orthodox Good Friday','local'=>'Μεγάλη Παρασκευή','type'=>'observance'],
  ['orthodox'=>0,'name'=>'Orthodox Easter Sunday','local'=>'Κυριακή του Πάσχα','type'=>'observance'],
  ['orthodox'=>1,'name'=>'Orthodox Easter Monday','local'=>'Δευτέρα του Πάσχα'],
  ['date'=>'05-01','name'=>'Labour Day','local'=>'Πρωτομαγιά','moved'=>[2022=>'05-02',2024=>'05-07']],
  ['orthodox'=>50,'name'=>'Holy Spirit Monday','local'=>'Αγίου Πνεύματος','type'=>'observance'],
  ['date'=>'08-15','name'=>'Dormition of the Mother of God','local'=>'Κοίμηση της Θεοτόκου'],
  ['date'=>'10-28','name'=>'Ohi Day','local'=>'Εικοστή Ογδόη Οκτωβρίου'],
  ['date'=>'12-25','name'=>'Christmas Day','local'=>'Χριστούγεννα'],
  ['date'=>'12-26','name'=>'Synaxis of the Mother of God','local'=>'Σύναξη της Θεοτόκου'],
 ]
];
