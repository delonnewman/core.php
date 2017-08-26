<?php

public class ArityException extends Exception {
    public function __construct($actual, $name, $previous = null) {
        parent::__construct("Wrong number of arguments for $name, got: $actual")
    }
}