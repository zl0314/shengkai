<?php

/*
 * 作者：戎青松
 * 时间：11:06:57
 * 
 */

/**
 * Description of GameModel
 *
 * @author Kevin
 */
__load("Model", "model");

class GameModel extends Model
{
    //put your code here
    private $game_key = array(
        'id', 'game_name', "start_time", 'end_time', 'game_text', 'game_img', 'game_logo', 'game_sealogo', 'game_intro', 'template', 'is_insurance','is_type', 'scat_id'
    ), $game;

    public function __construct()
    {
        parent::__construct();
    }

    public function get($id)
    {
        $sql = "SELECT * FROM " . $this->ecs->table("game") . "WHERE id=$id";
        return $this->db->getRow($sql);
    }
    //--hechengbin--start--获取支持退票险的赛事
    public function get_tuipiaoxian($id){
        $sql = "SELECT * FROM " . $this->ecs->table("game") . "WHERE id=$id";
        return $this->db->getRow($sql);
    }
    //--end
    //获取运动类别下的赛事
    public function get_scat_game($id)
    {
        $sql = "SELECT * FROM " .$this->ecs->table("game") . "WHERE scat_id=$id";
        return $this->db->getAll($sql);
    }
     public function get_article($article_id)
    {
        $sql = "SELECT * FROM " . $this->ecs->table("article") . "WHERE article_id=$article_id";
        return $this->db->getRow($sql);
    }
    
    public function getgameidandcityid($id1, $id2)
    {
        $sql = "select goods_name from sk_goods,sk_pitch,sk_region where sk_region.region_id=" . $id2 .
            "and is_ticket=1 and sk_pitch.region_id=sk_region.region_id and sk_pitch.id=sk_goods.pitch_id";

        return $this->db->getAll($sql);
    }

    public function get_all()
    {
        $sql = "SELECT * FROM " . $this->ecs->table("game") . "WHERE is_type=1";
        return $this->db->getAll($sql);
    }

    //获取坐席等级
    public function get_all_rank(){
        $sql = "SELECT distinct rank FROM " . $this->ecs->table("goods");
        return $this->db->getAll($sql);
    }

    public function get_Alls()
    {

        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('game');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        $sql = "SELECT * FROM " . $this->ecs->table("game") . " LIMIT " . $filter['start'] . ",$filter[page_size]";
        $row = $this->db->getAll($sql);
        return array('game' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }

    public function set(Array $array)
    {
        foreach ($this->game_key as $value) {
            if (isset($array[$value])) {
                $this->game[$value] = $array[$value];
            }
        }
    }

    public function save()
    {
        $this->game['start_time'] = strtotime($this->game['start_time']);
        $this->game['end_time'] = strtotime($this->game['end_time']);
        $res = $this->db->autoExecute($this->ecs->table("game"), $this->game, 'INSERT');
        if ($res) {
            return $this->db->insert_id();
        } else {
            return 0;
        }
    }

    public function update()
    {
        $this->game['start_time'] = strtotime($this->game['start_time']);
        $this->game['end_time'] = strtotime($this->game['end_time']);
        $res = $this->db->autoExecute($this->ecs->table("game"), $this->game, 'UPDATE', "id=" . $this->game['id']);
        return $res;
    }

    public function toString()
    {
        var_dump(strtotime($this->game['end_time']));
    }

    public function remove($id)
    {
        //查看当前赛事下是否存在赛程
        $sche_exist = $this->db->getOne("SELECT * FROM " . $this->ecs->table("schedule") . " WHERE game_id=$id");
        if (empty($sche_exist)) {
            $sql = "DELETE  FROM " . $this->ecs->table("game") . " WHERE id=$id";
            return $this->db->query($sql);
        } else {
            return false;
        }
    }

    public function remove_batch($data)
    {
        foreach ($data as $key => $id) {
            //查看当前赛事下是否存在赛程
            $sche_exist = $this->db->getOne("SELECT * FROM " . $this->ecs->table("schedule") . " WHERE game_id=$id");
            if (empty($sche_exist)) {
                $sql = "DELETE  FROM " . $this->ecs->table("game") . " WHERE id=$id";
                $res = $this->db->query($sql);
            }
        }
        return $res;
    }

    public function game_schedule_all($id)
    {
        //查看当前赛事下是否存在赛程
        $sche_exist = $this->db->getOne("SELECT count(*) FROM " . $this->ecs->table("schedule") . " WHERE game_id=$id");
        if ($sche_exist > 0) {
            $sql = "SELECT *  FROM " . $this->ecs->table("schedule") . " AS s LEFT JOIN  " . $this->ecs->table('number') . " AS n ON s.id = n.sche_id WHERE s.game_id=$id GROUP BY n.num_start";
            $res = $this->db->getAll($sql);
        }
        return $res;
    }

    public function get_pitch_all()
    {
        return $res = $this->db->getAll("SELECT * FROM " . $this->ecs->table('pitch') . " ORDER BY id");
    }

    public function get_all_good_for_game($game_id, $region_id)
    {
        if (empty($game_id)) {
            exit("非法数据");
        } else {
            $sql = "SELECT good.*,n.*,p.pitch_name,p.pitch_img,p.big_pitch_img,r.region_name,c.color_value FROM " . $this->ecs->table('goods') . " AS good," . $this->ecs->table('number') . " AS n," . $this->ecs->table('color_manage') . " AS c," . $this->ecs->table('pitch')
                . " AS p LEFT JOIN " . $this->ecs->table('region') . " AS r ON p.region_id=r.region_id WHERE good.number_id=n.id AND c.color_id=n.color_id AND n.pitch_id=p.id AND good.is_real=1 AND good.is_ticket=1 AND good.is_on_sale=1 AND good.is_delete=0 AND good.is_shipping=0";

            if (empty($region_id)) {
                $sql .= " AND  good.game_id={$game_id}";
            } else {
                $sql .= " AND good.game_id={$game_id}" . " AND p.region_id={$region_id}";
            }
            $sql .= " order by good.shop_price";
            return $this->db->getAll($sql);
        }
    }

    public function get_hot_game($number)
    {
        $sql = "SELECT good.goods_id FROM " . $this->ecs->table('goods') . " AS good," . $this->ecs->table('number') . " AS n," . $this->ecs->table('pitch')
            . " AS p WHERE good.number_id=n.id AND n.pitch_id=p.id AND good.is_real=1 AND is_ticket=1 AND is_hot = 1AND good.is_on_sale=1 AND good.is_delete=0 AND good.is_shipping=0 limit $number";
        return $this->db->getAll($sql);
    }

    public function  get_region_list($game_id, $sche_id)
    {
        $sql = "
        SELECT
 r.*
FROM
	" . $this->ecs->table('schedule') . " as s,
	" . $this->ecs->table('number') . " as n,
	" . $this->ecs->table('pitch') . "as p,
	" . $this->ecs->table('region') . "as r
WHERE
	r.region_id=p.region_id
AND
	p.id=n.pitch_id
AND
	n.sche_id=s.id
AND
	s.game_id=$game_id ";
        if (!empty($sche_id)) {
            $sql=$sql." AND s.id='{$sche_id}' ";
        }
        $sql = $sql . "group by r.region_id ";
        return $this->db->getAll($sql);
    }

    public function get_name_list()
    {
        return $this->db->getAll("SELECT * FROM" . $this->ecs->table('game'));
    }

    public function get_all_good_for_sche($game_id, $region_id, $sche_id)
    {
        if (empty($game_id)) {
            exit("非法数据");
        } else {

            $sql = "SELECT good.*,n.*,p.pitch_name,p.big_pitch_img,p.pitch_img,r.region_name,c.color_value FROM " . $this->ecs->table('goods') . " AS good," . $this->ecs->table('number') . " AS n," . $this->ecs->table('color_manage') . " AS c," . $this->ecs->table('pitch')
                    . " AS p LEFT JOIN ".$this->ecs->table('region')." AS r ON p.region_id=r.region_id WHERE good.number_id=n.id AND c.color_id=n.color_id AND n.pitch_id=p.id AND good.is_real=1 AND is_ticket=1 AND good.is_on_sale=1 AND good.is_delete=0 AND good.is_shipping=0 AND good.sche_id=$sche_id  "
            ;
            if (empty($region_id)) {
                $sql .= " AND  good.game_id=$game_id";
            } else {
                $sql .= " AND good.game_id=$game_id" . " AND p.region_id=$region_id";
            }
            $sql .= " order by good.shop_price";
            return $this->db->getAll($sql);
        }
    }

    //运动类别---start
    public function get_scat_game_goods($game_id, $region_id, $scat_id)
    {
        if (empty($game_id)) {
            exit("非法数据");
        } else {

            $sql = "SELECT DISTINCT good.*,n.*,p.pitch_name,p.big_pitch_img,p.pitch_img,r.region_name,c.color_value FROM " . $this->ecs->table('goods') . " AS good," . $this->ecs->table('number') . " AS n,".$this->ecs->table('game')." AS g," . $this->ecs->table('color_manage') . " AS c," . $this->ecs->table('pitch')
                . " AS p LEFT JOIN ".$this->ecs->table('region')." AS r ON p.region_id=r.region_id WHERE good.number_id=n.id AND c.color_id=n.color_id AND n.pitch_id=p.id AND good.is_real=1 AND is_ticket=1 AND good.is_on_sale=1 AND good.is_delete=0 AND good.is_shipping=0";
            if (empty($region_id)) {
                $sql .= " AND  good.game_id=g.id "." AND g.id=$game_id "." AND g.scat_id=$scat_id";
            } else {
                $sql .= " AND good.game_id=$game_id" . " AND p.region_id=$region_id";
            }
            $sql .= " order by good.shop_price";
//            echo $sql;die;
            return $this->db->getAll($sql);
        }
    }
    //运动类别 --end
    
    public function get_by_scat($scat_id)
    {
        return $this->db->getAll("SELECT id,game_name FROM" . $this->ecs->table('game')." where scat_id = $scat_id AND is_type=1");
    }
    //获取运动类别--hechengbin
    public function get_sportcat_list(){
        $sql = "SELECT * FROM ".$this->ecs->table('sportcat')." WHERE is_display=1";
        return $this->db->getAll($sql);
    }
}
