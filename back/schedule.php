<?php
/**
 *  赛程
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
$act = $_REQUEST['act'];
if($act == 'buy'){
   $schedules  = rtrim($_POST['schedules'],',');
   $sql = "SELECT * FROM ".$ecs->table('number')." AS n LEFT JOIN ".$ecs->table('num_team')." AS nt ON n.id = nt.num_id LEFT JOIN ".$ecs->table('goods')." AS g ON g.number_id = n.id WHERE n.id in('$schedules')";
}else{
/* 所有的赛场 */
$sql = "SELECT * FROM ".$ecs->table('pitch');
$pitch = $db->getAll($sql);
$table = '<tr bgcolor="#65beec" style="color: white;">';
$table .= '<th width="35px">&nbsp;</th>';
foreach ($pitch AS $pitch_info){
    $table .= "<th>".$pitch_info['pitch_name']."</th>";
}
$table .= "</tr>";

//给 $pitch 压入一数组
array_unshift($pitch,array('pitch_name'=>'&nbsp;'));

/* 所有赛程 */
$sql = "SELECT * FROM ".$ecs->table('schedule');
$schedule = $db->getAll($sql);
foreach($schedule AS $sche){   
    $sql = "SELECT * FROM ".$ecs->table('number')." WHERE sche_id = '$sche[id]'";
    $number = $db->getAll($sql);
    foreach ($number as $key=>$data){
        foreach ($pitch as $k=>$pitch_info){      
            if($data['pitch_id'] == $pitch_info['id']){
              $data['pitchid'] = $k;                       
            }
        }
        $number[$key] = $data;
    }
   $table .= "<tr bgcolor='#005798'><td colspan='".(count($pitch))."' style='text-align:center;font-weight:bold;color:white;'>".$sche['sche_name']."</td></tr>";       
    foreach ($number AS $num_info){    
        $table .= '<tr>';
        for($i=0;$i<count($pitch);$i++){
            if($i == 0){
               $table .= '<td bgcolor="#005798" style="color:white;">'.local_date("D d F",local_strtotime($num_info['num_start'])).'</td>';
            }else{
                if($num_info['pitchid'] == $i){
                    $table .= '<td><div class="grid-session_no" id="grid_'.$num_info['id'].'" onclick="number('.'grid_'.$num_info['id'].','.$num_info['id'].')">'.$num_info['num_name'].'</div></td>';
                }else{
                    $table .= '<td></td>';
                }
            }
        }
        $table .= '</tr>';                 
    }    
}
$smarty->assign("table_info",$table);
$smarty->display('schedule.dwt');
}
?>