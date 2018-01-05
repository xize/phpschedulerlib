<?php
namespace exampletest;

include "../src/scheduler.php";
require_once "../src/task.php";

$i = 0;

$task = new \phpschedulerlib\Task("mytask", function() {
    echo "<p>I'm saying helloworld for the ".$i++."th time!</p>";
}, 1000, true);