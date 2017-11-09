<?php

/**
 * Created by PhpStorm.
 * User: stepreal
 * Date: 17/9/28
 * Time: 上午1:14
 */
__load("Controller", "controller");

class User_couponController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        __load("CouponClusterService");
        $this->coupon_cluster_service = new CouponClusterService();
        __load("UserCouponService");
        $this->user_coupon_service = new UserCouponService();
        __load("CouponService");
        $this->coupon_service = new CouponService();
    }

    public function index()
    {
        $coupon_cluster_id = isset($_GET['coupon_cluster_id']) ? intval($_GET['coupon_cluster_id']) : 0;

        $this->assign('ur_here', "用户优惠券列表");
        $this->assign('action_link', array('text' => "返回优惠券列表", 'href' => 'route.php?con=coupon_cluster'));
        $filter = array(
            'coupon_cluster_id' => $coupon_cluster_id,
            'user_id' => isset($_GET['user_id']) ? trim($_GET['user_id']) : '',
        );
        $user_coupon_list = $this->user_coupon_service->get_user_coupon_list($filter);
//        echo '<pre>';print_r($user_coupon_list);exit;
        $this->assign('user_coupon_list', $user_coupon_list['list']);
        $this->assign("record_count", $user_coupon_list['record_count']);
        $this->assign("page_count", $user_coupon_list['page_count']);
        $this->assign("filter", $user_coupon_list['filter']);
        $this->assign("full_page", 1);
        $this->display("user_coupon/user_coupon_list.html");
    }

    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function index_query()
    {
        $airs = $this->user_coupon_service->get_all_user_coupon_list();
        $this->assign('airs', $airs['airs']);
        $this->assign("record_count", $airs['record_count']);
        $this->assign("page_count", $airs['page_count']);
        $this->assign("filter", $airs['filter']);
        make_json_result($this->fetch("user_coupon/user_coupon_list.html"), '', array('filter' => $airs['filter'], 'page_count' => $airs['page_count']));
    }

    public function add()
    {
        $this->assign('ur_here', "添加用户优惠券");
        $this->assign('action_link', array('text' => "用户优惠券列表", 'href' => 'route.php?con=coupon_cluster'));
        $this->assign('act', "add");
        $this->display("user_coupon/coupon_cluster_info.html");
    }

    public function remove()
    {
        $res = $this->user_coupon_service->remove($_GET['coupon_cluster_id']);
        $link = array(array('text' => "用户优惠券列表", 'href' => 'route.php?con=coupon_cluster'));
        if ($res) {
            sys_msg("删除成功", 0, $link);
        } else {
            sys_msg("删除失败", 0, $link);
        }
    }

    /**
     * 批量删除
     */
    public function batch_drop()
    {
        $link = array(array('text' => "用户优惠券列表", 'href' => 'route.php?con=coupon_cluster'));
        $res = $this->user_coupon_service->remove_b($_POST['checkboxes']);
        if ($res) {
            sys_msg("批量删除用户优惠券成功", 0, $link);
        } else {
            sys_msg("批量删除用户优惠券失败", 0, $link);
        }
    }

    /**
     * 兑换
     */
    public function exchange()
    {
        $coupon_key = isset($_REQUEST['coupon_key']) ? trim($_REQUEST['coupon_key']) : '';
        $user_id = isset($_REQUEST['user_id']) ? trim($_REQUEST['user_id']) : '';
        $coupon_cluster = $this->coupon_cluster_service->get_by_coupon_key($coupon_key);
        if (empty($coupon_cluster) || $coupon_cluster['deleted'] == 1) {
            sys_msg("优惠券码错误");
        }
        if ($coupon_cluster['exchange_count'] >= $coupon_cluster['demand_count']) {
            sys_msg("优惠券已兑换完了");
        }

        $this->coupon_service->exchange($coupon_cluster['coupon_cluster_id'], $user_id);
    }

    /**
     * 取得用户可用的优惠券
     *
     * @return array
     */
    public function get_user_order_available_coupon_list($arrInput)
    {
        $data = array();
        $order_amount = isset($arrInput['order_amount']) ? $arrInput['order_amount'] : 0;
        if (!$order_amount) {
            echo json_encode($data);
            return false;
        }

        if (empty($arrInput['user_id'])) {
            echo json_encode($data);
            return false;
        }

        $res = $this->user_coupon_service->get_user_order_available_coupon_list($arrInput);
        return $res;
    }
}