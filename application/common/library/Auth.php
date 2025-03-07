<?php

namespace app\common\library;

use app\common\model\User;
use app\common\model\UserRule;
use fast\Random;
use think\Config;
use think\Db;
use think\Exception;
use think\Hook;
use think\Request;
use think\Validate;

class Auth
{
    protected static $instance = null;
    protected $_error = '';
    protected $_logined = false;
    protected $_user = null;
    protected $_token = '';
    //Token默认有效时长
    protected $keeptime = 2592000;
    protected $requestUri = '';
    protected $rules = [];
    //默认配置
    protected $config = [];
    protected $options = [];
    protected $allowFields = ['id',
        'username',
        'nickname',
        'level',
        'mobile',
        'avatar',
        'money',
        'score',
        'stat',
        'pid','current_mosaic','current_times','mosalary_id'];

    public function __construct($options = []) {
        if ($config = Config::get('user')) {
            $this->config = array_merge($this->config, $config);
        }
        $this->options = array_merge($this->config, $options);
    }

    /**
    *
    * @param array $options 参数
    * @return Auth
    */
    public static function instance($options = []) {
        if (is_null(self::$instance)) {
            self::$instance = new static($options);
        }

        return self::$instance;
    }

    /**
    * 获取User模型
    * @return User
    */
    public function getUser() {
        return $this->_user;
    }

    /**
    * 兼容调用user模型的属性
    *
    * @param string $name
    * @return mixed
    */
    public function __get($name) {
        return $this->_user ? $this->_user->$name : null;
    }

    /**
    * 兼容调用user模型的属性
    */
    public function __isset($name) {
        return isset($this->_user) ? isset($this->_user->$name) : false;
    }

    /**
    * 根据Token初始化
    *
    * @param string $token Token
    * @return boolean
    */
    public function init($token) {
        if ($this->_logined) {
            return true;
        }
        if ($this->_error) {
            return false;
        }
        $data = Token::get($token);
        if (!$data) {
            return false;
        }
        $user_id = intval($data['user_id']);
        if ($user_id > 0) {
            $user = User::get($user_id);
            if (!$user) {
                $this->setError('Account not exist');
                return false;
            }

            if ($user['stat'] == 1) {
                $this->setError('Account is locked');
                return false;
            }

            // if ($user['status'] != 'normal') {
            //     $this->setError('Account is locked');
            //     return false;
            // }
            $this->_user = $user;
            $this->_logined = true;
            $this->_token = $token;

            //初始化成功的事件
            Hook::listen("user_init_successed", $this->_user);

            return true;
        } else {
            $this->setError('You are not logged in');
            return false;
        }
    }

    /**
    * 注册用户
    *
    * @param string $username 用户名
    * @param string $password 密码
    * @param string $email    邮箱
    * @param string $mobile   手机号
    * @param array  $extend   扩展参数
    * @return boolean
    */
    public function register($username, $password, $email = '', $mobile = '', $extend ) {

        $retuel = db::name("attachment")->orderRaw("RAND()")->where("category", "images")->find();
        $returl = "https://wap.885856.com".$retuel['url'];


        // 检测用户名、昵称、邮箱、手机号是否存在
        if (User::getByUsername($username)) {
            $this->setError('Nome de usuário existente');
            return false;
        }

        // if (User::getByNickname($username)) {
        //     $this->setError('Nickname already exist');
        //     return false;
        // }


        //  dump($extend);die;
        if ($mobile && User::getByMobile($mobile)) {
            $this->setError('Celular já existe');
            return false;
        }

        $ip = request()->ip();
        $time = time();

        $data = [
            'username' => $username,
            'password' => $password,
            // 'email'    => $email,
            'mobile' => $mobile,
            'level' => 0,
            'stat' => 0,
            'score' => 0,
            'avatar' => $returl,
        ];
        $params = array_merge($data, [
            'nickname' => preg_match("/^1[3-9]{1}\d{9}$/", $username) ? substr_replace($username, '****', 3, 4) : $username,
            'salt' => Random::alnum(),
            'jointime' => $time,
            'joinip' => $ip,
            'logintime' => $time,
            'loginip' => $ip,
            'prevtime' => $time,
            'status' => 'normal'
        ]);
        $params['password'] = $this->getEncryptPassword($password, $params['salt']);
        $params = array_merge($params, $extend);
        // dump($params);die;

        //账号注册时需要开启事务,避免出现垃圾数据
        Db::startTrans();
        try {
            $user = User::create($params, true);

            $this->_user = User::get($user->id);

            //设置Token
            $this->_token = Random::uuid();
            Token::set($this->_token, $user->id, $this->keeptime);

            //设置登录状态
            $this->_logined = true;

            //注册成功的事件
            Hook::listen("user_register_successed", $this->_user, $data);
            Db::commit();
        } catch (Exception $e) {
            $this->setError($e->getMessage());
            Db::rollback();
            return false;
        }
        return true;
    }

    /**
    * 用户登录
    *
    * @param string $account  账号,用户名、邮箱、手机号
    * @param string $password 密码
    * @return boolean
    */
    public function login($account, $password) {
       
        // $field = Validate::is($account, 'email') ? 'email' : (Validate::regex($account, '/^1\d{10}$/') ? 'mobile' : 'username');
        $user = User::get(["username" => $account]);
        // dump($user);
        // dump($field);
        // die;
        if (!$user) {
            $this->setError('A conta está incorreta');
            return false;
        }
        if ($user->stat == 1) {
            $this->setError('A conta está bloqueada');
            return false;
        }
        // if ($user->status != 'normal') {
        //     $this->setError('Account is locked');
        //     return false;
        // }
        if ($user->password != $this->getEncryptPassword($password, $user->salt)) {
            $this->setError('senha é incorreta');
            return false;
        }

        //直接登录会员
        return $this->direct($user->id);
    }

    /**
    * 退出
    *
    * @return boolean
    */
    public function logout() {
        if (!$this->_logined) {
            $this->setError('You are not logged in');
            return false;
        }
        //设置登录标识
        $this->_logined = false;
        //删除Token
        Token::delete($this->_token);
        //退出成功的事件
        Hook::listen("user_logout_successed", $this->_user);
        return true;
    }

    /**
    * 修改密码
    * @param string $newpassword       新密码
    * @param string $oldpassword       旧密码
    * @param bool   $ignoreoldpassword 忽略旧密码
    * @return boolean
    */
    public function changepwd($newpassword, $oldpassword = '', $ignoreoldpassword = false) {
        if (!$this->_logined) {
            $this->setError('You are not logged in');
            return false;
        }
        //判断旧密码是否正确
        if ($this->_user->password == $this->getEncryptPassword($oldpassword, $this->_user->salt) || $ignoreoldpassword) {
            Db::startTrans();
            try {
                $salt = Random::alnum();
                $newpassword = $this->getEncryptPassword($newpassword, $salt);
                $this->_user->save(['loginfailure' => 0, 'password' => $newpassword, 'salt' => $salt]);

                Token::delete($this->_token);
                //修改密码成功的事件
                Hook::listen("user_changepwd_successed", $this->_user);
                Db::commit();
            } catch (Exception $e) {
                Db::rollback();
                $this->setError($e->getMessage());
                return false;
            }
            return true;
        } else {
            $this->setError('Password is incorrect');
            return false;
        }
    }

    /**
    * 直接登录账号
    * @param int $user_id
    * @return boolean
    */
    public function direct($user_id) {
        $user = User::get($user_id);
        if ($user) {
            Db::startTrans();
            try {
                $ip = request()->ip();
                $time = time();

                //判断连续登录和最大连续登录
                if ($user->logintime < \fast\Date::unixtime('day')) {
                    $user->successions = $user->logintime < \fast\Date::unixtime('day', -1) ? 1 : $user->successions + 1;
                    $user->maxsuccessions = max($user->successions, $user->maxsuccessions);
                }

                $user->prevtime = $user->logintime;
                //记录本次登录的IP和时间
                $user->loginip = $ip;
                $user->logintime = $time;
                //重置登录失败次数
                $user->loginfailure = 0;

                $user->save();

                $this->_user = $user;

                $this->_token = Random::uuid();
                Token::set($this->_token, $user->id, $this->keeptime);

                $this->_logined = true;

                //登录成功的事件
                Hook::listen("user_login_successed", $this->_user);
                Db::commit();
            } catch (Exception $e) {
                Db::rollback();
                $this->setError($e->getMessage());
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
    * 检测是否是否有对应权限
    * @param string $path   控制器/方法
    * @param string $module 模块 默认为当前模块
    * @return boolean
    */
    public function check($path = null, $module = null) {
        if (!$this->_logined) {
            return false;
        }

        $ruleList = $this->getRuleList();
        $rules = [];
        foreach ($ruleList as $k => $v) {
            $rules[] = $v['name'];
        }
        $url = ($module ? $module : request()->module()) . '/' . (is_null($path) ? $this->getRequestUri() : $path);
        $url = strtolower(str_replace('.', '/', $url));
        return in_array($url, $rules) ? true : false;
    }

    /**
    * 判断是否登录
    * @return boolean
    */
    public function isLogin() {
        if ($this->_logined) {
            return true;
        }
        return false;
    }

    /**
    * 获取当前Token
    * @return string
    */
    public function getToken() {
        return $this->_token;
    }

   
    /**
    * 获取会员基本信息
    */
    public function getUserinfo() {
        $data = $this->_user->toArray();
        $allowFields = $this->getAllowFields();
        $userinfo = array_intersect_key($data, array_flip($allowFields));
        $userinfo = array_merge($userinfo, Token::get($this->_token));
        $dam = db::name("config")->where("name", "dam")->value("value")??0;
        
        $upmoeny = db::name("config")->where("name", "upmoeny")->value("value")??0;
        $gamenum = db::name("config")->where("name", "gamenum")->value("value")??0;
        $userinfo['upmoeny']=$upmoeny;
        $userinfo['gamenum']=$gamenum;
        
        $levelS = db::name("vips")->max("level");
        //dump($levelS);die;
        if($userinfo['level']+1  >= $levelS){
           $userinfo['level'] = $levelS;
        }
    
        
        $vips = db::name("vips")->where("level", $userinfo['level']+1)->find();
        if (!$vips) {
            $vips = db::name("vips")->where("level", 0)->find();

        }
        // dump($vips);die;
           $userinfo['vip_img']=$vips['image'];

        $vips['total_recharge'] = db::name("dotop")->where(['status' => 1, 'user_id' => $userinfo['id'], 'type' => 0])->sum("money");
        $vips['total_bet'] = db::name("game_record")->where(['uid' => $userinfo['id']])->sum("valid_bet_amount");


        //二开逻辑
        $level = db::name("vips")->where("running_water", '<=', $vips['total_bet'])->where("running_money", '<=', $vips['total_recharge'])->order("id desc")->find();
        // dump($level);
        if (!$level) {
            $level['level'] = 0;
        }
        //不等于原来等级
        if ($userinfo['level'] !== $level['level']??0) {
            db::name("user")->where("id", $userinfo['id'])->update(["updatetime" => time(), "level" => $level['level']]);
            \app\common\model\User::money($level['vip_up'], $userinfo['id'], "Atualizar VIP".$level['level']."prêmio", 2);
        }


        if ($userinfo['current_mosaic'] > 0) {
            $damok = db::name("game_record")->where("create_at", '>=', date("Y-m-d H:i:s", $userinfo['current_times']))->where(['uid' => $userinfo['id']])->sum("valid_bet_amount");

            if ($damok >= ($userinfo['current_mosaic']*$dam)) {
                //完成打码量 打码量清零
                db::name("user")->where("id", $userinfo['id'])->update(["current_mosaic" => 0, "current_times" => 0]);
            }
        }



       
        $total_recharges = round($vips['total_recharge'], 2);
        $running_moneys = round($vips['running_money'], 2);

        if ($total_recharges >= $running_moneys) {

            $userinfo['recharge'] = "R$ ".$running_moneys."/"."R$ ".$running_moneys;
        } else {
            $userinfo['recharge'] = "R$ ".$total_recharges." / "."R$ ".$running_moneys;
        }


        $total_bets = round($vips['total_bet'], 2);
        $running_waters = round($vips['running_water'], 2);

        if ($total_bets >= $running_waters) {

            $userinfo['bet'] = "R$ ".$running_waters."/"."R$ ".$running_waters;
        } else {
            $userinfo['bet'] = "R$ ".$total_bets."/"."R$ ".$running_waters;
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

        // dump($vips);die;
        $userinfo['current_mosaic']=$userinfo['current_mosaic']*$dam;
        $userinfo['recharge_b'] = $recharge_b >= 100?100:round($recharge_b, 2);
        $userinfo['bet_b'] = $bet_b >= 100?100:round($bet_b, 2);
        $userinfo['record_amount']  = db::name("game_record")->where("create_at",'>=',date("Y-m-d H:i:s",$userinfo['current_times']))->where(['uid' =>$userinfo['id']])->sum("valid_bet_amount");
        



        return $userinfo;
    }
    
    
    
    

    /**
    * 获取会员组别规则列表
    * @return array
    */
    public function getRuleList() {
        if ($this->rules) {
            return $this->rules;
        }
        $group = $this->_user->group;
        if (!$group) {
            return [];
        }
        $rules = explode(',', $group->rules);
        $this->rules = UserRule::where('status', 'normal')->where('id', 'in', $rules)->field('id,pid,name,title,ismenu')->select();
        return $this->rules;
    }

    /**
    * 获取当前请求的URI
    * @return string
    */
    public function getRequestUri() {
        return $this->requestUri;
    }

    /**
    * 设置当前请求的URI
    * @param string $uri
    */
    public function setRequestUri($uri) {
        $this->requestUri = $uri;
    }

    /**
    * 获取允许输出的字段
    * @return array
    */
    public function getAllowFields() {
        return $this->allowFields;
    }

    /**
    * 设置允许输出的字段
    * @param array $fields
    */
    public function setAllowFields($fields) {
        $this->allowFields = $fields;
    }

    /**
    * 删除一个指定会员
    * @param int $user_id 会员ID
    * @return boolean
    */
    public function delete($user_id) {
        $user = User::get($user_id);
        if (!$user) {
            return false;
        }
        Db::startTrans();
        try {
            // 删除会员
            User::destroy($user_id);
            // 删除会员指定的所有Token
            Token::clear($user_id);

            Hook::listen("user_delete_successed", $user);
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            $this->setError($e->getMessage());
            return false;
        }
        return true;
    }

    /**
    * 获取密码加密后的字符串
    * @param string $password 密码
    * @param string $salt     密码盐
    * @return string
    */
    public function getEncryptPassword($password, $salt = '') {
        return md5(md5($password) . $salt);
    }

    /**
    * 检测当前控制器和方法是否匹配传递的数组
    *
    * @param array $arr 需要验证权限的数组
    * @return boolean
    */
    public function match($arr = []) {
        $request = Request::instance();
        $arr = is_array($arr) ? $arr : explode(',', $arr);
        if (!$arr) {
            return false;
        }
        $arr = array_map('strtolower', $arr);
        // 是否存在
        if (in_array(strtolower($request->action()), $arr) || in_array('*', $arr)) {
            return true;
        }

        // 没找到匹配
        return false;
    }

    /**
    * 设置会话有效时间
    * @param int $keeptime 默认为永久
    */
    public function keeptime($keeptime = 0) {
        $this->keeptime = $keeptime;
    }

    /**
    * 渲染用户数据
    * @param array  $datalist  二维数组
    * @param mixed  $fields    加载的字段列表
    * @param string $fieldkey  渲染的字段
    * @param string $renderkey 结果字段
    * @return array
    */
    public function render(&$datalist, $fields = [], $fieldkey = 'user_id', $renderkey = 'userinfo') {
        $fields = !$fields ? ['id',
            'nickname',
            'level',
            'avatar'] : (is_array($fields) ? $fields : explode(',', $fields));
        $ids = [];
        foreach ($datalist as $k => $v) {
            if (!isset($v[$fieldkey])) {
                continue;
            }
            $ids[] = $v[$fieldkey];
        }
        $list = [];
        if ($ids) {
            if (!in_array('id', $fields)) {
                $fields[] = 'id';
            }
            $ids = array_unique($ids);
            $selectlist = User::where('id', 'in', $ids)->column($fields);
            foreach ($selectlist as $k => $v) {
                $list[$v['id']] = $v;
            }
        }
        foreach ($datalist as $k => &$v) {
            $v[$renderkey] = isset($list[$v[$fieldkey]]) ? $list[$v[$fieldkey]] : null;
        }
        unset($v);
        return $datalist;
    }

    /**
    * 设置错误信息
    *
    * @param string $error 错误信息
    * @return Auth
    */
    public function setError($error) {
        $this->_error = $error;
        return $this;
    }

    /**
    * 获取错误信息
    * @return string
    */
    public function getError() {
        return $this->_error ? __($this->_error) : '';
    }
}