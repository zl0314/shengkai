<?php

/*
 * 作者：戎青松
 * 时间：17:49:16
 * 
 */

/**
 * Description of Controller
 *
 * @author Kevin
 */
class Controller {

    //put your code here

    private $smarty;

    public function __construct() {
        $this->smarty = $GLOBALS['smarty'];
    }

    protected function display($html) {
        $this->smarty->display($html);
        exit;
    }
    
    protected function fetch($html) {
       return  $this->smarty->fetch($html);     
    }

    protected function assign($key, $value) {
        $this->smarty->assign($key, $value);
    }
     

}
