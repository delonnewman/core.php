<?php

namespace zera;

require_once 'Seqable.php';

interface IPersistentCollection extends Seqable {
    function count();
    function cons($x);
    function toEmpty();
    function equiv();
}