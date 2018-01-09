<?php
/**
    phpschedulerlib - schedule anonymous functions as schedulers
    Copyright (C) 2018 Guido Lucassen

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <https://www.gnu.org/licenses/>.
*/

namespace phpschedulerlib {

    require_once("task.php");

    class Scheduler {
        

        /**
        * polls through the tasks and run everything what is runable at this time.
        *
        * @author xize
        */
        public function poll() {

            echo "<p>all tasks:<p>";

            foreach(Task::getAllTasks() as $t) {
                echo "<p>".$t->getName()."</p>";
                if($t->isSafeToTick()) {
                    $t->doTick();
                }
                 else {
                    echo "I'm not ticked! :(";
                 }
            }
        }

        /**
        * generates a 1 pixel image disguised as a get post, in order to sync with the browser.
        *
        * @author xize
        */
        public function createTracker() {
            echo "<img src=\"?trck=". microtime() . "\" width=\"1px\" height=\"1px\"/>";
        }

    }

    $scheduler = new Scheduler();
    $scheduler->createTracker();

    if(isset($_GET['trck'])) {
         //create header to mimic a image.
         #header("Content-Type: image/jpeg");
         $time = floatval($_GET['trck']);
         if((floatval(microtime()) - $time) < 1000) { //force this condition when the site is slower, else we skip tasks.
            $scheduler->poll();
        }
    }
}