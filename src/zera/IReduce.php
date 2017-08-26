<?php

namespace zera;

interface IReduce {
    function reduce($fn, $init = null);
}