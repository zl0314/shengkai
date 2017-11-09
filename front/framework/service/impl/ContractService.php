<?php

/*
 * 作者：戎青松
 * 时间：10:37:56
 * 
 */

/**
 * Description of HotelService
 *
 * @author Kevin
 */
class ContractService {
    //put your code here
    private $contract;
    public function __construct() {
          __load("ContractModel", "model");
        $this->contract=new ContractModel();
    }
    public function get_Contract($id){
        return $this->contract->get_Contract($id);
    }

}
