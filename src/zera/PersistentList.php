<?php

namespace zera;

require_once 'ASeq.php';
require_once 'IPersistentList.php';
require_once 'IReduce.php';
require_once 'Counted.php';

class PersistentList extends ASeq implements IPersistentList, IReduce, Counted {
    private $first;
    private $rest;
    private $count;

    public function __construct($first = null, $rest = null, $count = 0, $meta = null) {
        parent::__construct($meta);
        $this->first = $first;
        $this->rest  = $rest;
        $this->count = $count;
    }

    public static $NIL = null;
    public static function NIL() {
        if (self::$NIL === null) {
            self::$NIL = new PersistentList();
        }
        return self::$NIL;
    }

    function first() {
        return $this->first;
    }

    function next() {
        if ($this->count === 1) {
            return null;
        }
        return $this->rest;
    }

    function peek() {
        return $this->first;
    }

    function pop() {
        if ($this->rest === null) {
            return self::NIL()->withMeta($this->meta);
        }
        return $this->rest;
    }

    function count() {
        return $this->count;
    }

    function cons($x) {
        return new self($x, $this, $this->count + 1, $this->meta);
    }

    function toEmpty() {
        return self::NIL()->withMeta($this->meta);
    }

    function withMeta($meta) {
        if ($meta !== $this->meta) {
            return new self($this->first, $this->rest, $this->count, $meta);
        }
        return this;
    }

    function reduce($fn, $init = null) {
        $args = func_get_args();
        $argc = sizeof($args);

        if ($argc === 1) {
            $ret = $this->first();
            for ($s = $this->next(); $s !== null; $s = $s->next()) {
                $ret = call_user_func($fn, $ret, $s->first());
            }
            return $ret;
        }
        elseif ($argc === 2) {
            $ret = call_user_func($fn, $init);
            for ($s = $this->next(); $s !== null; $s = $s->next()) {
                $ret = call_user_func($fn, $ret, $s->first());
            }
            return $ret;
        }
    }
}