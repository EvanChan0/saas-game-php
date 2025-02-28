<?php

namespace app\admin\controller;
use app\common\controller\Backend;
use think\db;
use fast\Random;
use app\common\model\User; // 根据你的命名空间和模型名来引入用户模型

/**
* 会员管理
*
* @icon fa fa-users
*/
class Users extends Backend
{

    /**
    * Users模型对象
    * @var \app\admin\model\Users
    */
    protected $model = null;

    public function _initialize() {
        parent::_initialize();
        $this->model = new \app\admin\model\Users;
        $this->view->assign("statusList", $this->model->getStatusList());
    }

/**
 * 批量操作方法
 */
public function multipop($ids = "")
{
    if ($this->request->isPost()) {
      
        $this->token();
        $params = $this->request->param();
        
        //   dump($params);die;
        $userIds = $params['ids'];
        $money = $params['row']['money'];
        $bz = $params['row']['bz'];
        // $money = str_replace(',', '', $money);
 
        $userIds = explode(',', $userIds);
        $nowTime = time();
        Db::startTrans();
        try {
            
            foreach ($userIds as $uId) {
                 db::table('fa_user')->where('id',$uId)->setinc("money",$money);
                   $result = db::name('mx')->insert([
                    'uid' => $uId,
                    'info' => $bz,//"批量上分成功",
                    'type' => 0,
                    'money' =>$money,
                    'addtime' => time(),
                ]);
            }
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        $this->success("批量上分成功");
    }
    return $this->view->fetch();
}

function getUpperThreeLevels($userId, $levels = 3) {
    $userModel = new User();

    $upperLevels = [];
    $currentUserId = $userId;

    for ($i = 0; $i < $levels; $i++) {
        $userInfo = $userModel->where('id', $currentUserId)->find();
        if ($userInfo) {
            $currentUserId = $userInfo->pid; // 假设有一个parent_id字段表示上级用户ID
            if ($currentUserId) {
                $upperLevels[] = $currentUserId; // 排除自己，只添加上级用户
            } else {
                break; // 如果没有上级用户，停止循环
            }
        } else {
            break; // 如果找不到上级用户，停止循环
        }
    }

    return $upperLevels;
}


    
    function getLowerThreeLevels($memberId, $level = 1)
    {
        $memberIds = [$memberId]; // 将当前会员ID添加到数组

        if ($level < 3) {
             $userModel = new User();
             $members = $userModel->where('pid', $memberId)->column('id'); // 获取当前会员的下级会员ID数组
            foreach ($members as $childMemberId) {
                $childMemberIds = $this->getLowerThreeLevels($childMemberId, $level + 1);
                $memberIds = array_merge($memberIds, $childMemberIds); // 合并ID数组
            }
        }

        return $memberIds;
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
            $uid = input('uid');
            
            //dump($_SESSION['think']['admin']);die;
            if ($_SESSION['think']['admin']['id'] == 1) {
    
            //   dump($uid);
            //   die;
                   
                // dump($_SESSION['think']['admin']['user_id']);die;
                if ($uid) {
                    // dump($uid);die;
                    $list = $this->model
                    ->where($where)
                    ->wherein("id", $uid)
                    ->order($sort, $order)
                    ->paginate($limit);

                } else {
                    $list = $this->model
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);

                }
            } else {

                if ($uid) {
                    $list = $this->model
                    ->where($where)
                    ->where("pid", $uid)
                    ->order($sort, $order)
                    ->paginate($limit);

                } else {
                    $list = $this->model
                    ->where($where)
                    ->where("pid", $_SESSION['think']['admin']['user_id'])

                    ->order($sort, $order)
                    ->paginate($limit);

                }


            }
             $dam= db::name("config")->where("name", "dam")->value("value")??0;
            
            foreach ($list as $row) {
                if($row->current_mosaic >0){
                    $row->dam= $row->current_mosaic*$dam;     
                }else{
                    $row->dam= "暂无打码任务";
                }
                $row->yong= db::name("user_money_log")->wherein("type",[2,3])->where(['user_id' => $row->id])->sum("money");
                 
                   
                $row->chong= db::name("dotop")->where("status",1)->where("type",0)->where(['user_id' => $row->id])->sum("money");
                $row->ti= db::name("dotop")->where("status",1)->where("type",1)->where(['user_id' => $row->id])->sum("money");
                
                $row->ying= db::name("game_record")->where("net_amount",'>',0)->where(['uid' => $row->id])->sum("valid_bet_amount");
                
                $row->shu= db::name("game_record")->where("net_amount",'<',0)->where(['uid' => $row->id])->sum("valid_bet_amount");
               
                $row->uidall=$this->getLowerThreeLevels($row->id)??"no";
                if(!$row->uidall){
                   $row->uidall="no"; 
                }
    
    // dump( $row->uidall);
                $row->fyl= $row->ying-$row->shu;
                //  $damok = db::name("game_record")->where("create_at",'>=',date("Y-m-d H:i:s",$row->current_times))->where(['uid' => $row->id])->sum("valid_bet_amount");
                
                // if($damok){
                //       $row->okdam= $damok;
                // }else{
                //       $row->okdam= 0;
                // }
               
            }
                $ying= db::name("game_record")->where("net_amount",'>',0)->sum("valid_bet_amount");
                
                $shu= db::name("game_record")->where("net_amount",'<',0)->sum("valid_bet_amount");
                
                
                $fyl= $ying-$shu;
            $result = array("total" => $list->total(), "yl" =>$ying, "ks" => $shu, "fyl" => $fyl, "rows" => $list->items(), "info" => $_SESSION['think']['admin']['id']);
            return json($result);
        }
        return $this->view->fetch();
    }


   //充值操作
    public function accesss() {
        $params = $this->request->post();

        $member = db::table('fa_user')->where('id', $params['uid'])->find();

            if ($params['range'] == 0) {
                $wheres = "setinc";
                $info = "pontuado com sucesso".$params['money'];
            } else {
                if ($member['money'] < $params['money']) {
                    $this->error('余额不足，不能扣取');
                }
                $info = "Sucesso".$params['money'];
                $wheres = "setdec";
            }
            $result = false;
            // dump($member['gec']);die;
            Db::startTrans();
            try {
                db::table('fa_user')->where('id', $params['uid'])->$wheres("money", $params['money']);
                //是否采用模型验证
                $result = db::name('mx')->insert([
                    'uid' => $params['uid'],
                    'info' => $info,
                    'type' => $params['range'],
                    'money' => $params['money'],
                    'addtime' => time(),
                ]);
                 if( db::name('dotop')->where("status",1)->where("user_id",$params['uid'])->find()){
                    $is_sc=0;
                }else{
                    $is_sc=1;
                }
                
                $results= db::name('dotop')->insert([
                    'user_id' => $params['uid'],
                    'info' => $params['coin'],
                    'username' => $member['username'],
                    'me_user' => $member['username'],
                    'dai_id' => $_SESSION['think']['admin']['user_id'] ,
                    'info' => $info,
                    'status' => 1,
                     'is_sc' => $is_sc,
                    'is_get' => $params['is_get'],
                    'type' => $params['range'],
                    'money' => $params['money'],
                    'addtime' => time(),
                ]);
               
                if($params['is_get']==1){
                     if($member['current_mosaic'] > 0){//存在打码未完成任务
                
                      db::name('user')->where('id', $params['uid'])->setinc("current_mosaic", $params['money']);
                    
                    }else{//不存在打码任务
                     db::name('user')->where('id', $params['uid'])->setinc("current_mosaic", $params['money']);
                     db::name('user')->where('id', $params['uid'])->update(["current_times"=>time()]);
                    }
                }
                Db::commit();
            } catch (ValidateException $e) {
                Db::rollback();
                $this->error($e->getMessage());
            } catch (PDOException $e) {
                Db::rollback();
                $this->error($e->getMessage());
            } catch (Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
            }
            if ($result !== false) {
                $this->success("操作成功");
            } else {
                $this->error(__('操作失败'));
            }

        
        
        



    }

     /**
    * 查看
    */
    public function detail($ids = null) {
       
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        
     
        
        $row->pushnum=db::name("user")->where("pid",$ids)->count();
        
        $row->cz= db::name("dotop")->whereIn("user_id", $ids)->where(['status' => 1, 'type' => 0])->sum("money");
        $row->tx=db::name("dotop")->whereIn("user_id", $ids)->where(['status' => 1, 'type' => 1])->sum("money");
        $row->xz=db::name("game_record")->whereIn("uid", $ids)->sum("bet_amount");
        
        
        
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds) && !in_array($row[$this->dataLimitField], $adminIds)) {
            $this->error(__('You have no permission'));
        }
        if (false === $this->request->isPost()) {
            $this->view->assign('row', $row);
            return $this->view->fetch();
        }
    }


    //充值操作
    public function access() {
        $params = $this->request->post();


             $member = db::table('fa_user')->where('id', $params['uid'])->find();
             $members = db::table('fa_user')->where('id',$_SESSION['think']['admin']['user_id'])->find();
// dump($member);die;
            if ($params['range'] == 0) {
                $wheres = "setinc";
                $info = "Enviar".$params['money'];
            } else {
                if ($member['money'] < $params['money']) {
                    $this->error('余额不足，不能扣取');
                }
                $info = "Sucesso".$params['money'];
                $wheres = "setdec";
            }
            $result = false;
            // dump($params);die;
            Db::startTrans();
            try {
                //是否采用模型验证
                $result = db::name('dotop')->insert([
                    'user_id' => $params['uid'],
                    'info' => $params['coin'],
                    'username' => $member['username'],
                    'me_user' => $members['username'],
                    'dai_id' => $_SESSION['think']['admin']['user_id'] ,
                    'info' => $info,
                    'status' => 0,
                    // 'is_get' => $params['is_get'],
                    'type' => $params['range'],
                    'money' => $params['money'],
                    'addtime' => time(),
                ]);
          
                
                
                
                
                
                Db::commit();
            } catch (ValidateException $e) {
                Db::rollback();
                $this->error($e->getMessage());
            } catch (PDOException $e) {
                Db::rollback();
                $this->error($e->getMessage());
            } catch (Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
            }
            if ($result !== false) {
                $this->success("上分申请提交成功");
            } else {
                $this->error(__('操作失败'));
            }

    }

  /**
     * 编辑
     *
     * @param $ids
     * @return string
     * @throws DbException
     * @throws \think\Exception
     */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds) && !in_array($row[$this->dataLimitField], $adminIds)) {
            $this->error(__('You have no permission'));
        }
        if (false === $this->request->isPost()) {
            $this->view->assign('row', $row);
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
          
                if(isset($params['password'])&& !empty($params['password'])){
                   $salt=Random::alnum();
                   $params['salt']=$salt;
                   $params['password']=md5(md5($params['password']) . $salt);  
                }else{
                    if(isset($params['password'])){
                      unset($params['password']);  
                    }
                    
                   
                }
        $params = $this->preExcludeFields($params);
        $result = false;
        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                $row->validateFailException()->validate($validate);
            }
            $result = $row->allowField(true)->save($params);
            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if (false === $result) {
            $this->error(__('No rows were updated'));
        }
        $this->success();
    } 



    /**
    * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
    * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
    * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
    */


}