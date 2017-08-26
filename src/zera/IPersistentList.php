<?php

namespace zera;

require_once 'Sequential.php';
require_once 'IPersistentStack.php';

interface IPersistentList extends Sequential, IPersistentStack {
}