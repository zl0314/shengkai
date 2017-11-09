<?php 



/**
 * 
 */
class GoodsService{

	private $goods,$goods_info;


	public function __construct(){
		__load('GoodsModel','model');
		$this->goods=new GoodsModel();
	}



	/**
	 * [check_goods_number description] 验证商品数量是否合法
	 * @param  [type] $goods_id [description]
	 * @param  [type] $number   [description]
	 * @return [type]           [description]
	 */
	public function check_goods_info($goods_id,$_POST,$SESS_ID,$goods_attr_id)

	{

            $goods_info= $this->goods->get($goods_id);
            $goods_info['session_id']=$SESS_ID;
            $goods_info['goods_number']=$_POST['goods_number'];
            $goods_info['goods_price']=$_POST['goods_price'];
            $goods_info['goods_attr_id']=$goods_attr_id;  
            if(empty( $goods_info)){
                return false;
            }
            foreach( $goods_info as $key=>$value){
                   if($key=='is_delete'&&$value==1){                  
                          return false;                  
                 }
                   if($key=='is_on_sale'&&$value==0){                    
                          return false;                   
                 }
                   if($key=='is_ticket'&&$value==1){
                      $goods_info['goods_type']="ticket";
                 }
                  else if($key=='is_hotel'&&$value==1){
                      $goods_info['goods_type']="hotel";
                 }
                  else if($key=='is_plane'&&$value==1){
                      $goods_info['goods_type']="plane";
                 }
                  else if($key=='is_goods'&&$value==1){
                      $goods_info['goods_type']="goods";
                 }
//                   if($key=='goods_number'){
//                     if($value!=1){
//                          return false;
//                     }
//                 }
            }
 
          $this->goods->set($goods_info);
          return $this->goods->save($goods_info);
          exit;
	}
        

	/**
	 * [check_titck_type description] 检测是否为球票
	 * @return [type] [description]
	 */
	public function check_titck_type(){
		if($this->goods_info['is_ticket']!=1){
			return false;
		}else{
			return true;
		}
	}
        
        public function get_ticket_info($goods_id,$game_id){
            return $this->goods->get_ticket_info($goods_id,$game_id);
        }
        public function add_hotel($data){
            return $this->goods->add_hotel($data);
        }
        public function get_goods_info($goods_id){
            return $this->goods->get($goods_id);
        }
        public function get_goods_product($goods_id,$attr_id){
            return $this->goods->get_goods_product($goods_id,$attr_id);
        }
        public function  get_goods_attr($attr_id){
            return $this->goods->get_goods_attr($attr_id);

        }


}




 ?>