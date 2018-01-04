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

    class Config {

        ###################################
        # EDIT THE DATABASE SETTINGS HERE #
        ###################################
        
        DEFINE("NETWORK", "localhost");
        DEFINE("DBUSER", "root");
        DEFINE("DBPASS", "");
        DEFINE("DB", "");

        ####################################################
        # END OF CONFIGURATION DO NOT EDIT BELOW THIS LINE #
        ####################################################

        /**
        * returns the network host in order to connect with the mysql database.
        *
        * @author xize
        */
        public function getNetwork() {
            return NETWORK;
        }

        /**
        * returns the username of the database.
        *
        * @author xize
        */
        public function getDBUser() {
            return DBUSER;
        }

        /**
        * returns the password of the database.
        *
        * @author xize
        */
        public function getPassword() {
            return DBPASS;
        }

        /**
        * returns the name of the datbase.
        *
        * @author xize
        */
        public function getDB() {
            return DB;
        }

    }

}