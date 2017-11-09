<?php

/*
 * 作者：戎青松
 * 时间：11:35:21
 * 
 */

/**
 * Description of TestImpl
 *
 * @author Kevin
 */
__load("Test","service");
class TestImpl implements Test{
    public function showphp() {
        echo dirname(__FILE__);
    }
}
