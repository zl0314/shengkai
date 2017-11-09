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

    public function add($data){
        return $this->contract->add($data);
    }
    public function update($data){
        return $this->contract->update($data);
    }
    public function get_AllContract(){
        return $this->contract->get_AllContract();
    }

    public function remove($id){
        return $this->contract->remove($id);
    }

    public function createOrder($data){
        return $this->contract->createOrder($data);

    }
}
