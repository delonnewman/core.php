<?php

namespace zera;

require_once 'hash.php';
require_once 'Symbol.php';

class Keyword implements Named {
    private static $table;

    private $sym;
    private $meta;
    private $str;
    private $hasheq;

    public static function intern() {
        $args = func_get_args();
        $argc = sizeof($args);
        $sym  = null;
        if ($argc === 2) {
            $sym = Symbol::intern($args[0], $args[1]);
        }
        elseif ($argc === 1) {
            if (is_a($args[0], 'zera\Symbol')) {
                $sym = $args[0];
            }
            else {
                $sym = Symbol::intern($args[0]);
            }
        }
        else {
            throw new Exception("Wrong number of arguments expected 1 or 2, got: $argc");
        }

        if (!array_key_exists("$sym", self::$table)) {
            if ($sym->meta() !== null) {
                $sym = $sym->withMeta(null);
            }
            $k = new Keyword($sym);
            self::$table["$sym"] = $k;
            return $k;
        }
        return self::$table["$sym"];
    }

    public static function find($sym) {
        $args = func_get_args();
        $argc = sizeof($args);
        $sym  = null;
        if ($argc === 2) {
            $sym = Symbol::intern($args[0], $args[1]);
        }
        elseif ($argc === 1) {
            if (is_a($args[0], 'zera\Symbol')) {
                $sym = $args[0];
            }
            else {
                $sym = Symbol::intern($args[0]);
            }
        }
        else {
            throw new Exception("Wrong number of arguments expected 1 or 2, got: $argc");
        }

        if (array_key_exists("$sym", self::$table)) {
            return self::$table["$sym"];
        }
        return null;
    }

    public function __construct($sym, $meta) {
        $this->sym    = $sym;
        $this->meta   = $meta;
        $this->hasheq = $sym->hasheq() + 0x9e3779b9;
    }

    public function hashCode() {
        return $this->sym + 0x9e3779b9;
    }

    public function hasheq() {
        return $this->hasheq;
    }

    public function __toString() {
        if ($this->str === null) {
            $this->str = ":$sym";
        }
        return $this->str;
    }

    public function getName() {
        return $this->sym->getName();
    }

    public function getNamspace() {
        return $this->sym->getNamspace();
    }

    public function __invoke($map, $default = null) {
        if (method_exists($map, 'get')) {
            return $map->get($this, $default);
        }
        return null;
    }
}