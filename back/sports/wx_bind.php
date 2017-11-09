<?php
/**
 * Created by PhpStorm.
 * User: hechengbin
 * Date: 2017/1/10 0010
 * Time: 9:51
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
/*
 * 微信用户列表
 */
if($_REQUEST['act'] == "wx_list"){
    /* 检查权限 */
    admin_priv('users_manage');
    //获取微信用户列表
    $user_wx_list = user_wx_list();

    $smarty->assign('user_list',    $user_wx_list['user_list']);
    $smarty->assign('filter',       $user_wx_list['filter']);
    $smarty->assign('record_count', $user_wx_list['record_count']);
    $smarty->assign('page_count',   $user_wx_list['page_count']);
    $smarty->assign('act', "wx_list");
    $smarty->assign('full_page',    1);

    $smarty->display('user_wx_list.htm');
}

/*------------------------------------------------------ */
//-- ajax返回用户列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $user_wx_list = user_wx_list();

    $smarty->assign('user_list',    $user_wx_list['user_list']);
    $smarty->assign('filter',       $user_wx_list['filter']);
    $smarty->assign('record_count', $user_wx_list['record_count']);
    $smarty->assign('page_count',   $user_wx_list['page_count']);

    $sort_flag  = sort_flag($user_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('user_wx_list.htm'), '', array('filter' => $user_wx_list['filter'], 'page_count' => $user_wx_list['page_count']));
}

/*
 * 返回用户列表数据
 */
function user_wx_list(){
    $result = get_filter();
    if ($result === false){
        /*
         * 过滤条件
         */
        $filter['username'] = empty($_REQUEST['username']) ? '' : trim($_REQUEST['username']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['username'] = json_str_iconv($filter['username']);
        }
        $filter['nickname'] = empty($_REQUEST['nickname']) ? '' : trim($_REQUEST['nickname']);
        $filter['sex'] = empty($_REQUEST['sex']) ? 0 : intval($_REQUEST['sex']);
        $filter['num_start'] = empty($_REQUEST['num_start']) ? 0 : strtotime($_REQUEST['num_start']);
        $filter['country'] = empty($_REQUEST['country']) ? '' : trim($_REQUEST['country']);
        $filter['province'] = empty($_REQUEST['province']) ? '' : trim($_REQUEST['province']);
        $filter['city'] = empty($_REQUEST['city']) ? '' : trim($_REQUEST['city']);

        $filter['sort_by']    = empty($_REQUEST['sort_by'])    ? 'bind_time' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC'     : trim($_REQUEST['sort_order']);

        $where = 1;

        if($filter['username']){
            $where .= " AND u.user_id=wb.user_id AND u.user_name ='".$filter['username']."'";
        }
        if($filter['nickname']){
            $where .= " AND wb.name ='".$filter['nickname']."'";
        }
        if($filter['sex'] != 0){
            $where .= " AND wb.sex=".$filter['sex'];
        }
        if($filter['num_start']){
            $filter['bind_time'] = strtotime($filter['num_start']);
            $where .= " AND wb.bind_time >= ".$filter['num_start'];
        }
        if($filter['country']){
            $where .= " AND wb.country = '".$filter['country']."'";
        }
        if($filter['province']){
            $where .= " AND wb.province = '".$filter['province']."'";
        }
        if($filter['city']){
            $where .= " AND wb.city = '".$filter['city']."'";
        }
        /*
         * 记录总数
         */
        if($filter['username'] || $filter['nickname'] || $filter['sex'] || $filter['num_start'] || $filter['country'] || $filter['province'] || $filter['city']){
//            $sql = "SELECT count(name) FROM ".$GLOBALS['ecs']->table('wx_bind')." AS wb,".$GLOBALS['ecs']->table('users')." AS u WHERE ".$where ." AND wb.user_id=u.user_id ";
            $sql = "SELECT count(id) FROM ".$GLOBALS['ecs']->table('wx_bind')." WHERE ".$where ;
            
        }else{
            $sql = "SELECT count(id) FROM ".$GLOBALS['ecs']->table('wx_bind')." WHERE ".$where ;
        }

        $filter['record_count'] = $GLOBALS['db']->getOne($sql);


        if($filter['username']){
            $joinSql = ",".$GLOBALS['ecs']->table('users')." AS u ";
        }

        /* 分页大小 */
        $filter = page_and_size($filter);

        $sql = "SELECT wb.* FROM ".$GLOBALS['ecs']->table('wx_bind') ." AS wb".$joinSql ." WHERE ". $where." ORDER by " . $filter['sort_by'] . ' ' . $filter['sort_order'] . " LIMIT " . $filter['start'] . ',' . $filter['page_size'];;
//        echo $sql;die;
        set_filter($filter, $sql);
    }else{
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }

    $user_list = $GLOBALS['db']->getAll($sql);
    foreach($user_list as $key=>$val){
        $user_list[$key]['user_info'] = getUserInfo($val['user_id']);
        $user_list[$key]['user_name'] = $GLOBALS['db']->getRow("SELECT user_name FROM sk_users WHERE user_id=$val[user_id]");
        $user_list[$key]['bind_time'] = date("Y-m-d H:i:s",$val['bind_time']);
    }

    $arr = array('user_list' => $user_list, 'filter' => $filter,
        'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

function getUserInfo($user_id){
    return $GLOBALS['db']->getRow("SELECT * FROM sk_users WHERE user_id=$user_id");
}
?>