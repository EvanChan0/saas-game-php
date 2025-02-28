<?php

namespace app\api\controller;
use think\Db;
use app\common\controller\Api;
use app\common\library\Ems;
use app\common\library\Sms;
use app\common\model\User;
use think\Hook;
use app\common\library\Sms as Smslib;
use think\Validate;
use fast\Random;


//         / \__
//           (    @\___
//           /         O
//          /   (_____/
//       /_____/   U

//   _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
//     \                                                  /
//      \       所有程序为零点开发 TG:Lingdian666        /
//       \          尊重作者 尊重版权 谢谢              /
//        \                                            /
//       - _ - _ - _ - _ - _ - _ - _ - _ - _ - _ - -


class Index extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];



    /**
    * 首页
    *
    */
    public function index() {


        $currentValue1 = db::name("config")->where("name", "upmoeny")->value("value")??0;

        $currentValue2 = db::name("config")->where("name", "gamenum")->value("value")??0;

        $targetValue1 = $currentValue1+100; // 第一个字段的目标值
        $targetValue2 = $currentValue2+100; // 第二个字段的目标值

        // 生成随机增加值（可以根据需要调整范围）
        $randomIncrease1 = rand(1, 100); // 生成 1 到 10 之间的随机数
        $randomIncrease2 = rand(1, 100); // 生成 1 到 10 之间的随机数

        // 计算新的字段值
        $newValue1 = $currentValue1 + $randomIncrease1;
        $newValue2 = $currentValue2 + $randomIncrease2;

        // 限制新值不超过目标值
        if ($newValue1 > $targetValue1) {
            $newValue1 = $targetValue1;
        }
        if ($newValue2 > $targetValue2) {
            $newValue2 = $targetValue2;
        }
        $currentValue1 = $newValue1;
        $currentValue2 = $newValue2;
        // dump($currentValue1);
        // dump($currentValue2);
        // die;
        db::name("config")->where("name", "upmoeny")->update(['value' => $currentValue1]);
        db::name("config")->where("name", "gamenum")->update(['value' => $currentValue2]);
        // 输出新的字段值（你可以替换成更新数据库字段等操作）
        echo "字段1当前值: $currentValue1, 字段2当前值: $currentValue2";
        $this->success('请求成22功');
    }
    /**
    * 获取尾部富文本列表
    * @ApiMethod (POST)
    */
    public function get_fool() {
        $data = db::name("fool")->where("is_faq",0)->select();

        if ($data) {
            $this->success("list", $data, 200);
        } else {
            $this->error("not data");
        }
    }


    /**
    * 获取尾部富文本详情
    *@param string $id
    * @ApiMethod (POST)
    */
    public function fool_dtails() {
        $id = $this->request->post('id');
        $data = db::name("fool")->where("id", $id)->find();

        if ($data) {
            $this->success("list", $data, 200);
        } else {
            $this->error("not data");
        }
    }


    /**
    * 获取左侧分类信息
    * @ApiHeaders (name=token, type=string, required=true, description="请求的Token")
    * @ApiMethod (POST)
    */
    public function get_left() {

        $gamelist = db::name("gamelist")->where("is_left", 1)->select();

        if ($gamelist) {
            $this->success("ok", $gamelist, 200);
        } else {
            $this->success("ok", [], 200);
        }
    }

    /**
    * 收藏游戏
    * @param string $game_id 游戏id
    * @ApiHeaders (name=token, type=string, required=true, description="请求的Token")
    * @ApiMethod (POST)
    */
    public function like_game() {
        $user = $this->auth->getUser();
        if (! $user) {
            $this->error('token 失效', '', 422);
        }
        $game_id = $this->request->post('game_id');

        $result = db::name("gamelike")->where("user_id", $user->id)->where("game_id", $game_id)->find();
        if ($result) {
            $this->error("请勿重复收藏！");
        }
        $resulst = db::name('gamelike')->insert([
            'user_id' => $user->id,
            'game_id' => $game_id,
            'addtime' => time(),
        ]);
        if ($resulst) {
            $this->success("收藏成功", $resulst, 200);
        } else {
            $this->error("收藏错误");
        }
    }

    /**
    * 取消收藏游戏
    * @param string $game_id 游戏id
    * @ApiHeaders (name=token, type=string, required=true, description="请求的Token")
    * @ApiMethod (POST)
    */
    public function c_like_game() {
        $user = $this->auth->getUser();
        if (! $user) {
            $this->error('token 失效', '', 422);
        }
        $game_id = $this->request->post('game_id');

        $result = db::name("gamelike")->where("user_id", $user->id)->where("game_id", $game_id)->delete();
        if ($result) {
            $this->success("取消成功", $result, 200);
        } else {
            $this->error("已取消");
        }
    }


    /**
    * 获取收藏游戏
    * @param string $game_id 游戏id
    * @ApiHeaders (name=token, type=string, required=true, description="请求的Token")
    * @ApiMethod (POST)
    */
    public function get_game() {
        $user = $this->auth->getUser();
        if (! $user) {
            $this->error('token 失效', '', 422);
        }
        $game_id = db::name("gamelike")->where("user_id", $user->id)->column("game_id");
        $pg_slots = db::name("gamelist")->whereIn("id", $game_id)->order("des desc")->select();
        //  dump($pg_slots);die;
        if ($pg_slots) {
            $this->success("ok", $pg_slots, 200);
        } else {
            $this->success("暂无数据", [], 200);
        }
    }


    /**
    * 使用奖金券
    * @param string $code 奖金券
    * @ApiHeaders (name=token, type=string, required=true, description="请求的Token")
    * @ApiMethod (POST)
    */
    public function set_code() {
        $user = $this->auth->getUser();
        if (! $user) {
            $this->error('token invalid', '', 422);
        }
        $code = $this->request->post('Code');

        // dump($code);die;

        $result = db::name("codeinfo")->where("code", $code)->find();
        if (!$result) {
            $this->error("Does not exist or the bonus coupon is wrong!");
        }

        if ($result['status'] == 1) {
            $this->error("This bonus coupon has been used!");
        }

        \app\common\model\User::money($result['money'], $user->id, "Bonus Voucher".$result['money'], 10);

        $res = db::name("codeinfo")->where("code", $code)->update(['uptime' => time(), 'status' => 1, 'user_id' => $user->id]);


        if ($res) {
            $this->success("Use successfully", $res, 200);
        } else {
            $this->error("Use error");
        }
    }


    function generateRandomPhoneNumber() {
        $prefix = '77****00'; // 固定前缀
        $suffix = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT); // 随机生成后四位数字
        return $prefix . $suffix;
    }

    function generateRandomAmount() {
        return 'ganhar' . mt_rand(1, 100) . ' Quantia';
    }

    /**
    * 获取通知
    *
    * @ApiMethod (POST)

    */
    public function getchat() {

        $numRecords = 5; // 想生成的记录数量

        $dataArray = [];

        for ($i = 0; $i < $numRecords; $i++) {
            $phoneNumber = $this->generateRandomPhoneNumber();
            $amount = $this->generateRandomAmount();
            $dataArray[] = "do utilizador" . $phoneNumber . "pegar " . $amount;
        }
        if ($dataArray) {
            $this->success("ok", $dataArray, 200);
        } else {
            $this->error("not data");
        }
    }

    /**
    * 是否绑定 pix
    *
    * @ApiHeaders (name=token, type=string, required=true, description="请求的Token")
    * @ApiMethod (POST)
    */
    public function is_bank() {
        $user = $this->auth->getUser();
        if (! $user) {
            $this->error('token 失效', '', 422);
        }

        $ret = db::name("bank")->where("user_id", $user->id)->find();

        if ($ret) {
            $data['data'] = $ret;
            $data['code'] = 200;
            $this->success('vinculado', $data, 200);
        } else {
            $data['data'] = [];
            $data['code'] = 101;
            $this->error('não vinculado', $data, 200);
        }
    }

    /**
    * 绑定pix
    *
    * @ApiHeaders (name=token, type=string, required=true, description="请求的Token")
    * @ApiMethod (POST)
    * @param string firstname 姓
    * @param string lastname 名
    * @param string email 邮箱
    * @param string phone 手机号
    * @param string cpf 税号
    * @param string dict_key  输入账号
    * @param string pixtype pix类型
    */
    public function add_bank() {
        $user = $this->auth->getUser();
        if (! $user) {
            $this->error('token 失效', '', 422);
        }

        $payerFirstName = $this->request->post('firstname');
        $payerLastName = $this->request->post('lastname');
        $payerEmail = $this->request->post('email');
        $payerPhone = $this->request->post('phone');
        $payerCPF = $this->request->post('cpf');
        $PIX = $this->request->post('dict_key');
        $pixType = $this->request->post('pixtype');

        if ($payerFirstName == '' || $payerLastName == '' || $payerEmail == '' || $payerPhone == '' || $payerCPF == '') {
            $this->error("O parâmetro obrigatório não está vazio");
        }

        if (db::name('bank')->where("pix", $PIX)->find()) {
            $this->error("conta pix já existe");
        }

        $ret = db::name('bank')->insert([
            'firstname' => $payerFirstName,
            'lastname' => $payerLastName,
            'email' => $payerEmail,
            'phone' => $payerPhone,
            'cpf' => $payerCPF,
            'user_id' => $user->id,
            'pix' => $PIX,
            'pixtype' => $pixType,
            'updatetime' => time(),
        ]);

        if ($ret) {
            $this->success('ligar com sucesso', [], 200);
        } else {
            $this->error('falha na ligação');
        }
    }


    /**
    * 获取轮播
    *
    * @ApiMethod (POST)

    */
    public function getban() {


        $result = db::name("lb")->select();


        if ($result) {
            $this->success("ok", $result, 200);
        } else {
            $this->error("not data");
        }
    }
    /**
    * 获取配置参数
    *
    * @ApiMethod (POST)

    */
    public function get_c() {

        $result['chat'] = db::name("config")->where("name", "chat")->value("value");
        $result['chats'] = db::name("config")->where("name", "chats")->value("value");
        $result['appd'] = db::name("config")->where("name", "appd")->value("value");
        if ($result) {
            $this->success("ok", $result, 200);
        } else {
            $this->error("not data");
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

    /**
    * 验签
    */
    public function verifySign(array $data, string $publicKey): bool
    {
        if (isset($data['sign'])) {
            $sign = base64_decode($data['sign']);
            unset($data['sign']);
        } else {
            return false;
        }
        ksort($data);
        $str = [];
        foreach ($data as $k => $v) {
            if ($v === '') {
                continue;
            }
            $str[] = $k . '=' . $v;
        }
        $send = implode('&', $str) . '&';
        // 获取用户公钥，并格式化
        $publicKey = "-----BEGIN PUBLIC KEY-----\n"
        . wordwrap(trim($publicKey), 64, "\n", true)
        . "\n-----END PUBLIC KEY-----";
        $publicKey = openssl_pkey_get_public($publicKey);
        $result = '';
        foreach (str_split($sign, 128) as $value) {
            openssl_public_decrypt($value, $decrypted, $publicKey);
            $result .= $decrypted;
        }
        return $result === $send;
    }




    // 发送HTTP POST请求
    public function sendHttpPostRequest($url, $data) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }



    /**
    * 充值&提现
    *
    * @ApiHeaders (name=token, type=string, required=true, description="请求的Token")
    * @ApiMethod (POST)
    * @param string $status 0充值1提现
    * @param string $is_get 是否充值奖励
    * @param string $money 金额
    */

    public function Withdraw_recharge() {


        $user = $this->auth->getUser();

        if (! $user) {
            $this->error('token 失效', '', 422);
        }
        $monss = $this->request->post('money');
        $is_get = $this->request->post('is_get')??0;
        $type = $this->request->post('status');

        if ($monss <= 0) {
            $this->success("O valor não está vazio");
        }


        // $dp = db::name("dotop")->where("user_id", $user->id)->where("type", $type)->where("status", 0)->find();
        // if ($dp) {
        //     $this->error("Por favor, aguarde a revisão");
        // }
        $member = db::table('fa_user')->where('id', $user->id)->find();
        if ($type == 1) {



            // current_mosaic

            $mobile = $this->request->post('mobile');
            $bankCard = $this->request->post('bankCard');
            // $bankName = $this->request->post('bankName');
            $With_count = db::name("dotop")->where("user_id", $user->id)->where("type", 1)->whereTime("addtime", "today")->count();
            $With_limt = db::name("dotop")->where("user_id", $user->id)->where("type", 1)->whereTime("addtime", "today")->sum("money");

            if ($mobile == '' || $bankCard == '') {
                $this->error("O parâmetro obrigatório não está vazio");
            }
            if ($member['current_mosaic'] > 0) {
                //存在打码未完成任务

                $this->error("Existem tarefas de codificação inacabadas");
                // db::name('user')->where('id', $params['uid'])->setinc("current_mosaic", $params['money']);

            }



            if ($member['is_tx'] == 1) {
                $this->error("As contas de teste não podem ser retiradas");
            }

            $vips = db::name("vips")->where("level", $this->auth->level)->find();

            if ($With_count >= $vips['day_withdraw']) {

                $this->error("O limite de retirada de hoje foi atingido");

            }

            if ($With_limt >= $vips['withdraw_limt']) {

                $this->error("O valor da retirada de hoje atingiu o limite superior");

            }
            //   dump($vips['single_withdraw']);die;

            if ($monss < $vips['single_withdraw']) {

                $this->error("A retirada excede o limite único:".$vips['single_withdraw']);

            }

            $info = "Os clientes solicitam subpontos";
            if ($user->money < $monss) {
                $this->success("Valor insuficiente disponível para retirada");
            }

            $withdrawa = $vips['withdraw']/100;
            $sxf = ($monss*$withdrawa);

            $news = $monss-$sxf;
            $res = db::table('fa_user')->where('id', $user->id)->setDec("money", $monss);
            //  \app\common\model\User::money(-$money, $this->auth->id, "提现扣除".$money, 7);


            if ($res) {

                $info = "Clientes solicitam pontos";

                $result = db::name('dotop')->insert([
                    'user_id' => $user->id,
                    'username' => $member['username'],
                    'me_user' => $member['username'],
                    'dai_id' => 0,
                    'mobile' => $mobile??"",
                    'bankCard' => $bankCard??"",
                    // 'bankName' => $bankName??"",
                    'info' => $info,
                    'status' => 0,
                    'sxf' => $sxf??0,
                    'is_get' => $is_get,
                    'type' => $type,
                    'money' => $monss,
                    'addtime' => time(),
                    'uptime' => time(),
                ]);

                $this->success("Retirada bem-sucedida", [], 200);
            } else {
                $this->error("Retirada falhou");
            }


        } else {



            // 请求参数
            $merchant_no = "M23100611581667"; // 商户号
            $description = "Payment Description"; // 订单描述
            $pay_amount = $monss; // 支付金额，保留两位小数，最小50.00
            $notify_url = "https://wap.885856.com/api/index/pay_notify"; // 异步回调地址 (可选)
            // 请求URL
            $request_url = 'https://api.br.toppay.cloud/api/trade/payin';

            // 请求数据
            $request_data = array(
                "title" => "title",
                "merchant_no" => $merchant_no,
                "out_trade_no" => 'pay'.date("YmdHis").rand(100000, 999999),
                "description" => $description,
                "pay_amount" => $pay_amount,
                "notify_url" => $notify_url
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
            $response_data = json_decode($response, true);
            if ($response_data['code'] === 0) {

                $info = "Clientes solicitam pontos";

                $result = db::name('dotop')->insert([
                    'user_id' => $user->id,
                    'username' => $member['username'],
                    'me_user' => $member['username'],
                    'dai_id' => 0,
                    'mobile' => $mobile??"",
                    // 'bankCard' => $bankCard??"",
                    // 'bankName' => $bankName??"",
                    'info' => $info,
                    'status' => 0,
                    'orderNo' => $request_data['out_trade_no'],
                    'sxf' => $sxf??0,
                    'is_get' => $is_get,
                    'type' => $type,
                    'money' => $monss,
                    'addtime' => time(),
                    'uptime' => time(),
                ]);
                $datas['paymentUrl'] = $response_data['data']['payment_link'];

                $this->success("ok", $datas, 200);
            } else {
                $this->error($response_data['msg']);
            }


        }


    }


    /**
    * Bpay提现回调
    *
    * @ApiHeaders (name=token, type=string, required=true, description="请求的Token")
    * @ApiMethod (POST)
    */
    public function Bpay_Tnotify() {


        //获取回调
        $json_raw = file_get_contents("php://input");

        if (!$json_raw) {
            echo"未获取回调参数"; die;
        }
        $json_data = (array)json_decode($json_raw);


        //打印日志
        $file = "notic_" . date("Ymd") . ".log";
        $ct = date("Y-m-d H:i:s", time());

        error_log("收到代付回调数据" . var_export($json_data, true) . " \r\n", 3, $file);
        $status = $json_data['status'];
        $mch_order_no = $json_data['order_no'];

        if ($status == 2) {
            $ordeinfo = db::name("dotop")->where("orderNo", $mch_order_no)->find();
            if (!empty($ordeinfo)) {
                // 数据库查询有结果
                if ($ordeinfo['status'] == 0 || $ordeinfo['status'] == 3) {
                    db::name("dotop")->where("id", $ordeinfo['id'])->update(['status' => 1, 'info' => "Recarga com sucesso", "uptime" => time()]);
                    db::name('mx')->insert([
                        'uid' => $ordeinfo['user_id'],
                        'info' => "Recarga com sucesso",
                        'type' => 1,
                        'money' => $ordeinfo['money'],
                        'addtime' => time(),
                    ]);

                }
            }
            echo "success";
            error_log("代付成功 " . " \r\n", 3, $file);

        } elseif ($status == 3 || $status == 4) {

            $ordeinfo = db::name("dotop")->where("orderNo", $mch_order_no)->find();
            if (!empty($ordeinfo)) {
                // 数据库查询有结果
                \app\common\model\User::money($ordeinfo['money'], $ordeinfo['user_id'], "Recarga com sucesso".$ordeinfo['money']);
                db::name("dotop")->where("id", $ordeinfo['id'])->update(['status' => 2, 'info' => "Falha no retorno de chamada", "uptime" => time()]);
                db::name('mx')->insert([
                    'uid' => $ordeinfo['user_id'],
                    'info' => "Falha no retorno de chamada",
                    'type' => 0,
                    'orders' => $mch_order_no,
                    'money' => $ordeinfo['money'],
                    'addtime' => time(),
                ]);

            }
            echo "success";
            error_log("代付失败 " . " \r\n", 3, $file);
        } else {
            db::name("dotop")->where("id", $ordeinfo['id'])->update(['status' => 4, 'info' => "Em processamento", "uptime" => time()]);
            error_log("代付中" . " \r\n", 3, $file);
            echo "success";
        }


    }

    /**
    * Bpay回调
    *
    * @ApiHeaders (name=token, type=string, required=true, description="请求的Token")
    * @ApiMethod (POST)
    */
    public function pay_notify() {
        // 开始数据库事务
        db::startTrans();

        // 获取回调
        $json_raw = file_get_contents("php://input");

        if (!$json_raw) {
            echo "未获取回调参数";
            // 回滚事务
            db::rollback();
            die;
        }

        $json_data = (array)json_decode($json_raw, true, 512, JSON_BIGINT_AS_STRING);

        // 打印日志
        $file = "notic_" . date("Ymd") . ".log";
        $ct = date("Y-m-d H:i:s", time());

        error_log("收到的回调数据" . var_export($json_data, true) . " \r\n", 3, $file);
        $merchantOrderNo = $json_data['out_trade_no'];

        if ($json_data['status'] == 1) {
            $ordeinfo = db::name("dotop")->where("orderNo", $merchantOrderNo)->find();

            if (!empty($ordeinfo)) {
                // 数据库查询有结果
                if ($ordeinfo['status'] == 0) {

                    if ($ordeinfo['is_get'] == 1) {
                        $istrue = db::name("dotop")->where("user_id", $ordeinfo['user_id'])->where("type", 0)->where("status", 1)->find();

                        if (!$istrue) {
                            $gift = db::name("rechlist")->where("true_money", $ordeinfo['money'])->value("gift") ?? 0;
                            $ordeinfo['money'] = $ordeinfo['money'] + $gift;
                        }
                    }

                    $random = $this->generateRandomString(); // 生成随机字符串
                    $sn = "o62"; // 商户前缀，请替换为实际的商户前缀
                    $secretKey = "7KmoPK477z4pUXa0ioBnD2436C0J92t4"; // 请替换为实际的密钥
                    $contentType = "application/json";
                    $data = array(
                        'orderId' => date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8) . rand(100000, 999999),
                        "playerId" => "coke0" . $ordeinfo['user_id'],
                        "currency" => "BRL",
                        "type" => 1,
                        "amount" => $ordeinfo['money'],
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

                    if ($res_code['code'] == 10000) {
                        $result = db::name('convert')->insert([
                            'user_id' => $ordeinfo['user_id'],
                            'balance' => $ordeinfo['money'],
                            'orderId' => $data['orderId'],
                            'type' => 1,
                            'currency' => "BRL",
                            'addtime' => time(),
                        ]);
                    }

                    db::name("dotop")->where("id", $ordeinfo['id'])->update(['status' => 1, 'info' => "Recarga bem sucedida", "uptime" => time()]);
                    db::name('mx')->insert([
                        'uid' => $ordeinfo['user_id'],
                        'info' => "Recarga com sucesso back",
                        'type' => 1,
                        'orders' => $merchantOrderNo,
                        'money' => $ordeinfo['money'],
                        'addtime' => time(),
                    ]);

                    // 提交事务
                    db::commit();
                }
                echo "success"; die;
                error_log("验签成功" . " \r\n", 3, $file);
            }
        } elseif ($json_data['status'] == "fail") {
            if ($ordeinfo['status'] == 2) {
                db::name("dotop")->where("id", $ordeinfo['id'])->update(['status' => 3, 'info' => "Falha no retorno de chamada de recarga", "uptime" => time()]);
            }
            error_log("失败" . openssl_error_string() . " \r\n", 3, $file);
            db::commit();
            echo "success"; die;
        } else {

            echo "success"; die;
            error_log("其他错误" . openssl_error_string() . " \r\n", 3, $file);
            // 回滚事务
            db::rollback();
        }
    }



    /**
    * 发送邮箱验证码或短信验证码
    * @param string  $username   邮箱&手机号
    * @param string $event 事件名称register或者login retrieve找回密码
    * @return boolean
    */
    public function send_email_mobile($code = null) {
        $username = $this->request->post('username');
        $event = $this->request->post('event');

        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            if ($username && !Validate::is($username, "email")) {
                $this->error(__('Email is incorrect'));
            }
            if ($event == "retrieve") {
                $em = db::name("user")->where("username", $username)->find();
                if (!$em) {
                    $this->error(__("用户邮箱不存在"));
                }
            }
            // $event = "reg";
            $code = is_null($code) ? mt_rand(100000, 999999) : $code;
            $tres = mailTo($username, "cokejogo", "convida você a se registrar. Seu código de verificação é：".$code);
            db::name("ems")->insert(['email' => $username, 'event' => $event, 'code' => $code, 'times' => 0, 'createtime' => time(),]);
            if ($tres == 1) {
                $this->success(__('success'), [], 200);
            } else {
                $this->error(__($tres));
            }
        } else {

            $mobile = $username;
            $event = $this->request->request("event");
            $event = $event ? $event : 'register';


            if ($event == "retrieve") {
                $phoone = db::name("user")->where("mobile", $username)->find();
                if (!$phoone) {
                    $this->error(__("用户手机号不存在"));
                }
            }


            if (!$mobile || !\think\Validate::regex($mobile, "^1\d{10}$")) {
                $this->error(__('手机号不正确'));
            }
            $last = Smslib::get($mobile, $event);
            if ($last && time() - $last['createtime'] < 60) {
                $this->error(__('发送频繁'));
            }
            $ipSendTotal = \app\common\model\Sms::where(['ip' => $this->request->ip()])->whereTime('createtime', '-1 hours')->count();
            if ($ipSendTotal >= 5) {
                $this->error(__('发送频繁'));
            }
            if ($event) {
                $userinfo = \app\common\model\User::getByMobile($mobile);
                if ($event == 'register' && $userinfo) {
                    //已被注册
                    $this->error(__('已被注册'));
                } elseif (in_array($event, ['changemobile']) && $userinfo) {
                    //被占用
                    $this->error(__('已被占用'));
                } elseif (in_array($event, ['changepwd', 'resetpwd']) && !$userinfo) {
                    //未注册
                    $this->error(__('未注册'));
                }
            }

            $ret = Smslib::send($mobile, mt_rand(100000, 999999), $event, 86);
            if ($ret) {
                $this->success(__('短信发送成功'), [], 200);
            } else {
                $this->error(__('发送失败，请检查短信配置是否正确'));
            }
        }
        $this->error(__('非法请求'));

    }





    /**
    * 发送邮箱验证码或短信验证码
    * @param string  $username   邮箱&手机号
    * @param string $event 事件名称register或者login
    * @return boolean
    */
    public function send_email_mobiles($code = null) {
        $username = $this->request->post('username');


        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            if ($username && !Validate::is($username, "email")) {
                $this->error(__('Email is incorrect'));
            }

            $event = "reg";
            $code = is_null($code) ? mt_rand(100000, 999999) : $code;
            $tres = mailTo($username, "凭证", "您的验证码是".$code);
            db::name("ems")->insert(['email' => $username, 'event' => $event, 'code' => $code, 'times' => 0, 'createtime' => time(),]);
            if ($tres == 1) {
                $this->success(__('邮箱发送成功'));
            } else {
                $this->error(__($tres));
            }
        } else {

            $mobile = $username;
            $event = $this->request->request("event");
            $event = $event ? $event : 'register';

            if (!$mobile || !\think\Validate::regex($mobile, "^1\d{10}$")) {
                $this->error(__('手机号不正确'));
            }
            $last = Smslib::get($mobile, $event);
            if ($last && time() - $last['createtime'] < 60) {
                $this->error(__('发送频繁'));
            }
            $ipSendTotal = \app\common\model\Sms::where(['ip' => $this->request->ip()])->whereTime('createtime', '-1 hours')->count();
            if ($ipSendTotal >= 5) {
                $this->error(__('发送频繁'));
            }
            if ($event) {
                $userinfo = \app\common\model\User::getByMobile($mobile);
                if ($event == 'register' && $userinfo) {
                    //已被注册
                    $this->error(__('已被注册'));
                } elseif (in_array($event, ['changemobile']) && $userinfo) {
                    //被占用
                    $this->error(__('已被占用'));
                } elseif (in_array($event, ['changepwd', 'resetpwd']) && !$userinfo) {
                    //未注册
                    $this->error(__('未注册'));
                }
            }

            $ret = Smslib::send($mobile, mt_rand(100000, 999999), $event, 86);
            if ($ret) {
                $this->success(__('短信发送成功'));
            } else {
                $this->error(__('发送失败，请检查短信配置是否正确'));
            }
        }
        $this->error(__('非法请求'));

    }





    /**
    * 获取内容详情
    * @param string $type
    * 8 关于我们
    * 7	首次存款金
    * 6	成为合作伙伴
    * 5	首页详情信息
    * 4	邀请文本设定
    * 3	充值说明
    * 2	首充内容
    * 1	飞机内容
    *
    * @ApiMethod (POST)
    */
    public function get_contents() {
        $type = $this->request->post('type');
        $data = db::name("contents")->where("type", $type)->find();

        if ($data) {
            $this->success("list", $data, 200);
        } else {
            $this->error("not data");
        }
    }



    /**
    * 流水记录
    *
    * @ApiHeaders (name=token, type=string, required=true, description="请求的Token")
    * @ApiMethod (POST)
    * @param string $startDateTime 开始时间
    * @param string $endDateTime 结束时间
    * @param string $page 从1开始
    * @param string $list 分页长度,默认10
    */
    public function find_bets() {
        $user = $this->auth->getUser();
        if (! $user) {
            $this->error('token 失效', '', 422);
        }
        $startDateTime = $this->request->post('startDateTime');
        $endDateTime = $this->request->post('endDateTime');
        $page = $this->request->post('page') ?? 1;
        $list = $this->request->post('list') ?? 10;
        $where['uid'] = $user->id;


        if ($startDateTime) {
            $where['create_at'] = ['>=',
                $startDateTime];
            $where['create_at'] = ['<=',
                $endDateTime];
        }
        //  dump($where);die;
        $ret = db::name("game_record")->where($where)->order('id desc')->page($page, $list)->select();
        $retcount = db::name("game_record")->count();
        $data['data'] = $ret;
        $data['tal'] = $retcount;
        if ($ret) {
            $this->success('投注记录', $data, 200);
        } else {
            $this->error('no data');
        }
    }


    /**
    * 返利记录
    *
    * @ApiHeaders (name=token, type=string, required=true, description="请求的Token")
    * @ApiMethod (POST)
    * @param string $startDateTime 开始时间
    * @param string $endDateTime 结束时间
    * @param string $page 从1开始
    * @param string $list 分页长度,默认10
    */
    public function rebate_list() {
        $user = $this->auth->getUser();
        if (! $user) {
            $this->error('token 失效', '', 422);
        }
        $startDateTime = $this->request->post('startDateTime');
        $endDateTime = $this->request->post('endDateTime');
        $page = $this->request->post('page') ?? 1;
        $list = $this->request->post('list') ?? 10;
        $where['user_id'] = $user->id;
        // $where['type'] = 0;
        // if ($startDateTime) {
        //     $where['create_at'] = ['>=',
        //         $startDateTime];
        //     $where['create_at'] = ['<=',
        //         $endDateTime];
        // }

        $ret = db::name("user_money_log")->where($where)->order('id desc')->page($page, $list)->select();

        foreach ($ret as &$v) {
            $v['createtime'] = date("Y-m-d H:i:s", $v['createtime']);
        }
        $retcount = db::name("user_money_log")->count();
        $data['data'] = $ret;
        $data['tal'] = $retcount;
        if ($ret) {
            $this->success('返利记录', $data, 200);
        } else {
            $this->error('no data');
        }
    }




    /**
    * 获取三级客户信息
    * @ApiMethod (POST)
    * type 0充值1提现
    */
    public function allThirdLevelUsers() {
        $user = $this->auth->getUser();
        if (! $user) {
            $this->error('token 失效', '', 422);
        }

        $tier1Uids = db::name("user")->where('pid', $user->id)->column("id")??"";
        if (!$tier1Uids) {
            $tier2 = [];
            $tier3 = [];
        } else {
            $tier2 = db::name("user")->whereIn('pid', $tier1Uids)->column("id");
            if (!$tier2) {
                $tier3 = [];
            } else {
                $tier3 = db::name("user")->whereIn('pid', $tier2)->column("id");
            }
        }

        if ($tier1Uids) {
            foreach ($tier1Uids as $k => $v) {
                $tier1Uidss[$k]['id'] = $v;
                $tier1Uidss[$k]['mobile'] = db::name("user")->where('id', $v)->value('username');
                $tier1Uidss[$k]['createtime'] = date("Y-m-d H:i:s", db::name("user")->where('id', $v)->value('createtime'));
                $tier1Uidss[$k]['total_recharge'] = db::name("dotop")->where(['status' => 1, 'user_id' => $v, 'type' => 0])->sum("money");
                $tier1Uidss[$k]['total_bet'] = db::name("game_record")->where(['uid' => $v])->sum("valid_bet_amount");
            }

        }

        if ($tier2) {

            foreach ($tier2 as $k => $v) {
                $tier22[$k]['id'] = $v;
                $tier22[$k]['mobile'] = db::name("user")->where('id', $v)->value('username');
                $tier22[$k]['createtime'] = date("Y-m-d H:i:s", db::name("user")->where('id', $v)->value('createtime'));
                $tier22[$k]['total_recharge'] = db::name("dotop")->where(['status' => 1, 'user_id' => $v, 'type' => 0])->sum("money");
                $tier22[$k]['total_bet'] = db::name("game_record")->where(['uid' => $v])->sum("valid_bet_amount");

            }

        }
        if ($tier3) {
            foreach ($tier3 as $k => $v) {
                $tier33[$k]['id'] = $v;
                $tier33[$k]['mobile'] = db::name("user")->where('id', $v)->value('username');
                $tier33[$k]['createtime'] = date("Y-m-d H:i:s", db::name("user")->where('id', $v)->value('createtime'));
                $tier33[$k]['total_recharge'] = db::name("dotop")->where(['status' => 1, 'user_id' => $v, 'type' => 0])->sum("money");
                $tier33[$k]['total_bet'] = db::name("game_record")->where(['uid' => $v])->sum("valid_bet_amount");
            }

        }

        $item['tier1'] = $tier1Uidss??[]; //一级人数
        $item['tier2'] = $tier22??[]; //二级人数
        $item['tier3'] = $tier33??[]; //三级人数

        $item['one_num'] = count($tier1Uids);
        $item['two_num'] = count($tier2);
        $item['three_num'] = count($tier3);
        $item['one_fastmoney'] = db::name("dotop")->whereIn("user_id", $tier1Uids)->where(['is_sc' => 1])->sum("money");
        $item['two_fastmoney'] = db::name("dotop")->whereIn("user_id", $tier2)->where(['is_sc' => 1])->sum("money");
        $item['three_fastmoney'] = db::name("dotop")->whereIn("user_id", $tier3)->where(['is_sc' => 1])->sum("money");

        $item['one_total_recharge'] = db::name("dotop")->whereIn("user_id", $tier1Uids)->where(['status' => 1, 'type' => 0])->sum("money");
        $item['two_total_recharge'] = db::name("dotop")->whereIn("user_id", $tier2)->where(['status' => 1, 'type' => 0])->sum("money");
        $item['three_total_recharge'] = db::name("dotop")->whereIn("user_id", $tier3)->where(['status' => 1, 'type' => 0])->sum("money");


        $item['one_total_wih'] = db::name("dotop")->whereIn("user_id", $tier1Uids)->where(['status' => 1, 'type' => 1])->sum("money");
        $item['two_total_wih'] = db::name("dotop")->whereIn("user_id", $tier2)->where(['status' => 1, 'type' => 1])->sum("money");
        $item['three_total_wih'] = db::name("dotop")->whereIn("user_id", $tier3)->where(['status' => 1, 'type' => 1])->sum("money");


        $item['one_total_profit'] = db::name("user_money_log")->whereIn("type", [1, 2])->whereIn("user_id", $tier1Uids)->sum("money");
        $item['two_total_profit'] = db::name("user_money_log")->whereIn("type", [1, 2])->whereIn("user_id", $tier2)->sum("money");
        $item['three_total_profit'] = db::name("user_money_log")->whereIn("type", [1, 2])->whereIn("user_id", $tier3)->sum("money");


        if ($item) {
            $this->success("list", $item, 200);
        } else {
            $this->error("not data");
        }


    }
    /**
    * 获取我的vip信息内容
    * @ApiHeaders (name=token, type=string, required=true, description="请求的Token")
    * @ApiMethod (POST)
    */
    public function get_vips() {
        $user = $this->auth->getUser();
        if (! $user) {
            $this->error('token 失效', '', 422);
        }
        $vips = db::name("vips")->select();
        foreach ($vips as &$v) {
            $user->level >= $v['level']?$v['is_vip'] = 1:$v['is_vip'] = 0;
        }
        if ($vips) {
            $this->success("list", $vips, 200);
        } else {
            $this->error("not data");
        }


    }
    /**
    * 获取我的vip信息内容
    * @ApiHeaders (name=token, type=string, required=true, description="请求的Token")
    * @ApiMethod (POST)
    */
    public function vips_info() {
        $user = $this->auth->getUser();
        if (! $user) {
            $this->error('token 失效', '', 422);
        }
        // $id = $this->request->post('level');
        $vips = db::name("vips")->where("level", $user->level)->find();

        $vips['total_recharge'] = db::name("dotop")->where(['status' => 1, 'user_id' => $user->id, 'type' => 0])->sum("money");
        $vips['total_bet'] = db::name("game_record")->where(['uid' => $user->id])->sum("valid_bet_amount");

        $total_recharges = round($vips['total_recharge'], 2);
        $running_moneys = round($vips['running_money'], 2);

        if ($total_recharges >= $running_moneys) {

            $vips['recharge'] = $running_moneys."/".$running_moneys;
        } else {
            $vips['recharge'] = $total_recharges."/".$running_moneys;
        }


        $total_bets = round($vips['total_bet'], 2);
        $running_waters = round($vips['running_water'], 2);

        if ($total_bets >= $running_waters) {

            $vips['bet'] = $running_waters."/".$running_waters;
        } else {
            $vips['bet'] = $total_bets."/".$running_waters;
        }


        if ($vips['running_money'] != 0) {
            $recharge_b = ($vips['total_recharge'] / $vips['running_money']) * 100;
        } else {
            $recharge_b = 0; // 或者其他你认为适合的默认值
        }

        if ($vips['running_water'] != 0) {
            $bet_b = ($vips['total_bet'] / $vips['running_water']) * 100;
        } else {
            $bet_b = 0;
        }


        $vips['recharge_b'] = $recharge_b >= 100?100:round($recharge_b, 2);
        $vips['bet_b'] = $bet_b >= 100?100:round($bet_b, 2);


        if ($vips) {
            $this->success("list", $vips, 200);
        } else {
            $this->error("not data");
        }
    }




    /**
    * 获取充值配置
    * @ApiMethod (POST)
    */
    public function get_rechlist() {

        $vips = db::name("rechlist")->select();
        if ($vips) {
            $this->success("list", $vips, 200);
        } else {
            $this->error("not data");
        }
    }

    /**
    * 获取充值&提现 记录
    * @ApiHeaders (name=token, type=string, required=true, description="请求的Token")
    * @ApiMethod (POST)
    * @param string  $status 0提现 1充值
    * @param string $page 从1开始
    * @param string $list 分页长度,默认10
    */
    public function Wit_rech_list() {



        $user = $this->auth->getUser();

        if (! $user) {
            $this->error('token 失效', '', 422);
        }

        $status = $this->request->post('status');
        $page = $this->request->post('page') ?? 1;
        $list = $this->request->post('list') ?? 10;
        $ret = db::name("dotop")->where("user_id", $user->id)->where("type", $status)->order('id desc')->page($page, $list)->select();
        $retcount = db::name("dotop")->where("user_id", $user->id)->where("type", $status)->count();

        foreach ($ret as $k => $v) {
            $ret[$k]['addtime_txt'] = date("Y-m-d H:i:s", $v['addtime']);
        }
        $data['data'] = $ret??[];
        $data['tal'] = $retcount;
        if ($ret) {
            $this->success(__('ok'), $data, 200);
        } else {
            $this->success(__('ok'), $data, 200);
        }
    }



    /**
    * 获取通知列表
    * @return boolean
    */
    public function get_msg() {

        $ret = db::name("msg")->select();
        foreach ($ret as $k => $v) {
            $ret[$k]['addtime_txt'] = date("Y-m-d H:i:s", $v['addtime']);
        }
        if ($ret) {
            $this->success(__('ok'), $ret, 200);
        } else {
            $this->error(__('暂无数据'));
        }


    }

/**
 * 递归获取所有下级用户
 */
function getAllSubUsers($userId, &$result = []) {
    $users = db('user')->field('id, pid, mosalary_id')->where('pid', $userId)->select();
    
    foreach ($users as $user) {
        $result[] = $user;
        $this->getAllSubUsers($user['id'], $result);
    }
    
    return $result;
}

/**
 * 获取分享链接信息
 */
public function get_links() {
    $user = $this->auth->getUser();

    if (!$user) {
        $this->error('token 失效', '', 422);
    }

    $allChildren = $this->getAllSubUsers($this->auth->id);

    if ($allChildren) {
        $idArray = array_column($allChildren, "id");

        $truenum = \app\admin\model\Dotop::where('user_id', 'in', $idArray)
            ->where(['status' => 1, 'type' => 0])
            ->group('user_id')
            ->count();
        
        $truemoney = \app\admin\model\Moneylog::where('type', 'in', [2, 3])
            ->where('user_id', 'in', $idArray)
            ->where("money", '>=', 0)
            ->group('user_id')
            ->sum("money");

        $Mosa = \app\admin\model\Mosalary::where("nums", '<=', $truenum)
            ->where("money", '<=', $truemoney)
            ->order("id desc")
            ->find();

        if ($Mosa && $this->auth->mosalary_id !== $Mosa['id']) {
            db::name("user")->where("id", $this->auth->id)
                ->update(["updatetime" => time(), "mosalary_id" => $Mosa['id']]);
        }
    }

    // 用户统计信息
    $user['share_link'] = "https://wap.885856.com/#/?invitation_code=" . $this->auth->id;
    $user['share_code'] = $this->auth->id;
    $user['count_user'] = db::name("user")->where("pid", $this->auth->id)->count();
    $user['today_user'] = db::name("user")->whereTime("createtime", "today")->where("pid", $this->auth->id)->count();
    $user['count_money'] = db::name("user_money_log")->where("money", '>=', 0)->where("user_id", $this->auth->id)->sum("money");
    $user['today_money'] = db::name("user_money_log")->where("money", '>=', 0)->whereTime("createtime", "today")->where("user_id", $this->auth->id)->sum("money");
    
    $Mosav = \app\admin\model\Mosalary::where("id",$user->mosalary_id)
            ->find();
    if($Mosav){
        $moon_money=$Mosav['money']*$Mosav['yj'];
    }
    $user['moon_money'] = $moon_money??0;

    return $this->success("success", $user, 200);
}


    /**
    * 三方游戏记录入库方法
    *
    * @ApiHeaders (name=token, type=string, required=true, description="请求的Token")
    * @ApiMethod (POST)
    * @param string $plat_type 游戏平台编号
    */
    public function game_record() {

        $random = $this->generateRandomString(); // 生成随机字符串
        $sn = "o62"; // 商户前缀，请替换为实际的商户前缀
        $secretKey = "7KmoPK477z4pUXa0ioBnD2436C0J92t4"; // 请替换为实际的密钥
        $contentType = "application/json";
        // dump("cokejogo_".$user->id);die;
        // 组装请求数据
        $timezoneOffset = 8; // UTC +8 for China Standard Time
        $startTime = date('Y-m-d H:i:s', strtotime("-6 hours") + ($timezoneOffset * 3600));
        $endTime = date('Y-m-d H:i:s', strtotime("now") + ($timezoneOffset * 3600));
        $data = array(
            "currency" => "BRL",
            "startTime" => $startTime,
            "endTime" => $endTime,
            "pageNo" => '1',
            "pageSize" => '500'
        );
        // 将请求数据转换为JSON字符串
        $jsonData = json_encode($data);
        // 计算签名
        $sign = md5($random . $sn . $secretKey);
        // 设置请求头
        $headers = array(
            "sign: $sign",
            "random: $random",
            "sn: $sn",
            "Content-Type: $contentType"
        );
        // 发起POST请求
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://sa.api-bet.net/api/server/recordAll');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $responses = curl_exec($ch);
        //  dump($responses);die;
        $res = json_decode($responses, 1);
        $ret = $res['data']['list'];

        foreach ($ret as $key => $value) {
            $datas['bet_id'] = $value['gameOrderId'];
            $datas['uid'] = explode("coke0", $value['playerId'])[1];
            $datas['game_type_name'] = $value['platType'];
            $datas['bet_amount'] = floatval($value['betAmount']);
            $datas['valid_bet_amount'] = floatval($value['validAmount']);
            $datas['net_amount'] = $value['settledAmount'];
            $datas['pumping_amount'] = $value['betAmount'] - $value['validAmount'];
            // $datas['pay_amount'] = $value['validAmount'];
            $datas['currency'] = 'BRL';
            $datas['create_at'] = $value['betTime'];
            $datas['net_at'] = $value['lastUpdateTime'];
            $where['bet_id'] = $value['gameOrderId'];
            $where['game_type_name'] = $value['platType'];

            if (!db::name("game_record")->where($where)->find()) {
                db::name("game_record")->insert($datas);

            }
        }
        // die;
        $this->success('成功', $ret);


    }



    public function game_index() {

        $status = $this->request->post('status')??1;
        // dump($status);die;
        if ($status == 1) {
            $pgarr['img'] = "https://wap.885856.com/pg.png";
            $pgarr['data'] = db::name("gamelist")->where("platType", "pg")->order("des desc")->limit(0, 20)->select();

            $PParr['img'] = "https://wap.885856.com/pp.png";
            $PParr['data'] = db::name("gamelist")->where("platType", "pp")->order("id desc")->limit(0, 10)->select();

            $T1arr['img'] = "https://wap.885856.com/t1.png";
            $T1arr['data'] = db::name("gamelist")->where("platType", "t1")->order("id desc")->limit(0, 10)->select();

            $livearr['img'] = "https://wap.885856.com/evoimg.png";
            $livearr['data'] = db::name("gamelist")->where("gameType", 1)->limit(0, 4)->select();

            $data['PG Slots'] = $pgarr;
            $data['PP Slots'] = $PParr;
            $data['T1'] = $T1arr;
            $data['EVO Live'] = $livearr;

        } elseif ($status == 3) {

            $hotarr['img'] = "https://wap.885856.com/hot.png";
            $hotarr['data'] = db::name("gamelist")->where("is_hot", 1)->order("id desc")->select();
            $data['HOT Slots'] = $hotarr;

        } elseif ($status == 4) {


            $T1arr['img'] = "https://wap.885856.com/t1.png";
            $T1arr['data'] = db::name("gamelist")->where("platType", "t1")->order("id desc")->limit(0, 10)->select();
            $data['T1 Slots'] = $T1arr;

        } elseif ($status == 5) {
            $pgarr['img'] = "https://wap.885856.com/pg.png";
            $pgarr['data'] = db::name("gamelist")->where("platType", "pg")->order("des desc")->limit(0, 20)->select();
            $data['PG Slots'] = $pgarr;

        } elseif ($status == 6) {
            $livearr['img'] = "https://wap.885856.com/evoimg.png";
            $livearr['data'] = db::name("gamelist")->where("gameType", 1)->limit(0, 4)->select();
            $data['EVO Live'] = $livearr;
        }
        // dump($data);die;
        if ($data) {
            $this->success("list", $data, 200);
        } else {
            $this->error("not data");
        }



    }



    /**
    * 模糊查询游戏
    *
    * @ApiHeaders (name=token, type=string, required=true, description="请求的Token")
    * @ApiMethod (POST)
    * @param string $keyword
    * @param string $page 从1开始
    * @param string $list 分页长度,默认10
    */
    public function game_like() {

        $keyword = $this->request->post('keyword');
        $page = $this->request->post('page') ?? 1;
        $list = $this->request->post('list') ?? 10;
        $list = [];
        if ($keyword == '') {
            $this->error('Os parâmetros obrigatórios não podem estar vazios');
        }

        $list = db::name("gamelist")->where('gameName', 'like', '%' . $keyword . '%')->page($page, $list)->select();
        $listcont = db::name("gamelist")->where('gameName', 'like', '%' . $keyword . '%')->count();
        $data['data'] = $list;
        $data['tal'] = $listcont;
        $this->success('list', $data, 200);


    }


    /**
    * 检查用户是否收藏此游戏
    *
    * @ApiHeaders (name=token, type=string, required=true, description="请求的Token")
    * @ApiMethod (POST)
    * @param string $game_id 游戏id

    */
    public function look_like() {
        $user = $this->auth->getUser();
        if (! $user) {
            $this->error('token 失效', '', 422);
        }
        $game_id = $this->request->post('game_id');
        $page = $this->request->post('page') ?? 1;
        $list = $this->request->post('list') ?? 10;
        $list = [];
        if ($game_id == '') {
            $this->error('Os parâmetros obrigatórios não podem estar vazios');
        }
        $result = db::name("gamelist")->where("id", $game_id)->find();
        $gamelike = db::name("gamelike")->where("user_id", $user->id)->where("game_id", $game_id)->count();


        $result['is_collect'] = $gamelike;
        $this->success('list', $result, 200);


    }

    /**
    * 游戏详情
    *
    * @ApiHeaders (name=token, type=string, required=true, description="请求的Token")
    * @ApiMethod (POST)
    * @param string $gameType
    * @param string $code
    * @param string $page 从1开始
    * @param string $list 分页长度,默认10
    */
    public function game_detai() {

        $gameCode = $this->request->post('platType');
        $code = $this->request->post('gameType');
        $list = [];
        $where = [];
        if ($code) {
            $where['gameType'] = $code;
        }
        if ($gameCode) {
            $where['platType'] = $gameCode;
        }
        // if($code==2){
        //       $where['is_hot'] = 1;
        // }
        $list = db::name("gamelist")->where($where)->select();
        $listcont = db::name("gamelist")->where($where)->count();
        $data['data'] = $list;
        $data['tal'] = $listcont;
        $this->success('list', $data, 200);


    }
    public function in_game() {
	$user = $this->auth->getUser();
        if (! $user) {
            $this->error('token 失效', '', 422);
        }
        $ispc = $this->request->post('ispc');
        $back = $this->request->post('back');
        $code = $this->request->post('gameCode');
        $gameType = $this->request->post('gameType');
        $game = $this->request->post('platType');

	$guid = Random::uuid();
        $uuid = Random::uuid();

	$apiurl = 'https://api.pg-bo.me/external-game-launcher/api/v1/GetLaunchURLHTML?trace_id='.$guid;
	$operator_token='I-3ed41633c73142d8aa02cdf4a002705e';
	
	$extra_str = 'btt=1&ops='.$uuid.'&l=pt';
        $extra_args = urlencode($extra_str);
	$clientIp=$this->real_ip();

        $path = urlencode('/'.$code.'/index.html');
	$postData = 'operator_token='.$operator_token.'&path='.$path.'&extra_args='.$extra_args.'&url_type=game-entry&client_ip='.$clientIp;
	$headerArray = [
                'Content-Type: application/x-www-form-urlencoded',
                'Cache-Control: no-cache, no-store, must-revalidate',
            ];


	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL,$apiurl);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
	    $output = curl_exec($ch);
	    curl_close($ch);
	    // $json = json_decode($output,true);
	file_put_contents($this->pg_temp_file_name($uuid,$guid),$output);
	$url = "/api/index/output?uuid=".$uuid."&guid=".$guid;
        $this->success("list", $url, 200);
   }
   public function output(){
	$uuid=$this->request->get('uuid');
	$guid=$this->request->get('guid');
	$fn=$this->pg_temp_file_name($uuid,$guid);
	$html= file_get_contents($fn);
	unlink($fn);
	echo $html;
   }
   private function pg_temp_file_name($uuid,$guid){
	$code=base64_encode($uuid).'_'.base64_encode($guid);
	return TEMP_PATH."/".$code.".html";
   }

    private function real_ip()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
            foreach ($matches[0] AS $xip) {
            if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                    $ip = $xip;
                    break;
                }
            }
        } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
        } elseif (isset($_SERVER['HTTP_X_REAL_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_X_REAL_IP'])) {
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        }
        return $ip;
    }
    public function in_game_deprecated() {


        $user = $this->auth->getUser();
        if (! $user) {
            $this->error('token 失效', '', 422);
        }
        $ispc = $this->request->post('ispc');
        $back = $this->request->post('back');
        $code = $this->request->post('gameCode');
        $gameType = $this->request->post('gameType');
        $game = $this->request->post('platType');

        // 准备请求参数
        $random = $this->generateRandomString(); // 生成随机字符串
        $sn = "o62"; // 商户前缀，请替换为实际的商户前缀
        $secretKey = "7KmoPK477z4pUXa0ioBnD2436C0J92t4"; // 请替换为实际的密钥
        $contentType = "application/json";
        // dump("cokejogo_".$user->id);die;

        if ($game == "ag") {
            $currency = "23";
        } else if ($game == "jdb") {
            $currency = "en";
        } else if ($game == "EVO") {
            $currency = "en";
        } else {
            $currency = "23";
        }


        // 组装请求数据
        $data = array(
            "platType" => $game,
            "currency" => "BRL",
            "lang" => $currency,
            "playerId" => "coke0".$user->id,
            "gameType" => $gameType,
            "gameCode" => $code,
            "returnUrl" => "https://wap.885856.com",
            "ingress" => $ispc,
            "walletType" => "2",
            // 根据接口需要添加请求参数
            // ...
        );


        // 将请求数据转换为JSON字符串
        $jsonData = json_encode($data);

        // 计算签名
        $sign = md5($random . $sn . $secretKey);

        // 设置请求头
        $headers = array(
            "sign: $sign",
            "random: $random",
            "sn: $sn",
            "Content-Type: $contentType"
        );

        // 发起POST请求
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://sa.api-bet.net/api/server/create'); // 创建玩家
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $responses = curl_exec($ch);

        dump($headers);die;
        curl_setopt($ch, CURLOPT_URL, "https://sa.api-bet.net/api/server/gameUrl"); // 进入游戏
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        curl_close($ch);

        // 处理响应数据
	var_dump($response);
	die;

        $responseData = json_decode($response, true);
           dump($responseData);die;
        // dump($responseData);die;
        if ($responseData['code'] == 10000) {
            $url = $responseData['data']['url'];

            $this->success("list", $url, 200);
        } else {
            $this->error($responseData['msg']);
        }
    }
    /**
    * 获取免转钱包
    *
    * @ApiMethod (POST)
    */
    public function get_wallet() {
        $user = $this->auth->getUser();
        if (! $user) {
            $this->error('token 失效', '', 422);
        }

        //   dump($user->id);die;
        // 准备请求参数
        $random = $this->generateRandomString(); // 生成随机字符串
        $sn = "o62"; // 商户前缀，请替换为实际的商户前缀
        $secretKey = "7KmoPK477z4pUXa0ioBnD2436C0J92t4"; // 请替换为实际的密钥
        $contentType = "application/json";
        $data = array(
            "playerId" => "coke0".$user->id,
            "currency" => "BRL",
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
        curl_setopt($ch, CURLOPT_URL, "https://sa.api-bet.net/api/server/walletBalance"); // 替换为实际的接口URL
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        curl_close($ch);
        $res_code = json_decode($response, true);
        if ($res_code['code'] == 10000) {
            $money['balance'] = $res_code['data']['balance'];
            $money['money'] = $user->money;
            $this->success($res_code['msg'], $money, 200);
        } else {
            $this->error($res_code['msg'], []);
        }
    }

    /**
    * 免转钱包
    *
    * @ApiMethod (POST)
    * @param string $status.  1:转入、2:中心钱包转出、3:游戏转出
    * @param string $money 金额
    */
    public function walletTransfer() {
        $user = $this->auth->getUser();
        if (! $user) {
            $this->error('token 失效', '', 422);
        }
        $status = $this->request->post('status');
        $money = $this->request->post('money');
        $plat_type = $this->request->post('plat_type');
        if ($status != 3) {
            if ($money == '' || $status == '') {
                $this->error('Os parâmetros obrigatórios não podem estar vazios');
            }
        }


        // 准备请求参数
        $random = $this->generateRandomString(); // 生成随机字符串
        $sn = "o62"; // 商户前缀，请替换为实际的商户前缀
        $secretKey = "7KmoPK477z4pUXa0ioBnD2436C0J92t4"; // 请替换为实际的密钥
        $contentType = "application/json";

        //   dump($user->id);die;
        if ($status == 2) {
            $data = array(
                'orderId' => date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).rand(100000, 999999),
                "playerId" => "coke0".$user->id,
                "currency" => "BRL",
                "type" => $status,
                "amount" => $money,

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
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonhData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            curl_close($ch);

            $res_code = json_decode($response, true);
            // dump($res_code);die;
            if ($res_code['code'] == 10000) {
                \app\common\model\User::where('id', $user->id)->setInc('money', $money);
                $result = db::name('convert')->insert([
                    'user_id' => $user->id,
                    'balance' => $money,
                    'orderId' => $data['orderId'],
                    'type' => $status,
                    'currency' => "BRL",
                    'addtime' => time(),
                ]);

                $this->success($res_code['msg'], $res_code['data']['balance'], 200);
            } else {
                $this->error($res_code['msg'], []);
            }
        } elseif ($status == 3) {
            //一键转出
            $random = $this->generateRandomString(); // 生成随机字符串
            $sn = "o62"; // 商户前缀，请替换为实际的商户前缀
            $secretKey = "7KmoPK477z4pUXa0ioBnD2436C0J92t4"; // 请替换为实际的密钥
            $contentType = "application/json";

            $data = array(
                "platType" => $plat_type,
                "playerId" => "coke0".$user->id,
                "currency" => "BRL",
                "type" => 2,
                "amount" => $money,

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
            curl_setopt($ch, CURLOPT_URL, "https://sa.api-bet.net/api/server/transferAll"); // 替换为实际的接口URL
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
                \app\common\model\User::where('id', $user->id)->setInc('money', $res_code['data']['balanceAll']);
                $this->success('conversão bem-sucedida', 200, 200);
            } else {
                $this->error($res_code['msg'], []);
            }


        } else {
            // dump($user);die;

            //转入
            if ($user->money < $money) {
                $this->error('Saldo insuficiente');
            }
            $data = array(
                'orderId' => date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).rand(100000, 999999),
                "playerId" => "coke0".$user->id,
                "currency" => "BRL",
                "type" => $status,
                "amount" => $money,
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
                    'user_id' => $user->id,
                    'balance' => $money,
                    'orderId' => $data['orderId'],
                    'type' => $status,
                    'currency' => "BRL",
                    'addtime' => time(),
                ]);
                \app\common\model\User::where('id', $user->id)->setDec('money', $money);
                $this->success($res_code['msg'], $res_code['data']['balance'], 200);
            } else {
                $this->error($res_code['msg'], []);
            }
        }
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




    /**
    * 获取首页弹窗列表
    *
    * @ApiMethod (POST)

    */
    public function conts() {

        $list = db::name("conts")->select();
        $listcont = db::name("conts")->count();
        $data['data'] = $list;
        $data['tal'] = $listcont;

        if ($data) {
            $this->success('list', $data, 200);
        } else {
            $this->error("not data");
        }



    }


    /**
    * 获取活动
    *
    * @ApiMethod (POST)

    */
    public function activity() {

        $data = db::name("ac")->select();

        if ($data) {
            $this->success("list", $data, 200);
        } else {
            $this->error("not data");
        }



    }

    /**
    * 获取活动详情
    *@param string $id 活动id
    * @ApiMethod (POST)

    */
    public function get_acdtai() {
        $id = $this->request->post('id');
        $data = db::name("ac")->where("id", $id)->find();

        if ($data) {
            $this->success("list", $data, 200);
        } else {
            $this->error("not data");
        }


    }


    //检查是否满足月薪模式

    public function weekmstat() {
        $result = false;
        Db::startTrans();
        try {
            Db::name('user')->where('level',
                '>',
                0)->chunk(100,
                function($users) {
                    foreach ($users as $k => $v) {
                        $week = db::name("user_money_log")->where("type", 4)->whereTime('createtime', 'week')->where("user_id", $v['id'])->find();
                        if (!$week) {
                            $vips = db::name("vips")->where("level", $v['level'])->find();
                            \app\common\model\User::money($vips['week'], $v['id'], "Recompensas semanais", 4);
                        }

                    }
                });
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
    }


    //周奖励

    public function week() {
        $result = false;
        Db::startTrans();
        try {
            Db::name('user')->where('level',
                '>',
                0)->chunk(100,
                function($users) {
                    foreach ($users as $k => $v) {
                        $week = db::name("user_money_log")->where("type", 4)->whereTime('createtime', 'week')->where("user_id", $v['id'])->find();
                        if (!$week) {
                            $vips = db::name("vips")->where("level", $v['level'])->find();
                            \app\common\model\User::money($vips['week'], $v['id'], "Recompensas semanais", 4);
                        }

                    }
                });
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
    }

    //月奖励
    public function month() {
        $result = false;
        Db::startTrans();
        try {
            Db::name('user')->where('level',
                '>',
                0)->chunk(100,
                function($users) {
                    foreach ($users as $k => $v) {
                        $month = db::name("user_money_log")->where("type", 5)->whereTime('createtime', 'month')->where("user_id", $v['id'])->find();
                        if (!$month) {
                            $vips = db::name("vips")->where("level", $v['level'])->find();
                            \app\common\model\User::money($vips['moon'], $v['id'], "Recompensas semanais", 5);
                        }
                    }
                });
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
    }

    //投注佣金 所有游戏流水的 0.5‰
    public function money_self() {
        $result = false;
        Db::startTrans();
        try {
            Db::name('game_record')->where('is_me',
                '=',
                0)->chunk(100,
                function($users) {
                    // dump($users); die;
                    if (!$users) {
                        echo"暂无返佣"; die;
                    }
                    $selfmoney = db::name("config")->where("name", "selfmoney")->value("value")/100;
                    // $dayrate = Db::name('config')->where('name', 'dayrate')->value("value");
                    foreach ($users as $k => $v) {
                        $newmoney = $v['valid_bet_amount']*$selfmoney;
                        // dump(round($newmoney,2));
                        
                        \app\common\model\User::money($newmoney, $v['uid'], "Comissão de apostas", 3);
                        db::name("game_record")->where("id", $v['id'])->update(["is_me" => 1]);
                    }
                });
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


    }




    //三级分销收益分发
   public function Scheduled_Tasks() {
    $result = false;
    Db::startTrans();
    try {
        Db::name('game_record')->where('is_fl', '=', 0)->chunk(100, function ($users) {
            foreach ($users as $k => $v) {
                $pid = Db::name('user')->where('id', $v['uid'])->value("pid");
                if (isset($pid)) {
                    $this->Tertiary_distribution($pid, $v['valid_bet_amount'], $v['id'], 1); // 从1级开始分佣
                }
            }
        });
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
}

public function Tertiary_distribution($userId, $money, $orderId, $currentLevel) {
    $rebateRates = [
        0, // 占位符，0级没有分佣
        $this->getRebateRate('one'),
        $this->getRebateRate('two'),
        $this->getRebateRate('three'),
    ];

    $this->processRebate($money, $userId, $rebateRates, $orderId, $currentLevel);
}

private function processRebate($money, $userId, $rebateRates, $orderId, $currentLevel) {
    if ($currentLevel >= count($rebateRates)) {
        return;
    }

    $user = db::name("user")->where("id", $userId)->field('id,pid')->find();
// dump($user);
    if ($user['id']) {
        $rebateAmount = $money * $rebateRates[$currentLevel];
        \app\common\model\User::money($rebateAmount, $user['id'], "{$currentLevel}desconto de nível", 2);

        $this->processRebate($money, $user['pid'], $rebateRates, $orderId, $currentLevel + 1); // 继续处理上级
    }

    db::name("game_record")->where("id", $orderId)->update(["is_fl" => 1]);
}

private function getRebateRate($levelName) {
    return db::name("config")->where("name", $levelName)->value("value") / 100;
}




}
