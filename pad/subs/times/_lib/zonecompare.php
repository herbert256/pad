<?php
function padTimeZoneCompare($instant,$zones,$format='Y-m-d H:i') {
 if(!($instant instanceof DateTimeInterface)||!is_array($zones)||!$zones||count($zones)>50||!in_array($format,['Y-m-d H:i','Y-m-d H:i:s','H:i'],TRUE))throw new InvalidArgumentException('zonecompare requires an explicit instant, 1..50 IANA zones and a supported format');
 $html='<dl class="pad-time-zonecompare">';
 foreach($zones as $label=>$zone){if(!is_string($zone)||!in_array($zone,DateTimeZone::listIdentifiers(DateTimeZone::ALL_WITH_BC),TRUE))throw new InvalidArgumentException('zonecompare invalid IANA timezone');
 $local=DateTimeImmutable::createFromInterface($instant)->setTimezone(new DateTimeZone($zone));
 $html.='<dt>'.padChartAttr(is_int($label)?$zone:$label).'</dt><dd><time datetime="'.padChartAttr($local->format(DATE_ATOM)).'">'.padChartAttr($local->format($format.' P')).'</time> <span>'.padChartAttr($zone).'</span></dd>';}
 return $html.'</dl>';
}
