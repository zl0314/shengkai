<?php

/*
 * 作者：鞠嵩
 * 时间：10:37:56
 * 
 */

/**
 * Description of GroupService
 *
 * @author Kevin
 */
class AdvertService {
    //put your code here
    private $advert;
    public function __construct() {
          __load("AdvertModel", "model");
        $this->advert=new AdvertModel();

    }

    public function add_advert($data){
        return $this->advert->add_advert($data);
    }
    public function update_advert($data){     
        return $this->advert->update_advert($data); 
    }
     public function get_advert_list(){
        return $this->advert->get_advert_list();        
    }
     public function get_advert($id){
        return $this->advert->get_advert($id);
     }
    public function remove_advert($id){
        return $this->advert->remove_advert($id);
    }
    public function remove_b($data) {
        return $this->advert->remove_batch($data);
    }
    public function get_advert_template($id){
        return $this->advert->get_advert_template($id);        
    }
}
