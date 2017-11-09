<?php

/**
 * Created by PhpStorm.
 * User: stepreal
 * Date: 17/10/8
 * Time: 下午11:49
 */
__load("Controller", "controller");

class UserController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        __load("UserService");
        $this->user_service = new UserService();
    }

    /**
     * 批量删除
     */
    public function search_by_mobile_phone()
    {
        $mobile_phone = isset($_REQUEST['mobile_phone']) ? trim($_REQUEST['mobile_phone']) : '';
        $result = $this->user_service->search_by_mobile_phone($mobile_phone);
        $res = array(
            'code' => 200,
            'message' => '',
            'data' => $result,
        );
        echo json_encode($res);
        exit;
    }
}