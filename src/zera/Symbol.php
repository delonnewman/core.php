<?php

namespace zera;

require_once 'hash.php';
require_once 'Named.php';

class Symbol implements Named {
    private $ns;
    private $name;
    private $hasheq;
    private $meta;
    private $str;

    public static function intern() {
        $args = func_get_args();
        $argc = sizeof($args);
        if ($argc === 1) {
            $x = $args[0];
            $parts = explode('/', $x);
            if (sizeof($parts) === 1) {
                return new Symbol(null, $parts[0]);
            }
            elseif (sizeof($parts) === 2) {
                return new Symbol($parts[0], $parts[1]);
            }
            else {
                return new Symbol($parts[0], join('/', array_slice($parts, 1)));
            }
        }
        elseif ($argc === 2) {
            return new Symbol($args[0], $args[1]);
        }
        else {
            throw new Exception("Wrong number of arguments expected 1 or 2, got $argc");
        }
    }

    function __construct($ns, $name, $meta = null) {
        $this->ns   = $ns;
        $this->name = $name;
        $this->meta = $meta;
    }

    function withMeta($meta) {
        return new Symbol($this->ns, $this->name, $meta);
    }

    function meta() {
        return $this->meta;
    }

    function __toString() {
        if ($this->str === null) {
            if ($this->ns === null) {
                $this->str = "$this->ns/$this->name";
            }
            else {
                $this->str = $this->name;
            }
        }
        return $this->str;
    }

    function getName() {
        return $this->name;
    }

    function getNamspace() {
        return $this->namespace;
    }

    function hashCode() {
        return hashCombine(hashOf($this->name), hashOf($this->ns));
    }

    function hasheq() {
        if ($this->hasheq === null) {
            $this->hasheq = $this->hashCode();
        }
        return $this->hasheq;
    }

    function equals($o) {
        if ($this === $o) {
            return true;
        }
        if (!is_a($o, 'zera\Symbol')) {
            return false;
        }
        return $this->ns === $o->ns and $this->name === $o->name;
    }

    function __invoke($map, $default = null) {
        if (method_exists($map, 'get')) {
            return $map->get($this, $default);
        }
        return null;
    }
}