<?php

namespace zera;

require_once 'IPersistentCollection.php';

interface IPersistentStack extends IPersistentCollection {
    function peek();
    function pop();
}