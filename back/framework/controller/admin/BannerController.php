<?php

/*
 * 作者：戎青松
 * 时间：9:59:10
 * 
 */

/**
 * Description of PitchController
 *
 * @author Kevin
 */
__load("Controller", "controller");

class BannerController extends Controller
{

    //put your code here
    private $banner;

    public function __construct()
    {
        parent::__construct();
        __load("BannerService");
        $this->banner = new BannerService();
    }

    /**
     * Banner 列表
     */
    public function index()
    {
        $this->assign('ur_here', "Banner列表");
        $this->assign('action_link', array('text' => "添加Banner", 'href' => 'route.php?con=banner&act=add_place'));
        $place_all = $this->banner->get_place_list();
        $this->assign('place_all', $place_all['place_all']);
        $this->assign("record_count", $place_all['record_count']);
        $this->assign("page_count", $place_all['page_count']);
        $this->assign("filter", $place_all['filter']);
        $this->assign("full_page", 1);
        $this->display("banner/place_list.html");

    }


    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function index_query()
    {
        $place_all = $this->banner->get_place_list();
        $this->assign('place_all', $place_all['place_all']);
        $this->assign("record_count", $place_all['record_count']);
        $this->assign("page_count", $place_all['page_count']);
        $this->assign("filter", $place_all['filter']);
        make_json_result($this->fetch("banner/place_list.html"), '', array('filter' => $place_all['filter'], 'page_count' => $place_all['page_count']));
    }

    public function add_place()
    {
        $this->assign('ur_here', "Banner添加");
        $this->assign('act', "insert");
        $this->assign('action_link', array('text' => "Banner列表", 'href' => 'route.php?con=banner'));
        $this->display("banner/place_info.html");
    }

    public function add_banner()
    {
        if (empty($_GET['place_id'])) {
            $link = array(array('text' => "Banner列表", 'href' => 'route.php?con=banner'));
            sys_msg("非法数据", 0, $link);
        } else {
            $this->assign('ur_here', "Banner图添加");
            $this->assign('place_id', $_GET['place_id']);
            $this->assign('act', "insert");
            $this->assign('action_link', array('text' => "Banner图列表", 'href' => 'route.php?con=banner&act=banner_list&place_id=' . $_GET['place_id']));
            $this->display("banner/banner_info.html");
        }
    }

    public function update_place()
    {
        if (empty($_GET['id'])) {
            $link = array(array('text' => "Banner列表", 'href' => 'route.php?con=banner'));
            sys_msg("非法数据", 0, $link);
        } else {
            $place = $this->banner->get_place($_GET['id']);
            if ($place) {
                $this->assign('ur_here', "Banner编辑");
                $this->assign('act', "update");
                $this->assign('place', $place);
                $this->assign('action_link', array('text' => "Banner列表", 'href' => 'route.php?con=banner'));

                $this->display("banner/place_info.html");
            } else {
                $link = array(array('text' => "Banner列表", 'href' => 'route.php?con=banner'));
                sys_msg("非法数据", 0, $link);
            }
        }
    }

    public function update_banner()
    {
        if (empty($_GET['id'])) {
            $link = array(array('text' => "Banner列表", 'href' => 'route.php?con=banner'));
            sys_msg("非法数据", 0, $link);
        } else {
            $banner = $this->banner->get_banner($_GET['id']);
            if ($banner) {
                $this->assign('ur_here', "Banner图编辑");
                $this->assign('act', "update");
                $this->assign('banner', $banner);
                $this->assign('place_id', $_GET['place_id']);
                $this->assign('action_link', array('text' => "Banner图列表", 'href' => 'route.php?con=banner&act=banner_list&place_id=' . $_GET['place_id']));

                $this->display("banner/banner_info.html");
            } else {
                $link = array(array('text' => "Banner列表", 'href' => 'route.php?con=banner'));
                sys_msg("非法数据", 0, $link);
            }
        }
    }

    public function banner_list()
    {
        if (empty($_GET['place_id'])) {
            $link = array(array('text' => "Banner列表", 'href' => 'route.php?con=banner'));
            sys_msg("非法数据", 0, $link);
        } else {
            $banners = $this->banner->get_banners();
            $this->assign('ur_here', "Banner图列表");
            $this->assign('banners', $banners['banner']);
            $this->assign("record_count", $banners['record_count']);
            $this->assign("page_count", $banners['page_count']);
            $this->assign("filter", $banners['filter']);
            $this->assign("full_page", 1);
            $this->assign('place_id', $_GET['place_id']);
            $this->assign('action_link', array('text' => "Banner列表", 'href' => 'route.php?con=banner'));
            $this->assign('action_link2', array('text' => "添加banner图", 'href' => 'route.php?con=banner&act=add_banner&place_id=' . $_GET['place_id']));
            $this->display("banner/banner_list.html");
        }
    }

    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function banner_list_query()
    {
        $banners = $this->banner->get_banners();
        $this->assign('banners', $banners['banner']);
        $this->assign("record_count", $banners['record_count']);
        $this->assign("page_count", $banners['page_count']);
        $this->assign("filter", $banners['filter']);
        make_json_result($this->fetch("banner/banner_list.html"), '', array('filter' => $banners['filter'], 'page_count' => $banners['page_count']));
    }

    public function banner_edit()
    {
        if (empty($_GET['place_id'])) {
            $link = array(array('text' => "Banner列表", 'href' => 'route.php?con=banner'));
            sys_msg("非法数据", 0, $link);
        } else {
            $link = array(array('text' => "Banner图列表", 'href' => 'route.php?con=banner&act=banner_list&place_id=' . $_GET['place_id']));
            if ("insert" == $_POST['act']) {
                $data = array(
                    "img_url" => $_POST['img_url'],
                    "status" => $_POST['status'],
                    "place_id" => $_GET['place_id'],
                    "max_title" => $_POST['max_title'],
                    "min_title" => $_POST['min_title'],
                    "sequence" => $_POST['sequence'],
                    "link" => $_POST['link'],
                    "mobile_link" => $_POST['mobile_link'],
                    "video_link"=>$_POST['video_link']
                );
                $res = $this->banner->insert_banner($data);
                if ($res) {
                    admin_log(addslashes($data['img_url']), 'add', 'banners');// 记录日志
                    sys_msg("操作成功", 0, $link);
                } else {
                    sys_msg("操作失败", 0, $link);
                }
            } else {
                $data = array(
                    "img_url" => $_POST['img_url'],
                    "status" => $_POST['status'],
                    "place_id" => $_GET['place_id'],
                    "max_title" => $_POST['max_title'],
                    "min_title" => $_POST['min_title'],
                    "link" => $_POST['link'],
                    "mobile_link" => $_POST['mobile_link'],
                    "video_link"=>$_POST['video_link'],
                    "sequence" => $_POST['sequence'],
                    "id" => $_POST['id']
                );
                $res = $this->banner->update_banner($data);
                if ($res) {
                    admin_log(addslashes($data['img_url']), 'edit', 'banners');// 记录日志
                    sys_msg("操作成功", 0, $link);
                } else {
                    sys_msg("操作失败", 0, $link);
                }
            }
        }
    }

    public function edit()
    {

        $link = array(array('text' => "Banner列表", 'href' => 'route.php?con=banner'));
        if ("insert" == $_POST['act']) {
            $res = $this->banner->insert_place($_POST);
            if ($res) {
                admin_log(addslashes($_POST['place_name']), 'add', 'banner');// 记录日志
                sys_msg("操作成功", 0, $link);
            } else {
                sys_msg("操作失败", 0, $link);
            }
        } else {

            $res = $this->banner->update_place($_POST);
            if ($res) {
                admin_log(addslashes($_POST['place_name']), 'edit', 'banner');// 记录日志
                sys_msg("操作成功", 0, $link);
            } else {
                sys_msg("操作失败", 0, $link);
            }
        }
    }

    public function banner_remove()
    {
        if (empty($_GET['id'])) {
            $link = array(array('text' => "Banner列表", 'href' => 'route.php?con=banner'));
            sys_msg("操作失败", 0, $link);
        } else {
            //--hechengbin -- 删除banner图 --start
            $result = $this->banner->get_banner($_GET['id']);
            //--hechengbin -- end
            $res = $this->banner->banner_remove($_GET['id']);
            if ($res) {
                admin_log(addslashes($result['img_url']), 'remove', 'banners');// 记录日志
                $link = array(array('text' => "Banner列表", 'href' => 'route.php?con=banner'));
                sys_msg("操作成功", 0, $link);
            } else {
                $link = array(array('text' => "Banner列表", 'href' => 'route.php?con=banner'));
                sys_msg("操作失败", 0, $link);
            }
        }
    }

    public function place_remove()
    {
        if (empty($_GET['place_id'])) {
            $link = array(array('text' => "Banner列表", 'href' => 'route.php?con=banner'));
            sys_msg("操作失败", 0, $link);
        } else {
            //--hechengbin -- 删除banner图 --start
            $result = $this->banner->get_place($_GET['place_id']);
            //--hechengbin -- end
            $res = $this->banner->place_remove($_GET['place_id']);
            if ($res) {
                admin_log(addslashes($result['place_name']), 'remove', 'banner');// 记录日志
                $link = array(array('text' => "Banner列表", 'href' => 'route.php?con=banner'));
                sys_msg("操作成功", 0, $link);
            } else {
                $link = array(array('text' => "Banner列表", 'href' => 'route.php?con=banner'));
                sys_msg("操作失败", 0, $link);
            }
        }


    }

}
