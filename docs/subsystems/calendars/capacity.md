# calendar:capacity

`windows=` records have start,end,resource,capacity. `bookings=` records (or data) have start,end,resource,load. Half-open intervals use calendar normalization: timed boundaries require explicit offsets; allDay uses civil dates in timezone. Supply windows cannot overlap per resource. Used load is summed, not assumed to equal booking count; explicit mode=count opts into count. `unit=` labels all numbers. Remaining/over are nonnegative; zero capacity is valid. A sweep splits each change instant and preserves DST offsets. Maximum 2000 combined intervals.

## Example

```pad
{calendar:capacity windows='capacityWindows', bookings='capacityBookings', unit='staff', timezone='Europe/Amsterdam'/}
```

Run the focused fixture with `php tests/subsystems/calendar-capacity.php`.

Array-valued options accept a literal array or the quoted name of an application array/store.
