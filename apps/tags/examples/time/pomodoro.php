<?php
// Application composition: start a focus or break interval on this request, no new time kind.
$pomodoroMinutes = ($_GET['minutes'] ?? '') === '5' ? 5 : 25;
$pomodoroLabel = $pomodoroMinutes === 5 ? 'Short break' : 'Focus session';
$pomodoroEnd = (new DateTimeImmutable())->modify('+'.$pomodoroMinutes.' minutes')->format(DATE_ATOM);
?>
