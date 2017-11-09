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
class ColorService {
    //put your code here
    private $color;
    public function __construct() {
          __load("ColorModel", "model");
        $this->color=new ColorModel();

    }

    public function add_Color($data){
        return $this->color->add_Color($data);
    }
    public function update($data){     
        return $this->color->update($data); 
    }
     public function get_All_Color(){
        return $this->color->get_All_Color();        
    }
     public function get_Color($id){
        return $this->color->get_Color($id);
     }
    public function remove($id){
        return $this->color->remove($id);
    }
    public function remove_b($data) {
        return $this->color->remove_batch($data);
    }
    public function get_Color_info($game_id){
        return $this->color->get_Color_info($game_id);
    }
    public function color_List($game_id){
        return $this->color->color_List($game_id);
    }
}
