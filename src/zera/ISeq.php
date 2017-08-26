<?php

namespace zera;

require_once 'IPersistentCollection.php';

interface ISeq extends IPersistentCollection {
    function first();
    function next();
    function more();
    function cons($x);
}