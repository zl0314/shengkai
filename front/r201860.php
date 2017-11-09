<?php
/**
 * 2018 俄罗斯问卷
 *
 * User: stepreal
 * Date: 17/9/24
 * Time: 上午2:03
 */


define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
//获取赛事信息
$act = empty($_REQUEST['act']) ? "index" : trim($_REQUEST['act']);
if ($act == "index") {
    $smarty->display("question_paper_r201860.dwt");
} elseif ($act == 'save') {
    $realname = isset($_POST['realname']) ? trim($_POST['realname']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $ticket_type_level = isset($_POST['ticket_type_level']) ? intval($_POST['ticket_type_level']) : '';
    $vip_level_text = isset($_POST['vip_level_text']) ? trim($_POST['vip_level_text']) : '';
    $number_id = isset($_POST['number_id']) ? intval($_POST['number_id']) : '';
    $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';

    if (empty($realname) || empty($phone) || empty($email) || empty($number_id)) {
        echo json_encode(array('code' => 1, 'message' => '信息不完整'));
        return false;
    }
    if ($ticket_type_level != 2) {
        $vip_level_text = '';
    }

    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('number') . " WHERE id = '{$number_id}' LIMIT 1";
    $res = $GLOBALS['db']->getRow($sql);
    $number_name = $res['number_name'];

    $create_time = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'];
    $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
    $sql = "INSERT INTO " . $GLOBALS['ecs']->table('question_paper_2018') . " SET realname = '{$realname}', phone = '{$phone}', email = '{$email}', ticket_type_level = '{$ticket_type_level}', vip_level_text = '{$vip_level_text}', number_id = '{$number_id}', number_name = '{$number_name}', notes = '{$notes}', uid = '{$user_id}', create_time = '{$create_time}', ip = '{$ip}'";
    $res = $GLOBALS['db']->query($sql);
    if ($res) {
        echo json_encode(array('code' => '200'));
        setcookie("r201860", '1|' . $create_time);
        exit;
    } else {
        echo json_encode(array('code' => '2', 'message' => '抱歉,系统繁忙,请重试'));
        exit;
    }
} elseif ($act == 'ajax') {
    $type = !empty($_REQUEST['type']) ? intval($_REQUEST['type']) : 0;
    $grand = !empty($_REQUEST['grand']) ? intval($_REQUEST['grand']) : 0;
    $parent = !empty($_REQUEST['parent']) ? intval($_REQUEST['parent']) : 0;

    $arr['data']['list'] = get_relateds($type, $parent);
    $arr['type'] = $type;

    echo json_encode($arr);
}


function get_relateds($type, $parent)
{
    $arr = array();
    if ($type == 1) {
        $res = $GLOBALS['db']->getAll("select * from sk_schedule where game_id = $parent");
        foreach ($res as $key => $val) {
            $arr[$key]['related_id'] = $val['id'];
            $arr[$key]['related_name'] = $val['sche_name'];

        }
    }
    if ($type == 2) {
        //$res = $GLOBALS['db']->getAll("select n.*,p.pitch_name from sk_number as n , sk_pitch as p where n.pitch_id=p.id AND n.sche_id = $parent");
        $res = $GLOBALS['db']->getAll("select n.*,p.pitch_name from sk_number as n , sk_pitch as p where n.pitch_id=p.id AND n.sche_id in (select id from sk_schedule where game_id = 60);");
        foreach ($res as $key => $val) {
            $arr[$key]['related_id'] = $val['id'];
            $arr[$key]['related_name'] = $val['num_name'] . "--" . $val['pitch_name'];
        }
    }
    return $arr;
}