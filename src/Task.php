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
    
    class Task {

        require_once("config.php");

        private static $tasks = array();

        private $ID;
        private $cfg;
        private $func;
        private $ticks;
        private $isdelayed;
        private $status = false;

        public function __construct(string $id, $anonymousfunc, int $ticks, $isdelayed) {
            array_push(SELF::tasks, &$this);
            $this->ID = $id;
            $this->cfg = new \Config();
            $this->func = $anonymousfunc;
            $this->ticks = $ticks;

            $sql = new mysqli($cfg->getNetwork(), $cfg->getDBUser(), $cfg->getDBPassword(), $cfg->getDB());

            //check first if the table phpschedulerlib exists, else we will create a new table.
            $exists = $sql->prepare("SELECT 1 FROM phpschedulerlib");
            $existsb = $exists->execute();
            $exists->close();

            if(!$existsb) {
                $create = $sql->prepare("
                CREATE TABLE IF NOT EXISTS `phpschedulerlib` (
                    `id` int(254) NOT NULL AUTO_INCREMENT,
                    `time` blob NOT NULL,
                    `name` varchar(100) NOT NULL,
                    `isdelayed` integer(255) NOT NULL,
                    PRIMARY KEY(`id`),
                    UNIQUE `phpschedulerlib` (name),
                    KEY `name` (`name`)
                )");
                $create->execute();
                $create->close();
            }

            //first check if there is a task already running with this name.
            $check = $sql->prepare("SELECT name FROM phpschedulerlib WHERE name=?");
            $check->bind_param("s", $id);
            $bol = $check->execute();
            $check->close();
            if($bol) {
                //throw exception.
                throw new Exception("Unable to instance this class, duplicate scheduler!: ". $this->ID ."");
            } else {
                //create new task inside the database
                $add = $sql->prepare("INSERT INTO phpschedulerlib(time, name, isdelayed) VALUES(?, ?, ?)");
                $add->bind_param("bsi", microtime()*$ticks, $id, $isdelayed ?  1 : 0);
                $add->execute();
                $add->close();
            }
        }

        /**
        * returns the time the last time this task ran
        *
        * @author xize
        */
        public function getTime() {
            $sql = new \mysqli($this->cfg->getNetwork(), $this->cfg->getDBUser(), $this->cfg->getDBPassword(), $this->cfg->getDB());
            $time = $sql->prepare("SELECT time FROM phpschedulerlib WHERE name=?");
            $t = $time->bind_param("s", $this->ID);
            $time->close();
            return $t;
        }

        /**
        * returns the current time from the datetime
        *
        * @author xize
        */
        public function getCurrentTime() {
            return microtime();
        }

        /**
        * updates the tick clock in the database.
        *
        * @author xize
        */
        public function updateClock() {
            $sql = new \mysqli($this->cfg->getNetwork(), $this->cfg->getDBUser(), $this->cfg->getDBPassword(), $this->cfg->getDB());
            $update = $sql->prepare("UPDATE phpschedulerlib WHERE name=? SET time=?");
            $update->bind_param("ss", $this->ID, $this->getCurrentTime()*$this->ticks);
            $update->execute();
            $update->close();
        }

        /**
        * returns true if it is safe to tick, otherwise false
        *
        * @author xize
        */
        public function isSafeToTick() {
            $time = $this->getTime();
            $currenttime = $this->getCurrentTime();
            if($currenttime > $time) {
                return true;
            }
            return false;
        }

        /**
        * returns true if the task is delayed, otherwise false
        *
        * @author xize
        */
        public function isDelayed() {
            $sql = new \mysqli($this->cfg->getNetwork(), $this->cfg->getDBUser(), $this->cfg->getDBPassword(), $this->cfg->getDB());
            $delayed = $sql->prepare("SELECT isdelayed FROM phpschedulerlib WHERE name=?");
            $delayed->bind_param("s", $this->ID);
            $bol = $delayed->execute() == 1 ? true : false;
            $delayed->close();
            return $bol;
        }

        /**
        * returns true if the scheduler is running
        *
        * @author xize
        */
        public function isRunning() {
            return $this->status;
        }

        /**
        * stops the scheduler.
        *
        * @author xize
        */
        public function stop() {
            $this->status = true;
            //remove from the sql db ;-)
            $sql = new \mysqli($this->cfg->getNetwork(), $this->cfg->getDBUser(), $this->cfg->getDBPassword(), $this->cfg->getDB());
            $remove = $sql->prepare("DELETE FROM phpschedulerlib WHERE name=?");
            $remove->bind_param("s", $this->ID);
            $remove->execute();
            $remove->close();
        }

        /**
        * starts a scheduled task which runs on every tick
        *
        * @param delay - the interval between each tick.
        * @author xize
        */
        public function runTimerTask() {
                if($this->isRunning()) {
                    $this->func();
                } else {
                    $this->stop();
                }
        }

        /**
        * starts a delayed task, this task will run for once
        *
        * @param delay - the interval between each tick.
        * @author xize
        */
        public function runDelayedTask() {
            if($this->isRunning()) {
                $this->func();
            }
            $this->stop();
        }

        /**
        * returns a array with schedulers
        *
        * @author xize
        */
        public static function getAllTasks() {
            return SELF::tasks;
        }

    }
}