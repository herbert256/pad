# Regression: the job queue and the scheduler

## Introduction

Regression test for the job queue (`pad/lib/queue.php`) and the task scheduler
(`pad/lib/schedule.php`). A page queues jobs and works them in the same request, so it shows
what the handlers did, the retries and the failed jobs; another reads `_schedule.php` at
fixed moments and runs the scheduler for one of them. Both empty what they used first and
after, so every fetch starts from nothing; the Regression suite compares each page with its
answer in `regression/regression/queue/`. `pad work regression/queue --once` and
`pad schedule regression/queue --list` run the same handlers on the command line.

## Files

| File | Description |
|------|-------------|
| `_jobs/greet.php` | Keeps `hello $name` - through `queueGreeting` of `_lib/`, the job runs in the application |
| `_jobs/flaky.php` | Fails until its third attempt |
| `_jobs/broken.php` | Answers FALSE - a failed attempt |
| `_jobs/strict.php` | Raises a `padError`, which fails the job and not the request |
| `_jobs/warn.php` | Reads an undefined variable - a PHP warning fails the job |
| `_jobs/stamp.php` | Writes a line to the application's log, for the command line |
| `_lib/greeting.php` | `queueGreeting`, the function `greet` calls |
| `_schedule.php` | Five entries: every minute, every 15 minutes, a queued one at 03:00, weekdays at 08:00 without overlap, monthly |
| `work.php/pad` | Queues eight jobs, works them, retries the failed ones |
| `schedule.php/pad` | The entries with their next run, which are due at four moments, two runs of one minute |
| `_config/config.php` | `_common` off, Tidy off |
