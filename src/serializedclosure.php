<?php

/**
    this class has been inspired by http://www.htmlist.com/development/extending-php-5-3-closures-with-serialization-and-reflection/
    and so on the credits to this class belongs to whoever wrote this class in the tutorial.
*/

namespace phpschedulerlib {

    class SerializedClosure {

        private $closure;
        private $reflected;
        private $code;
        private $used_variables = array();

        public function __construct(\Closure $closure) {
            if($closure instanceof \Closure) {
                $this->closure = $closure;
                $this->reflected = new \ReflectionFunction($closure);
                $this->code = $this->serializeCode();
                $this->used_variables = $this->serializeUsedVariables();
            } else {
                throw new \Exception("\"$closure\" is not a instanceof \"Closure\"");
            }
        }

        /**
        * 
        * invokes the closure as a runtime object again.
        *
        */
        public function __invoke() {
            $args = func_get_args();
            return $this->reflected->invokeArgs($args);
        }

        /**
        *
        * returns the closure object
        *
        */
        public function getClosure() {
            return $this->closure;
        }

        /**
        *
        * returns the serializeable code
        *
        */
        public function getCode() {
            return $this->code;
        }

        /**
        *
        * returns the parameters from the function
        *
        */
        public function getParameters() {
            return $this->reflection->getParameters();
        }

        /**
        *
        * returns the variables inside the function
        *
        */
        public function getUsedVariables() {
            return $this->used_variables;
        }

        /**
        *
        * serializes the variables
        *
        */
        private function serializeUsedVariables() {
            $index = stripos($this->code, 'use');
            if(!index) {
                return array();
            }

            $begin = strpos($this->code, "(", $index)+1;
            $end = strpos($this->code, ")", $begin);
            $vars = explode(',', substr($this->code, $begin, $end - $begin));

            $static_variables = $this->reflected->getStaticVariables();

            $used_vars = array();

            foreach($vars as $var) {
                $var = trim($var, ' $&amp;');
                $used_vars[$var] = $static_vars[$var];
            }

            return $used_vars;
        }

        /**
        *
        * serializes the code
        *
        */
        private function serializeCode() {
            $file = new \SplFileObject($this->reflected->getFileName());
            $file->seek($this->reflected->getStartLine()-1);

            $code = "";

            while($file->key() < $this->reflected->getEndLine()) {
                $code .= $file->current();
                $file->next();
            }

            $begin = strpos($code, "function");
            $end = strrpos($code, "}");
            $code = substr($code, $begin, $end-$begin+1);
            return $code;
        }

        /**
        *
        * stores the reflected data in a array when sleeping
        *
        */
        public function __sleep() {
            return array('code', 'used_variables');
        }

        /**
        *
        * reconstructs the closure by waking up
        *
        */
        public function __wakeup() {
            extract($this->used_variables);

            eval('$_function = '.$this->code.';');

            if(isset($_function) && $_function instanceof \Closure) {
                $this->closure = $_function;
                $this->reflection = new \ReflectionFunction($_function);
            } else {
                throw new \Exception();
            }
        }
    }
}