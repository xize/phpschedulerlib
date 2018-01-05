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

    class Scheduler {

        #require_once("task.php");

        /**
        * polls through the tasks and run everything what is runable at this time.
        *
        * @author xize
        */
        public function poll() {
            foreach(Task::getAllTasks() as $t) {
                if($t->isSafeToTick()) {
                    if($t->isDelayed()) {
                        $t->runDelayedTask();                      
                    } else {
                        $t->runTimerTask();
                    }
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

    if(isset($_GET['trck'])) {
         $time = $_GET['trck'];
         if((microtime() - $time) < 1000) {
            $this->poll();
        }
    }

    $scheduler = new Scheduler();
    $scheduler->createTracker();
}