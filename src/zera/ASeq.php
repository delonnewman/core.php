<?php

namespace zera;

require_once 'ISeq.php';
require_once 'Sequential.php';
require_once 'IHashEq.php';

abstract class ASeq implements ISeq, Sequential, IHashEq {
    private $hash;
    private $hasheq;
    private $meta;

    function toEmpty() {
        return PersistentList::NIL();
    }

    function __construct($meta = null) {
        $this->meta = $meta;
    }

    function more() {
        $s = $this->next();
        if ($s === null) {
            return PersistentList::NIL();
        }
        return $s;
    }

    function seq() {
        return $this;
    }

    function hashCode() {
        if ($this->hash === null) {
            $hash = 1;
            for ($s = $this->seq(); $s !== null; $s = $s->next()) {
                $hash = 31 * $hash + ($s->first() === null ? 0 : hashOf($s->first()));
            }
            $this->hash = $hash;
        }
        return $this->hash;
    }

    function hasheq() {
        if ($this->hasheq === null) {
            $this->hasheq = $this->hashCode();
        }
        return $this->hasheq;
    }

    function equiv($val) {
        
    }
}