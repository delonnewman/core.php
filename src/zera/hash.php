<?php

namespace zera;

function unsignedRightShift($a, $b) {
    if ($b >= 32 || $b < -32) {
        $m = (int)($b/32);
        $b = $b-($m*32);
    }

    if ($b < 0) {
        $b = 32 + $b;
    }

    if ($b == 0) {
        return (($a>>1)&0x7fffffff)*2+(($a>>$b)&1);
    }

    if ($a < 0) {
        $a = ($a >> 1);
        $a &= 2147483647;
        $a |= 0x40000000;
        $a = ($a >> ($b - 1));
    }
    else {
        $a = ($a >> $b);
    }
    return $a;
}

function stringToBytes($s) {
    return array_slice(unpack('C*', "\0" . $s), 1);
}

// Usage: perlHash($buf)
//        perlHash($seed, $buf)
//        perlHash($seed, $buf, $offset, $length)
//
// where $buf is an array of bytes, $seed is an integer,
// $offset is an integer, and $length is an integer
//
// See Also:
// https://stackoverflow.com/questions/11214270/what-hashing-function-algorithm-does-perl-use
// https://github.com/lacuna/bifurcan/blob/master/src/io/lacuna/bifurcan/hash/PerlHash.java
function perlHash() {
    $args = func_get_args();
    $argc = sizeof($args);

    $seed   = null;
    $buf    = null;
    $offset = null;
    $len    = null;
    if ($argc === 1) {
        $seed   = 0;
        $buf    = $args[0];
        $offset = 0;
        $len    = sizeof($buf) - 1;
    }
    elseif ($argc === 2) {
        $seed   = $argv[0];
        $buf    = $argv[1];
        $offset = 0;
        $len    = sizeof($buf) - 1;
    }
    elseif ($argc === 4) {
        $seed   = $argv[0];
        $buf    = $argv[1];
        $offset = $argv[2];
        $len    = $argv[3];
    }
    else {
        throw new Exception("wrong number of arguments expected 1, 2, or 4, got: $argc");
    }

    $key = $seed;

    $limit = $offset + $len;
    for ($i = $offset; $i < $limit; $i++) {
        $key += $buf[$i] & 0xFF;
        $key += $key << 10;
        $key ^= unsignedRightShift($key, 6);
    }
    $key += $key << 3;
    $key ^= unsignedRightShift($key, 11);
    $key += $key << 15;

    return $key;
}

function stringHash($s) {
    return perlHash(stringToBytes($s));
}

function hashOf($value) {
    $type = gettype($value);

    if ($type === 'integer' or $type === 'double') {
        return $value;
    }
    elseif ($type === 'string') {
        return stringHash('~s' . $value);
    }
    elseif ($type === 'array') {
        $buf = [];
        while ($x = current($value)) {
            array_push($buf, hashOf($x));
            next($value);
        }
        return stringHash(join('|', $buf));
    }
    elseif ($type === 'object' and method_exists($value, 'hashCode')) {
        return $value->hashCode();
    }
    else {
        return null;
    }
}

function hashCombine($seed, $hash) {
    $seed ^= $hash + 0x9e3779b9 + ($seed << 6) + ($seed >> 2);
    return $seed;
}

function equals($a, $b) {
    if ($a === null) return $b === null;
    $h = hashOf($a);
    if ($h === null) return $a === $b;
    else {
        return $h === hashOf($b);
    }
}