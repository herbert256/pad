# Additional spherical calculations

All use existing [longitude, latitude] degrees and mean Earth radius 6371008.8 m.
`padGeo('destination', point, distance, ['bearing'=>90,'unit'=>'km'])` advances on
a great circle. Distance is nonnegative and no more than half the circumference;
bearing is finite degrees clockwise from north. Longitude normalizes to [-180,180).
A nonzero journey from an exact pole is rejected because its bearing is undefined.
Zero distance preserves the supplied point. This is a spherical calculation, not
ellipsoidal geodesy; antipodal destinations are supported but inverse bearings at
antipodes remain undefined.

`padGeo('route-length', points, null, ['unit'=>'m'])` sums shortest great-circle
legs with compensation. At most 10000 list points; an empty or singleton route is
zero. This is the length of supplied legs, never a street routing service.

`padGeo('nearest', origin, identityKeyedPoints, ['unit'=>'m'])` returns
`{id,point,distance,unit}`, or null for an empty candidate set. At most 10000 points;
exact equal distances retain the first supplied identity. All three validate unit
options even for empty/zero cases; units must be registered canonical length symbols.
