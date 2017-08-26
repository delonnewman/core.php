<?php

public class AFn implements IFn {
    public function __invoke() {
        return call_user_func_array([$this, 'invoke'], func_get_args());
    }
}