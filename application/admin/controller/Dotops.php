<?php

namespace app\admin\controller;

use app\common\controller\Backend;

/**
 * 上分审核管理
 *
 * @icon fa fa-circle-o
 */
class Dotops extends Backend
{

    /**
     * Dotops模型对象
     * @var \app\admin\model\Dotops
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Dotops;
        $this->view->assign("statusList", $this->model->getStatusList());
    }



    /**
    * 查看
    */
    public function index() {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            //   dump($_SESSION['think']['admin']);die;
             $uid = input('uid/d');
            if ($_SESSION['think']['admin']['id'] == 1) {


             
                if ($uid) {
                 
                $list = $this->model
                ->where($where)
                ->where("user_id", $uid)
                 ->where("type", 1)
                ->order($sort, $order)
                ->paginate($limit);
                
                } else {
                $list = $this->model
                ->where($where)
                ->where("type", 1)
                ->order($sort, $order)
                ->paginate($limit);
                }
            




            } else {

                $list = $this->model
                ->where($where)
                ->where("type", 1)
                ->where("dai_id", $_SESSION['think']['admin']['user_id'])

                ->order($sort, $order)
                ->paginate($limit);



            }

            foreach ($list as $k => $row) {

                if ($row->type == 0) {
                    $row->moneys = ($row->money*5);
                } else {
                    $row->moneys = ($row->money/5);
                }

            }
            $result = array("total" => $list->total(), "rows" => $list->items(), "info" => $_SESSION['think']['admin']['id']);
            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


}
