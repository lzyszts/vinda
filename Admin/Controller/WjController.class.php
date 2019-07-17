<?php
//命名空间
namespace Admin\Controller;
use Tools\BackController;

class WjController extends BackController {
    /*
     * 问卷系统后台首页
     */
    public function index() {
        $this->display();
    }
}