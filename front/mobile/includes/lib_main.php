<?php
////手机版提示信息
//function mobile_show_message($content, $links = '', $hrefs = '', $type = 'info', $auto_redirect = true)
//{
////    assign_template();
//
//    $msg['content'] = $content;
//    if (is_array($links) && is_array($hrefs))
//    {
//        if (!empty($links) && count($links) == count($hrefs))
//        {
//            foreach($links as $key =>$val)
//            {
//                $msg['url_info'][$val] = $hrefs[$key];
//            }
//            $msg['back_url'] = $hrefs['0'];
//        }
//    }
//    else
//    {
////        $link   = empty($links) ? $GLOBALS['_LANG']['back_up_page'] : $links;
//        $href    = empty($hrefs) ? 'javascript:history.back()'       : $hrefs;
////        $msg['url_info'][$link] = $href;
//        $msg['back_url'] = $href;
//    }
//
//    $msg['type']    = $type;
////    $position = assign_ur_here(0, $GLOBALS['_LANG']['sys_msg']);
////    $GLOBALS['smarty']->assign('page_title', $position['title']);   // 页面标题
////    $GLOBALS['smarty']->assign('ur_here',    $position['ur_here']); // 当前位置
//
////    if (is_null($GLOBALS['smarty']->get_template_vars('helps')))
////    {
////        $GLOBALS['smarty']->assign('helps', get_shop_help()); // 网店帮助
////    }
//
//    $GLOBALS['smarty']->assign('auto_redirect', $auto_redirect);
//    $GLOBALS['smarty']->assign('message', $msg);
//    $GLOBALS['smarty']->display('message_cat.html');
//
//    exit;
//}
////手机版提示信息
//function mobile_show_null_cart($content, $links = '', $hrefs = '', $type = 'info', $auto_redirect = true)
//{
////    assign_template();
//    $msg['content'] = $content;
//    if (is_array($links) && is_array($hrefs))
//    {
//        if (!empty($links) && count($links) == count($hrefs))
//        {
//            foreach($links as $key =>$val)
//            {
//                $msg['url_info'][$val] = $hrefs[$key];
//            }
//            $msg['back_url'] = $hrefs['0'];
//        }
//    }
//    else
//    {
////        $link   = empty($links) ? $GLOBALS['_LANG']['back_up_page'] : $links;
//        $href    = empty($hrefs) ? 'javascript:history.back()'       : $hrefs;
////        $msg['url_info'][$link] = $href;
//        $msg['back_url'] = $href;
//    }
//
//    $msg['type']    = $type;
////    $position = assign_ur_here(0, $GLOBALS['_LANG']['sys_msg']);
////    $GLOBALS['smarty']->assign('page_title', $position['title']);   // 页面标题
////    $GLOBALS['smarty']->assign('ur_here',    $position['ur_here']); // 当前位置
//
////    if (is_null($GLOBALS['smarty']->get_template_vars('helps')))
////    {
////        $GLOBALS['smarty']->assign('helps', get_shop_help()); // 网店帮助
////    }
//
//    $GLOBALS['smarty']->assign('auto_redirect', $auto_redirect);
//    $GLOBALS['smarty']->assign('message', $msg);
//    $GLOBALS['smarty']->display('message_cat.html');
//
//    exit;
//}
//?>