<?php

/**
 * Created by PhpStorm.
 * User: stepreal
 * Date: 17/9/28
 * Time: 上午1:14
 */
__load("Controller", "controller");

class CouponController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        __load("CouponService");
        $this->coupon_service = new couponService();
        __load("CouponClusterService");
        $this->coupon_cluster_service = new CouponClusterService();
    }

    public function index()
    {
        $this->assign('ur_here', "优惠券列表");
        $this->assign('action_link', array('text' => "添加优惠券", 'href' => 'route.php?con=coupon&act=add'));
        $coupon_list = $this->coupon_service->get_coupon_list();
        $this->assign('coupon_list', $coupon_list['list']);
        $this->assign("record_count", $coupon_list['record_count']);
        $this->assign("page_count", $coupon_list['page_count']);
        $this->assign("filter", $coupon_list['filter']);
        $this->assign("full_page", 1);
        $this->display("coupon/coupon_list.html");
    }

    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function index_query()
    {
        $airs = $this->coupon_service->get_all_coupon_list();
        $this->assign('airs', $airs['airs']);
        $this->assign("record_count", $airs['record_count']);
        $this->assign("page_count", $airs['page_count']);
        $this->assign("filter", $airs['filter']);
        make_json_result($this->fetch("coupon/coupon_list.html"), '', array('filter' => $airs['filter'], 'page_count' => $airs['page_count']));
    }

    public function add()
    {
        $this->assign('ur_here', "添加优惠券");
        $this->assign('action_link', array('text' => "优惠券列表", 'href' => 'route.php?con=coupon'));
        $this->assign('act', "add");
        $this->assign('countries', $this->coupon_service->get_AllRegion(0));
        $this->display("coupon/coupon_info.html");
    }

    public function remove()
    {
        $res = $this->coupon_service->remove($_GET['id']);
        $link = array(array('text' => "优惠券列表", 'href' => 'route.php?con=coupon'));
        if ($res) {
            sys_msg("删除成功", 0, $link);
        } else {
            sys_msg("删除失败", 0, $link);
        }
    }

    public function update()
    {
        $coupon_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $coupon = $this->coupon_service->get_coupon_by_id($coupon_id);
        if (empty($coupon)) {
            sys_msg("优惠券不存在", 0);
        }

        $this->assign('coupon', $coupon);
        $this->assign('ur_here', "编辑优惠券");
        $this->assign('action_link', array('text' => "优惠券列表", 'href' => 'route.php?con=coupon'));
        $this->assign('act', "update");
        $this->display("coupon/coupon_info.html");
    }

    public function batch_drop()
    {
        $link = array(array('text' => "优惠券列表", 'href' => 'route.php?con=coupon'));
        $res = $this->coupon_service->remove_b($_POST['checkboxes']);
        if ($res) {
            sys_msg("批量删除优惠券成功", 0, $link);
        } else {
            sys_msg("批量删除优惠券失败", 0, $link);
        }
    }

    /**
     * 生成优惠券
     */
    public function generate()
    {
        $coupon_cluster_id = isset($_GET['coupon_cluster_id']) ? intval($_GET['coupon_cluster_id']) : 0;
        $coupon_cluster = $this->coupon_cluster_service->get_coupon_cluster_by_id($coupon_cluster_id);
        if (empty($coupon_cluster)) {
            sys_msg("优惠券活动不存在", 0);
        }
        $fp = fopen(TEMP_PATH . "/coupon_generate.lock", "r+");
        if (!flock($fp, LOCK_EX | LOCK_NB)) {
            sys_msg("正在生成中,请勿重复操作", 0);
        }
        $this->coupon_service->generate($coupon_cluster_id);
        sys_msg("生成优惠券成功", 0);
    }
}