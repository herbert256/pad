# calendar:capacity

`windows=` records have start,end,resource,capacity. `bookings=` records (or data) have start,end,resource,load. Half-open intervals use calendar normalization: timed boundaries require explicit offsets; allDay uses civil dates in timezone. Supply windows cannot overlap per resource. Used load is summed, not assumed to equal booking count; explicit mode=count opts into count. `unit=` labels all numbers. Remaining/over are nonnegative; zero capacity is valid. A sweep splits each change instant and preserves DST offsets. Maximum 2000 combined intervals.

## Example

The paired PHP page supplies the application data:

```php
<?php
$capacityWindows=[['resource'=>'Room A','start'=>'2026-11-02T09:00:00+01:00','end'=>'2026-11-02T17:00:00+01:00','capacity'=>3]];$capacityBookings=[['resource'=>'Room A','start'=>'2026-11-02T10:00:00+01:00','end'=>'2026-11-02T12:00:00+01:00','load'=>2],['resource'=>'Room A','start'=>'2026-11-02T11:00:00+01:00','end'=>'2026-11-02T13:00:00+01:00','load'=>2]];
```

The PAD template renders it:

```pad
{calendar:capacity windows='capacityWindows', bookings='capacityBookings', unit='staff', timezone='Europe/Amsterdam'/}
```

Run the focused fixture with `php tests/subsystems/calendar-capacity.php`.

Array-valued options accept a literal array or the quoted name of an application array/store.
