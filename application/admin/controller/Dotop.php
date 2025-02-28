<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\db;
/**
* 上分审核管理
*
* @icon fa fa-circle-o
*/
class Dotop extends Backend
{

    /**
    * Dotop模型对象
    * @var \app\admin\model\Dotop
    */
    protected $model = null;

    public function _initialize() {
        parent::_initialize();
        $this->model = new \app\admin\model\Dotop;
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign("statusLists", $this->model->getStatusLists());
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
                ->where("type", 0)
                ->order($sort, $order)
                ->paginate($limit);
                
                } else {
                $list = $this->model
                ->where($where)
                ->where("type", 0)
                ->order($sort, $order)
                ->paginate($limit);
                }
            

            } else {

                $list = $this->model
                ->where($where)
                ->where("type", 0)
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
                'moneys' => $params['money'],
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
            $this->success("操作成功");
        } else {
            $this->error(__('操作失败'));
        }


    }
 public function generateSignature(array $params, string $privateKey): string
    {
        ksort($params);
        $str = [];
        foreach ($params as $k => $v) {
            if ($v === '') {
                continue;
            }
            $str[] = $k . '=' . $v;
        }
        $send = implode('&', $str) . '&';
        // 获取用户公钥，并格式化
        $privateKey = "-----BEGIN PRIVATE KEY-----\n"
        . wordwrap(trim($privateKey), 64, "\n", true)
        . "\n-----END PRIVATE KEY-----";
        $content = '';
        $privateKey = openssl_pkey_get_private($privateKey);
        foreach (str_split($send, 117) as $temp) {
            openssl_private_encrypt($temp, $encrypted, $privateKey);
            $content .= $encrypted;
        }
        return base64_encode($content);
    }


    public function adopt_tx() {
        $post = $this->request->request();
        if (!$post['id']) {
            $this->error('参数错误');
        }
        $cashrecord = db::name("dotop")->where("id", $post['id'])->find();
        $bank = db::name("bank")->where("user_id", $cashrecord['user_id'])->find();

       
        if ($cashrecord['status'] == 1) {
            $this->error("已上分");
        }
        if ($cashrecord['status'] == 2) {
            $this->error("已拒绝上分");
        }
        if ($cashrecord['status'] == 3) {
            $this->error("等待回调通知");
        }
        
        if (!$bank) {
            $this->error("用户未绑定账户信息");
        }
        


        $sxf=$cashrecord['sxf']*10000;
        
            // 请求参数
            $merchant_no = "M23100611581667"; // 商户号
            $description = "Payment Description"; // 订单描述
            // $pay_amount = $monss; // 支付金额，保留两位小数，最小50.00
            $notify_url = "https://wap.885856.com/api/index/pay_notify"; // 异步回调地址 (可选)
            
            $request_url = 'https://api.br.toppay.cloud/api/trade/payout';
            
            $request_data = array(
                "merchant_no" => $merchant_no,
                "name" => "name",
                "out_trade_no" => 'pay'.date("YmdHis").rand(100000, 999999),
                "description" => "description",
                "pay_amount" => "10.00",
                "pix_type" => $bank['pixtype'],
                "dict_key" => $bank['dict_key'],
                "notify_url" => "https://wap.885856.com/api/index/Bpay_Tnotify",
                
            ); 

            // 生成签名
            $privateKey = 'MIICeAIBADANBgkqhkiG9w0BAQEFAASCAmIwggJeAgEAAoGBAO4WpHa7lB7NgIKkUx3xSDBHqoleATYRsOqQu7piEAjgcMTFiwCjBX8W/JPI9UZ//qc5pyry2eV6OW5B7gqwn1I+1sywHjgtTOvwI/q2jGd/MM8g+N2t/hyRL+/PnpI6//ulqPKAC658wFn7ep2lJd5Ji/p472kEpC1JlQjj7DKjAgMBAAECgYEAzIy1dbDInAIwY40sP7BZNemMcYJLBhoC5jO8pW+0mPvCxrt1JfHpOfwUuh9P4ub6cM9OeM62N7pfdwO7sIF4DjfONUOrbyLMAj40XrzZSlwFdvN36Mr+Lo+r1awW/vTQOYLFk0zyIAbz3THYqxf5dMfZd8SoumxuOdfDe53JiSECQQD8fbh+HQIK5ZJ59xBJ2QcjkZVEPsAHgqw+tchL3LaLV7Er+mNN988tlaIzlvwwwFybMpPGT6JtSqc0G6mnFkIRAkEA8WWuksgpKWVUn3z7llftqYxOtqm4J9JAEa91ONo6Yo3gu8kTPyERHPHkm5LODMxZRNaSlgz1gMNyOV5Zdk81cwJAQNoFxharTKM0oTENNPqSc9dT0IiRiBxPI3hLbvjMxjOK5THVydPpmdrCI6AXPGpcHty5ygjwPkQbeC3WwHzeEQJBAKZMAkOkGJcn83AbzcX4tQVEX0V/DhqAwRpt4TdVPzt9ugAjuVqYEQ37ph12uPQgIq9Fkp5ENyyJeDsFQGQ8lPkCQQCvg4FYQ/mxcIO6gA8D1DaNCVW0nm6mt45naouv737jMcX+fHHPeKN1d5mx2Ytx8lsvWmBjQ8LNN8SdUqk+jdds
            '; // 商户私钥
            $sign = $this->generateSignature($request_data, $privateKey);

            // 添加签名到请求数据
            $request_data['sign'] = $sign;

         
            // 将请求数据转换为JSON格式
            $request_json = json_encode($request_data);
            
            // 创建 cURL 资源
            $ch = curl_init();
            // 设置 cURL 选项
            curl_setopt($ch, CURLOPT_URL, $request_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request_json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            
            // 发送请求并获取响应
            $response = curl_exec($ch);
            
            // dump($response);die;
            $response_data = json_decode($response, true);
           
        if ($response_data['code'] == 0) {
            $result = db::name("dotop")->where("id", $post['id'])->update(['status' => 3, 'orderNo' => $orderNo, 'info' => "pontuado com sucesso", "uptime" => time()]);
//   dump($recode);die;

            $this->success("代付已提交，等待回调通知");
        } else {
            $this->error($response_data['msg']);
        }



        $this->error("请选择提现操作");
    }

    // 生成指定长度的随机字符串
    function generateRandomString($length = 16) {
        $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
    public function adopt() {
        $post = $this->request->request();
        if (!$post['id']) {
            $this->error('参数错误');
        }
        $cashrecord = db::name("dotop")->where("id", $post['id'])->find();

        // dump($post);die;
        if ($cashrecord['status'] == 1) {
            $this->error("已上分");
        }
        if ($cashrecord['status'] == 2) {
            $this->error("已拒绝上分");
        }

        if ($cashrecord['type'] == 0) {


            if ($cashrecord['is_get'] == 1) {

                $istrue = db::name("dotop")->where("user_id", $cashrecord['user_id'])->where("type", 0)->where("status", 1)->find();
                if (!$istrue) {
                    $gift = db::name("rechlist")->where("true_money", $cashrecord['money'])->value("gift")??0;
                    $ordeinfo['money'] = $ordeinfo['money']+$gift;
                }
            }


            $random = $this->generateRandomString(); // 生成随机字符串
            $sn = "ioy"; // 商户前缀，请替换为实际的商户前缀
            $secretKey = "dC8385OErIC3917V8K79qX14Kz89F744"; // 请替换为实际的密钥
            $contentType = "application/json";
            $data = array(
                'orderId' => date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).rand(100000, 999999),
                "playerId" => "coke0".$cashrecord['user_id'],
                "currency" => "BRL",
                "type" => 1,
                "amount" => $cashrecord['money'],
            );
            $jsonData = json_encode($data);
            $sign = md5($random . $sn . $secretKey);
            $headers = array(
                "sign: $sign",
                "random: $random",
                "sn: $sn",
                "Content-Type: $contentType"
            );

            // 发起POST请求
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://sa.api-bet.net/api/server/walletTransfer"); // 替换为实际的接口URL
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            $response = curl_exec($ch);
            curl_close($ch);
            $res_code = json_decode($response, true);
            // dump($res_code);die;
            if ($res_code['code'] == 10000) {
                $result = db::name('convert')->insert([
                    'user_id' => $cashrecord['user_id'],
                    'balance' => $cashrecord['money'],
                    'orderId' => $data['orderId'],
                    'type' => 1,
                    'currency' => "BRL",
                    'addtime' => time(),
                ]);

            }


            //   \app\common\model\User::money($cashrecord['money'], $cashrecord['user_id'], "pontuado com sucesso".$cashrecord['money']);
        }

        $result = db::name("dotop")->
        where("id", $post['id'])->update(['status' => 1, 'info' => "pontuado com sucesso", "uptime" => time()]);

        db::name('mx')->insert([
            'uid' => $cashrecord['user_id'],
            'info' => "pontuado com sucesso",
            'type' => 1,
            'money' => $cashrecord['money'],
            'addtime' => time(),
        ]);



        if ($result) {
            $this->success("操作成功");
        } else {
            $this->error("操作失败");
        }
    }

    public function cancel() {
        $post = $this->request->request();
        if (!$post['uid']) {
            $this->error('参数错误');
        }
        $cashrecord = db::name("dotop")->where("id", $post['uid'])->find();

        Db::startTrans();
        try {
            $result = db::name("dotop")->where("id", $post['uid'])->update(['status' => 2, 'info' => $post['remark'], "uptime" => time()]);
            // 提交事务


            if ($cashrecord['type'] == 1) {

                \app\common\model\User::money($cashrecord['money'], $cashrecord['user_id'], $post['remark'].$cashrecord['money']);
            }


            db::name('mx')->insert([
                'uid' => $cashrecord['user_id'],
                'info' => $post['remark'],
                'type' => 0,
                'money' => $cashrecord['money'],
                'addtime' => time(),
            ]);
            Db::commit();
        } catch (\Exception $e) {
            // dump($e->getMessage());die;
            $this->error('数据错误' & $e->getMessage());
            // 回滚事务
            Db::rollback();
        }
        $this->success("拒绝成功");
    }
    /**
    * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
    * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
    * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
    */


}