<?php

namespace \zera;

trait TList {
    abstract function nth($idx, $default=null);
    abstract function size();

    function isLinear() {
        return false;
    }
}