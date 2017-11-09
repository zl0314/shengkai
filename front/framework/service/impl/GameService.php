<?php

/*
 * 作者：戎青松
 * 时间：14:45:52
 * 
 */

/**
 * Description of GameService
 *
 * @author Kevin
 */
class GameService {

    //put your code here
    private $game;

    public function __construct() {
        __load("GameModel", "model");
        $this->game = new GameModel();
    }

    public function get_list() {
        return $this->game->get_all();
    }

    //获取坐席等级
    public function get_all_rank(){
        return $this->game->get_all_rank();
    }

    public function get_game_list() {
        return $this->game->get_Alls();
    }
    
    public function get_game($id) {
        return $this->game->get($id);
    }
    //获取运动类别下的赛事
    public function get_scat_game($id){
        return $this->game->get_scat_game($id);
    }
    
     public function get_aritcle($aritcle_id) {
        return $this->game->get_article($aritcle_id);
    }
    public function get_gameandcity($id1,$id2) {
        return $this->game->get($id1,$id2);
    }

    public function add_game() {
        $this->game->set($_POST);
        return $this->game->save();
    }

    public function update_game() {
        $this->game->set($_POST);
        return $this->game->update();
    }

    public function remove_game($id) {
        $this->game->set($_POST);
        return $this->game->remove($id);
    }

    public function remove_b($data) {
        return $this->game->remove_batch($data);
    }

    public function get_game_schedule($id) {
        return $this->game->game_schedule_all($id);
    }

    public function get_game_pitch() {
        return $this->game->get_pitch_all();
    }

    public function get_game_info($game, $region, $rank = '') {
        $game_info = $this->game->get($game);
        if (empty($game_info)) {
            exit(非法数据);
        }
        return $this->game->get_all_good_for_game($game,$region, $rank);
    }
    public function get_hot_game($number) {
        $number = empty($number) ? 4 : intval($number);
        return $this->game->get_hot_game($number);
    }

    public function get_region_list($game_id,$sche_id){
        return $this->game->get_region_list($game_id,$sche_id);
    }
   public function get_name_list(){
        return $this->game->get_name_list();
   }
   public function get_game_name($game_id){
        return $this->game->get($game_id);
   }
    //--hechengbin --start---获取支持退票险的赛事
    public function get_tuipiaoxian($game_id){
        return $this->game->get_tuipiaoxian($game_id);
    }
    //--end--
   public function get_game_sche($game, $region,$sche_id, $rank = '') {
       $game_info = $this->game->get($game);
       if (empty($game_info)) {
            exit(非法数据);
       }
        return $this->game->get_all_good_for_sche($game, $region,$sche_id, $rank);
   }
    //运动类别--start
   public function get_scat_game_goods($game,$region,$scat_id){
       $game_info = $this->game->get($game);
       if (empty($game_info)) {
           exit(非法数据);
       }
       return $this->game->get_scat_game_goods($game, $region,$scat_id);
   }
    //运动类别--end
   public function get_by_scat($scat_id){
        return $this->game->get_by_scat($scat_id);
   }
    //获取运动类别--hechengbin
    public function get_sportcat_list(){
        return $this->game->get_sportcat_list();
    }
}
