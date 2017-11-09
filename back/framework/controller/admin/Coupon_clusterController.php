<?php

/**
 * Created by PhpStorm.
 * User: stepreal
 * Date: 17/9/28
 * Time: 上午1:13
 */
__load("Controller", "controller");

class Coupon_clusterController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        __load("CouponClusterService");
        $this->coupon_cluster_service = new CouponClusterService();
    }

    public function index()
    {
        $this->assign('ur_here', "优惠券活动列表");
        $this->assign('action_link', array('text' => "添加优惠券活动", 'href' => 'route.php?con=coupon_cluster&act=add'));
        $coupon_cluster_list = $this->coupon_cluster_service->get_coupon_cluster_list();
//        echo '<pre>';print_r($coupon_cluster_list);exit;
        $this->assign('coupon_cluster_list', $coupon_cluster_list['list']);
        $this->assign("record_count", $coupon_cluster_list['record_count']);
        $this->assign("page_count", $coupon_cluster_list['page_count']);
        $this->assign("filter", $coupon_cluster_list['filter']);
        $this->assign("full_page", 1);
        $this->display("coupon_cluster/coupon_cluster_list.html");
    }

    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function index_query()
    {
        $airs = $this->coupon_cluster_service->get_all_coupon_cluster_list();
        $this->assign('airs', $airs['airs']);
        $this->assign("record_count", $airs['record_count']);
        $this->assign("page_count", $airs['page_count']);
        $this->assign("filter", $airs['filter']);
        make_json_result($this->fetch("coupon_cluster/coupon_cluster_list.html"), '', array('filter' => $airs['filter'], 'page_count' => $airs['page_count']));
    }

    public function add()
    {
        $this->assign('ur_here', "添加优惠券活动");
        $this->assign('action_link', array('text' => "优惠券活动列表", 'href' => 'route.php?con=coupon_cluster'));
        $this->assign('act', "add");
        $this->display("coupon_cluster/coupon_cluster_info.html");
    }

    public function remove()
    {
        $res = $this->coupon_cluster_service->remove($_GET['coupon_cluster_id']);
        $link = array(array('text' => "优惠券活动列表", 'href' => 'route.php?con=coupon_cluster'));
        if ($res) {
            sys_msg("删除成功", 0, $link);
        } else {
            sys_msg("删除失败", 0, $link);
        }
    }

    /**
     * 添加或编辑
     */
    public function edit()
    {
        $act = empty($_POST['act']) ? "add" : trim($_POST['act']);
        if ($act == "add") {
            $arrInput = $_POST;
            if (empty($arrInput['demand_count']) || $arrInput['demand_count'] <= 0) {
                sys_msg("生成数量不能小于0", 0);
            }
            if (empty($arrInput['limitation'])) {
                $arrInput['limitation'] = 1;
            }
            if (empty($arrInput['coupon_key'])) {
                $arrInput['coupon_key'] = date('ymdHis');
            }
            if (empty($arrInput['expire_day'])) {
                $arrInput['expire_day'] = 30;
            }
            if (empty($arrInput['min_amount']) || $arrInput['min_amount'] <= 0) {
                sys_msg("最低启用金额不能小于0", 0);
            }
            if (empty($arrInput['amount']) || $arrInput['amount'] <= 0) {
                sys_msg("优惠金额不能小于0", 0);
            }
            if ($arrInput['min_amount'] <= $arrInput['amount']) {
                sys_msg("优惠金额不能大于启用金额", 0);
            }
            if (empty($arrInput['end_time'])) {
                $arrInput['end_time'] = date('Y-m-d H:i:s', strtotime("+3 years"));
            }
            $res = $this->coupon_cluster_service->add($arrInput);
            $link = array(array('text' => "优惠券活动列表", 'href' => 'route.php?con=coupon_cluster'), array('text' => "继续添加优惠券活动", 'href' => 'route.php?con=coupon_cluster&act=add'));
            if (empty($res)) {
                sys_msg("添加失败", 0, $link);
            } else {
                sys_msg("添加成功", 0, $link);
            }
        } else {
            $coupon_cluster_id = isset($_POST['coupon_cluster_id']) ? intval($_POST['coupon_cluster_id']) : 0;
            $coupon_cluster = $this->coupon_cluster_service->get_coupon_cluster_by_id($coupon_cluster_id);
            if (empty($coupon_cluster)) {
                sys_msg("优惠券活动不存在", 0);
            }

            $res = $this->coupon_cluster_service->update($_POST);
            $link = array(array('text' => "优惠券活动列表", 'href' => 'route.php?con=coupon_cluster'));
            if ($res) {
                sys_msg("更新成功", 0, $link);
            } else {
                sys_msg("更新失败", 0, $link);
            }
        }
    }

    /**
     * 更新
     */
    public function update()
    {
        $coupon_cluster_id = isset($_GET['coupon_cluster_id']) ? intval($_GET['coupon_cluster_id']) : 0;
        $coupon_cluster = $this->coupon_cluster_service->get_coupon_cluster_by_id($coupon_cluster_id);
        if (empty($coupon_cluster)) {
            sys_msg("优惠券活动不存在", 0);
        }

        $this->assign('coupon_cluster', $coupon_cluster);
        $this->assign('ur_here', "编辑优惠券活动");
        $this->assign('action_link', array('text' => "优惠券活动列表", 'href' => 'route.php?con=coupon_cluster'));
        $this->assign('act', "update");
        $this->display("coupon_cluster/coupon_cluster_info.html");
    }

    /**
     * 批量删除
     */
    public function batch_drop()
    {
        $link = array(array('text' => "优惠券活动列表", 'href' => 'route.php?con=coupon_cluster'));
        $res = $this->coupon_cluster_service->remove_b($_POST['checkboxes']);
        if ($res) {
            sys_msg("批量删除优惠券活动成功", 0, $link);
        } else {
            sys_msg("批量删除优惠券活动失败", 0, $link);
        }
    }

    /**
     * 兑换优惠券
     */
    public function exchange()
    {
        $coupon_cluster_id = isset($_GET['coupon_cluster_id']) ? intval($_GET['coupon_cluster_id']) : 0;
        $coupon_cluster = $this->coupon_cluster_service->get_coupon_cluster_by_id($coupon_cluster_id);
        if (empty($coupon_cluster)) {
            sys_msg("优惠券活动不存在", 0);
        }
        $this->assign('coupon_cluster', $coupon_cluster);
        $this->display("coupon_cluster/coupon_cluster_exchange.html");
    }
}