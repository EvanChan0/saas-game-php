-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 20, 2025 at 12:52 PM
-- Server version: 5.7.44-log
-- PHP Version: 7.2.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sql_yislots_com`
--

-- --------------------------------------------------------

--
-- Table structure for table `fa_ac`
--

CREATE TABLE `fa_ac` (
  `id` int(11) NOT NULL,
  `titile` varchar(299) NOT NULL COMMENT '标题',
  `image` varchar(299) NOT NULL COMMENT '预览图',
  `stub` varchar(299) NOT NULL COMMENT '活动简介',
  `content` text NOT NULL COMMENT '活动内容'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='活动管理' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_activity_config`
--

CREATE TABLE `fa_activity_config` (
  `id` int(11) NOT NULL,
  `deposit_limit` int(11) NOT NULL COMMENT '充值金额限制',
  `bet_limit` int(11) NOT NULL COMMENT '投注金额限制',
  `activity` varchar(20) NOT NULL DEFAULT '' COMMENT '活动名称'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='活动配置';

-- --------------------------------------------------------

--
-- Table structure for table `fa_admin`
--

CREATE TABLE `fa_admin` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `username` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '用户名',
  `nickname` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '昵称',
  `password` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '密码',
  `salt` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '密码盐',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '头像',
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '电子邮箱',
  `mobile` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '手机号码',
  `loginfailure` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '失败次数',
  `logintime` bigint(20) DEFAULT NULL COMMENT '登录时间',
  `loginip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '登录IP',
  `createtime` bigint(20) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(20) DEFAULT NULL COMMENT '更新时间',
  `token` varchar(59) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'Session标识',
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal' COMMENT '状态',
  `user_id` int(11) NOT NULL,
  `googlesecret` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员表' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_admin_log`
--

CREATE TABLE `fa_admin_log` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `username` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '管理员名字',
  `url` varchar(1500) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '操作页面',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '日志标题',
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '内容',
  `ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'IP',
  `useragent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'User-Agent',
  `createtime` bigint(20) DEFAULT NULL COMMENT '操作时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员日志表' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_area`
--

CREATE TABLE `fa_area` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `pid` int(11) DEFAULT NULL COMMENT '父id',
  `shortname` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '简称',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '名称',
  `mergename` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '全称',
  `level` tinyint(4) DEFAULT NULL COMMENT '层级:1=省,2=市,3=区/县',
  `pinyin` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '拼音',
  `code` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '长途区号',
  `zip` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '邮编',
  `first` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '首字母',
  `lng` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '经度',
  `lat` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '纬度'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='地区表' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_attachment`
--

CREATE TABLE `fa_attachment` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '类别',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '会员ID',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '物理路径',
  `imagewidth` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '宽度',
  `imageheight` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '高度',
  `imagetype` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '图片类型',
  `imageframes` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '图片帧数',
  `filename` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '文件名称',
  `filesize` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '文件大小',
  `mimetype` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'mime类型',
  `extparam` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '透传数据',
  `createtime` bigint(20) DEFAULT NULL COMMENT '创建日期',
  `updatetime` bigint(20) DEFAULT NULL COMMENT '更新时间',
  `uploadtime` bigint(20) DEFAULT NULL COMMENT '上传时间',
  `storage` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'local' COMMENT '存储位置',
  `sha1` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '文件 sha1编码'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='附件表' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_auth_group`
--

CREATE TABLE `fa_auth_group` (
  `id` int(10) UNSIGNED NOT NULL,
  `pid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父组别',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '组名',
  `rules` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '规则ID',
  `createtime` bigint(20) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(20) DEFAULT NULL COMMENT '更新时间',
  `status` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='分组表' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_auth_group_access`
--

CREATE TABLE `fa_auth_group_access` (
  `uid` int(10) UNSIGNED NOT NULL COMMENT '会员ID',
  `group_id` int(10) UNSIGNED NOT NULL COMMENT '级别ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='权限分组表' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_auth_rule`
--

CREATE TABLE `fa_auth_rule` (
  `id` int(10) UNSIGNED NOT NULL,
  `type` enum('menu','file') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'file' COMMENT 'menu为菜单,file为权限节点',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父ID',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '规则名称',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '规则名称',
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '图标',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '规则URL',
  `condition` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '条件',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '备注',
  `ismenu` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否为菜单',
  `menutype` enum('addtabs','blank','dialog','ajax') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '菜单类型',
  `extend` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '扩展属性',
  `py` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '拼音首字母',
  `pinyin` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '拼音',
  `createtime` bigint(20) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(20) DEFAULT NULL COMMENT '更新时间',
  `weigh` int(11) NOT NULL DEFAULT '0' COMMENT '权重',
  `status` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='节点表' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_bank`
--

CREATE TABLE `fa_bank` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT '用户编号',
  `firstname` varchar(299) NOT NULL COMMENT '姓',
  `lastname` varchar(299) NOT NULL COMMENT '名',
  `email` varchar(299) NOT NULL COMMENT '邮箱',
  `phone` varchar(299) NOT NULL COMMENT '电话',
  `cpf` varchar(299) NOT NULL COMMENT '稅号',
  `pix` varchar(299) NOT NULL COMMENT 'pix账户',
  `pixtype` varchar(299) NOT NULL COMMENT 'pix账户类型',
  `updatetime` varchar(299) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='提现信息绑定' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_category`
--

CREATE TABLE `fa_category` (
  `id` int(10) UNSIGNED NOT NULL,
  `pid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父ID',
  `type` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '栏目类型',
  `name` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `nickname` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `flag` set('hot','index','recommend') COLLATE utf8mb4_unicode_ci DEFAULT '',
  `image` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '图片',
  `keywords` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '关键字',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '描述',
  `diyname` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '自定义名称',
  `createtime` bigint(20) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(20) DEFAULT NULL COMMENT '更新时间',
  `weigh` int(11) NOT NULL DEFAULT '0' COMMENT '权重',
  `status` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='分类表' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_ce`
--

CREATE TABLE `fa_ce` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_chests`
--

CREATE TABLE `fa_chests` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL COMMENT 'name',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '0生效，-1无效',
  `rewards` decimal(10,0) NOT NULL COMMENT '红包金额',
  `limits` int(11) NOT NULL COMMENT '领取限制人数'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='宝箱设置';

-- --------------------------------------------------------

--
-- Table structure for table `fa_command`
--

CREATE TABLE `fa_command` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `type` varchar(30) NOT NULL DEFAULT '' COMMENT '类型',
  `params` varchar(1500) NOT NULL DEFAULT '' COMMENT '参数',
  `command` varchar(1500) NOT NULL DEFAULT '' COMMENT '命令',
  `content` text COMMENT '返回结果',
  `executetime` bigint(20) UNSIGNED DEFAULT NULL COMMENT '执行时间',
  `createtime` bigint(20) UNSIGNED DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(20) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  `status` enum('successed','failured') NOT NULL DEFAULT 'failured' COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='在线命令表' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_config`
--

CREATE TABLE `fa_config` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '变量名',
  `group` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '分组',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '变量标题',
  `tip` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '变量描述',
  `type` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '类型:string,text,int,bool,array,datetime,date,file',
  `visible` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '可见条件',
  `value` text COLLATE utf8mb4_unicode_ci COMMENT '变量值',
  `content` text COLLATE utf8mb4_unicode_ci COMMENT '变量字典数据',
  `rule` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '验证规则',
  `extend` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '扩展属性',
  `setting` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '配置'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统配置' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_contents`
--

CREATE TABLE `fa_contents` (
  `id` int(11) NOT NULL,
  `name` varchar(299) NOT NULL COMMENT '名称',
  `content` text NOT NULL COMMENT '富文本',
  `type` int(11) NOT NULL COMMENT '类型'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='内容管理' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_conts`
--

CREATE TABLE `fa_conts` (
  `id` int(11) NOT NULL,
  `stat` int(11) NOT NULL COMMENT '开关',
  `content` text NOT NULL COMMENT '富文本'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='首页弹窗管理' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_convert`
--

CREATE TABLE `fa_convert` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `balance` decimal(10,2) NOT NULL,
  `orderId` varchar(555) NOT NULL,
  `type` int(11) NOT NULL,
  `currency` varchar(299) NOT NULL,
  `addtime` varchar(299) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='转换记录' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_dotop`
--

CREATE TABLE `fa_dotop` (
  `id` int(11) NOT NULL,
  `info` varchar(299) NOT NULL COMMENT '信息',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `username` varchar(299) NOT NULL COMMENT '上分对象',
  `me_user` varchar(299) NOT NULL COMMENT '提交方',
  `money` decimal(10,2) NOT NULL COMMENT '上分金额',
  `dai_id` int(11) NOT NULL COMMENT '代理id',
  `addtime` varchar(299) NOT NULL COMMENT '申请时间',
  `type` int(11) NOT NULL COMMENT '0充值1提现',
  `status` int(11) NOT NULL COMMENT '0：自动审 2：错误  3：等待中  4：需人工审  5：已拒绝',
  `uptime` varchar(299) NOT NULL COMMENT '审核时间',
  `is_get` int(11) NOT NULL COMMENT '是否充值奖励',
  `customerCert` varchar(100) DEFAULT NULL,
  `customerName` varchar(100) DEFAULT NULL,
  `accountNum` varchar(100) DEFAULT NULL,
  `accountType` varchar(100) CHARACTER SET armscii8 DEFAULT NULL,
  `moneys` decimal(10,2) NOT NULL COMMENT '真实上分金额',
  `sxf` decimal(10,2) DEFAULT NULL,
  `orderNo` varchar(100) NOT NULL COMMENT '订单号',
  `pro_money` decimal(10,2) DEFAULT NULL COMMENT '比例赠送金额',
  `checkInvite` int(11) DEFAULT '0',
  `IP` varchar(20) DEFAULT NULL,
  `fk_type` int(11) DEFAULT '0' COMMENT '0:正常 1：博主  2：IP异常  3：充提差过大  ',
  `fk_info` varchar(200) DEFAULT NULL,
  `withdraw_today` decimal(20,2) DEFAULT '0.00',
  `withdraw_nums` int(11) DEFAULT '0',
  `is_frist` int(11) NOT NULL DEFAULT '0' COMMENT '是否首充 1：首充',
  `now_score` int(11) NOT NULL DEFAULT '0',
  `now_needscore` varchar(2000) DEFAULT NULL,
  `is_yf` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='上分审核管理' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_dotops`
--

CREATE TABLE `fa_dotops` (
  `id` int(11) NOT NULL,
  `info` varchar(299) NOT NULL COMMENT '信息',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `username` varchar(299) NOT NULL COMMENT '上分对象',
  `me_user` varchar(299) NOT NULL COMMENT '提交方',
  `money` decimal(10,2) NOT NULL COMMENT '上分金额',
  `dai_id` int(11) NOT NULL COMMENT '代理id',
  `addtime` varchar(299) NOT NULL COMMENT '申请时间',
  `type` int(11) NOT NULL COMMENT '0充值1提现',
  `status` int(11) NOT NULL COMMENT '状态',
  `uptime` varchar(299) NOT NULL COMMENT '审核时间',
  `is_get` int(11) NOT NULL COMMENT '是否充值奖励',
  `mobile` varchar(299) DEFAULT NULL,
  `bankCard` varchar(299) DEFAULT NULL,
  `bankName` varchar(299) DEFAULT NULL,
  `moneys` decimal(10,2) NOT NULL COMMENT '真实上分金额',
  `sxf` decimal(10,2) DEFAULT NULL,
  `orderNo` varchar(299) NOT NULL COMMENT '订单号'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='上分审核管理' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_ems`
--

CREATE TABLE `fa_ems` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `event` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '事件',
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '邮箱',
  `code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '验证码',
  `times` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '验证次数',
  `ip` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'IP',
  `createtime` bigint(20) DEFAULT NULL COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='邮箱验证码表' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_event`
--

CREATE TABLE `fa_event` (
  `id` int(11) NOT NULL,
  `link_id` varchar(30) DEFAULT NULL,
  `pixel_id` varchar(30) DEFAULT NULL,
  `event_name` varchar(30) DEFAULT NULL,
  `result` varchar(2000) DEFAULT NULL,
  `msg` varchar(2000) DEFAULT NULL,
  `UA` varchar(2000) DEFAULT NULL,
  `IP` varchar(20) DEFAULT NULL,
  `adddate` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fa_event_err`
--

CREATE TABLE `fa_event_err` (
  `id` int(11) NOT NULL,
  `cookies` varchar(2000) DEFAULT NULL,
  `msg` varchar(2000) DEFAULT NULL,
  `UA` varchar(2000) DEFAULT NULL,
  `IP` varchar(30) DEFAULT NULL,
  `adddate` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fa_gamelist`
--

CREATE TABLE `fa_gamelist` (
  `id` int(11) NOT NULL,
  `gameId` int(11) DEFAULT NULL,
  `pg_game_id` varchar(50) NOT NULL,
  `newpg_id` int(11) DEFAULT NULL,
  `label` varchar(299) NOT NULL,
  `type` varchar(11) NOT NULL,
  `order` int(11) NOT NULL,
  `name` varchar(299) NOT NULL,
  `level` int(11) NOT NULL,
  `is_hot` int(11) NOT NULL DEFAULT '0',
  `is_show` int(11) NOT NULL DEFAULT '1' COMMENT '0关闭1开启',
  `game_info` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='三方游戏列表' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_game_record`
--

CREATE TABLE `fa_game_record` (
  `id` int(11) NOT NULL COMMENT '注单ID',
  `siteid` int(11) NOT NULL DEFAULT '0',
  `parent_bet_id` varchar(100) DEFAULT NULL,
  `bet_id` varchar(100) DEFAULT NULL,
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `game_id` int(11) DEFAULT NULL,
  `game_type_name` varchar(20) DEFAULT NULL COMMENT '游戏名称',
  `bet_amount` decimal(20,4) DEFAULT NULL COMMENT '投注额',
  `valid_bet_amount` decimal(20,4) DEFAULT NULL COMMENT '有效投注额',
  `net_amount` decimal(20,4) DEFAULT NULL COMMENT '输赢金额',
  `pumping_amount` decimal(20,4) DEFAULT NULL COMMENT '抽水',
  `currency` varchar(10) DEFAULT NULL COMMENT '币种',
  `create_at` varchar(100) DEFAULT NULL COMMENT '开始时间=当前该注单的投注时间 或该注单创建时间',
  `net_at` varchar(100) DEFAULT NULL COMMENT '结束时间=当前注单被结算的时间',
  `is_fl` int(11) NOT NULL DEFAULT '0' COMMENT '是否反水',
  `trace_id` varchar(100) DEFAULT NULL,
  `info` text,
  `transaction_id` varchar(100) DEFAULT NULL,
  `balance_amount` decimal(20,2) DEFAULT NULL,
  `apiname` varchar(50) DEFAULT NULL,
  `resultType` varchar(20) DEFAULT NULL,
  `rollback` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='投注记录管理' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_invite_record`
--

CREATE TABLE `fa_invite_record` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `recharge_money` decimal(20,2) NOT NULL DEFAULT '0.00',
  `is_first` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `do_time` int(11) NOT NULL,
  `up_invite_money` decimal(20,2) NOT NULL,
  `up1_recharge_money` decimal(20,2) NOT NULL,
  `up1_user_id` int(11) NOT NULL DEFAULT '0',
  `up2_recharge_money` decimal(20,2) NOT NULL,
  `up2_user_id` int(11) NOT NULL DEFAULT '0',
  `up3_recharge_money` decimal(20,2) NOT NULL,
  `up3_user_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fa_kaika`
--

CREATE TABLE `fa_kaika` (
  `id` int(11) NOT NULL,
  `phone` varchar(255) NOT NULL COMMENT '手机号',
  `katype` varchar(255) NOT NULL COMMENT '卡类型',
  `kahao` varchar(255) NOT NULL COMMENT '卡号',
  `chika` varchar(255) NOT NULL COMMENT '持卡人',
  `endu` varchar(255) DEFAULT NULL COMMENT '信用额度',
  `endu2` varchar(255) NOT NULL COMMENT '信用额度2',
  `zdr` varchar(255) NOT NULL COMMENT '账单日',
  `yijiao` varchar(255) NOT NULL COMMENT '溢缴款支取账户',
  `banli` varchar(255) NOT NULL COMMENT '办理进度',
  `banli2` varchar(255) NOT NULL COMMENT '办理进度2',
  `image` varchar(255) NOT NULL COMMENT '银行logo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='数据录入管理' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_lb`
--

CREATE TABLE `fa_lb` (
  `id` int(11) NOT NULL,
  `name` varchar(299) NOT NULL COMMENT '标题',
  `image` varchar(999) NOT NULL COMMENT '轮播图',
  `url` varchar(1000) DEFAULT NULL,
  `url_tab` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='轮播管理' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_msg`
--

CREATE TABLE `fa_msg` (
  `id` int(11) NOT NULL,
  `title` varchar(299) NOT NULL COMMENT '标题',
  `subtitle` varchar(299) NOT NULL COMMENT '简介',
  `content` text NOT NULL COMMENT '内容',
  `addtime` varchar(299) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='通知' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_mx`
--

CREATE TABLE `fa_mx` (
  `id` int(11) NOT NULL,
  `info` varchar(999) NOT NULL COMMENT '信息',
  `type` int(11) NOT NULL COMMENT '0：充值 1：提现  2：提现冻结 3：充值奖励  4：升级奖励  5：邀请奖励   6：后台博主打款  7：提现解冻 8：游戏扣费  9：游戏奖励 10：签到奖励',
  `amount` decimal(20,2) DEFAULT '0.00' COMMENT '操作金额',
  `money` decimal(20,2) NOT NULL COMMENT '用户余额',
  `before_money` decimal(20,2) DEFAULT NULL COMMENT '变动前余额',
  `addtime` varchar(299) NOT NULL COMMENT '添加时间',
  `uid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='明细管理' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_order`
--

CREATE TABLE `fa_order` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `orderNo` varchar(20) NOT NULL,
  `orderType` int(11) NOT NULL DEFAULT '0',
  `currency` varchar(11) NOT NULL,
  `amount` decimal(20,2) NOT NULL,
  `rate` decimal(4,2) NOT NULL DEFAULT '0.00',
  `fee` int(11) NOT NULL DEFAULT '0',
  `returnUrl` varchar(100) DEFAULT NULL,
  `notifyUrl` varchar(100) NOT NULL,
  `sign` varchar(100) NOT NULL,
  `pay_stauts` int(11) NOT NULL DEFAULT '0',
  `checkInvite` int(11) NOT NULL DEFAULT '0',
  `dotop_id` int(11) NOT NULL,
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `is_frist` int(11) DEFAULT '0',
  `is_yf` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fa_order_notify`
--

CREATE TABLE `fa_order_notify` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `notify_msg` text NOT NULL,
  `check_stauts` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `orderNo` varchar(50) NOT NULL,
  `orderStatus` int(11) NOT NULL,
  `amount` decimal(20,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fa_prize`
--

CREATE TABLE `fa_prize` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `specifications_rom` varchar(200) NOT NULL,
  `specifications_color` varchar(200) NOT NULL,
  `transports` varchar(100) NOT NULL,
  `img` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fa_prize_log`
--

CREATE TABLE `fa_prize_log` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `prize_id` int(11) NOT NULL,
  `state` int(11) NOT NULL DEFAULT '0',
  `date` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fa_prize_transports`
--

CREATE TABLE `fa_prize_transports` (
  `id` int(11) NOT NULL,
  `type` varchar(200) NOT NULL,
  `days` int(11) NOT NULL,
  `money` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fa_rechlist`
--

CREATE TABLE `fa_rechlist` (
  `id` int(11) NOT NULL,
  `amount_min` decimal(10,2) NOT NULL COMMENT '金额',
  `amount_max` decimal(10,2) NOT NULL,
  `rate` decimal(6,4) DEFAULT NULL,
  `bonus_finish` int(11) DEFAULT NULL,
  `bonus_rate` decimal(6,4) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `start_at` int(11) DEFAULT NULL,
  `end_at` int(11) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `total_rebate` varchar(10) DEFAULT NULL,
  `buyTimes` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='充值列表配置' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_shipping_address`
--

CREATE TABLE `fa_shipping_address` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `phone` varchar(200) DEFAULT NULL,
  `zip_code` varchar(20) DEFAULT NULL,
  `state` varchar(200) DEFAULT NULL,
  `city` varchar(200) DEFAULT NULL,
  `street` varchar(200) DEFAULT NULL,
  `number` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fa_sign`
--

CREATE TABLE `fa_sign` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `days` int(11) NOT NULL,
  `bonus` decimal(11,2) NOT NULL,
  `bonus_time` int(11) NOT NULL,
  `draw_time` int(11) NOT NULL,
  `sign_stauts` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fa_sms`
--

CREATE TABLE `fa_sms` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `event` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '事件',
  `mobile` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '手机号',
  `code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '验证码',
  `times` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '验证次数',
  `ip` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'IP',
  `createtime` bigint(20) UNSIGNED DEFAULT '0' COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='短信验证码表' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_totals`
--

CREATE TABLE `fa_totals` (
  `id` int(11) NOT NULL,
  `datestr` varchar(20) NOT NULL COMMENT '日期',
  `regNums` int(11) DEFAULT '0' COMMENT '注册人数',
  `rechargeAll` decimal(20,2) DEFAULT '0.00' COMMENT '总充值金额',
  `profit` decimal(20,2) DEFAULT '0.00' COMMENT '利润',
  `rechargeFristNums` int(11) DEFAULT '0' COMMENT '新客人数',
  `rechargeFristMoney` decimal(20,2) DEFAULT '0.00' COMMENT '充值金额',
  `rechargeFristARPPU` decimal(4,2) DEFAULT '0.00' COMMENT 'ARPPU',
  `rechargeSecNums` int(11) DEFAULT '0' COMMENT '老客人数',
  `rechargeSecMoney` decimal(20,2) DEFAULT '0.00' COMMENT '充值金额',
  `rechargeSecARPPU` decimal(4,2) DEFAULT '0.00' COMMENT 'ARPPU',
  `rechargeProcess` decimal(4,2) DEFAULT '0.00' COMMENT '当日新客充值率',
  `withdrawUserAll` decimal(20,2) DEFAULT '0.00' COMMENT '提现人数',
  `withdrawUserFri` decimal(20,2) DEFAULT '0.00' COMMENT '提现新客人数',
  `withdrawUserSec` decimal(20,2) DEFAULT '0.00' COMMENT '提现老客人数',
  `withdrawMoneyAll` decimal(20,2) DEFAULT '0.00' COMMENT '提现金额',
  `withdrawMoneyFri` decimal(20,2) DEFAULT '0.00' COMMENT '新客提现金额',
  `withdrawMoneySec` decimal(20,2) DEFAULT '0.00' COMMENT '老客提现金额',
  `withdrawNumsAll` int(11) DEFAULT '0' COMMENT '提现单数',
  `withdrawNumsFri` int(11) DEFAULT '0' COMMENT '新客提现单数',
  `withdrawNumsSec` int(11) DEFAULT '0' COMMENT '老客提现单数',
  `withdrawAverageAll` decimal(20,2) DEFAULT '0.00' COMMENT '提现平均值',
  `withdrawAverageFri` decimal(20,2) DEFAULT '0.00' COMMENT '新客提现平均值',
  `withdrawAverageSec` decimal(20,2) DEFAULT '0.00' COMMENT '老客提现平均值',
  `withdrawFee` decimal(20,2) DEFAULT '0.00' COMMENT '提现手续费',
  `dazheNums` int(11) DEFAULT '0' COMMENT '打折人数',
  `bzdakuan` decimal(20,2) DEFAULT '0.00' COMMENT '博主钱包打款',
  `gameFee` decimal(20,2) DEFAULT '0.00' COMMENT '接线抽成',
  `fristReward` decimal(20,2) DEFAULT '0.00' COMMENT '首充总赠送',
  `secReward` decimal(20,2) DEFAULT '0.00' COMMENT '次充总赠送',
  `signReward` decimal(20,2) DEFAULT '0.00' COMMENT '签到总赠送',
  `uplevelReward` decimal(20,2) DEFAULT '0.00' COMMENT '升级总赠送',
  `chargeReward` decimal(20,2) DEFAULT '0.00' COMMENT '充值流水赠送'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fa_user`
--

CREATE TABLE `fa_user` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `pid` int(11) DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0' COMMENT '组别ID',
  `username` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '用户名',
  `nickname` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '昵称',
  `password` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '密码',
  `salt` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '密码盐',
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '电子邮箱',
  `mobile` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '手机号',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '头像',
  `level` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '等级',
  `gender` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '性别',
  `birthday` date DEFAULT NULL COMMENT '生日',
  `bio` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '格言',
  `money` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '余额',
  `money_true` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '冻结金额',
  `withdrawAmount` decimal(20,2) DEFAULT '0.00' COMMENT '提现总额',
  `rechargeAmount` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '充值总额',
  `rechargeAmountFrist` decimal(20,2) DEFAULT '0.00' COMMENT '首充金额',
  `inviteAmount` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '奖励金额',
  `tuiAmount` decimal(20,2) DEFAULT '0.00',
  `score` decimal(20,2) DEFAULT '0.00' COMMENT '流水总额',
  `score_wl` decimal(20,2) DEFAULT '0.00' COMMENT '游戏输赢',
  `score_bigwin` decimal(10,0) DEFAULT '0',
  `choushui` decimal(20,2) DEFAULT '0.00' COMMENT '抽水金额累计',
  `chou_fee` decimal(6,4) DEFAULT '0.0000',
  `mb_max_count` int(11) NOT NULL DEFAULT '0',
  `mb_count` int(11) DEFAULT '0',
  `mb_lastmoney` decimal(10,2) DEFAULT '0.00',
  `mb_totals` decimal(20,2) DEFAULT '0.00',
  `user_rtp` int(11) DEFAULT '0',
  `dama` int(11) DEFAULT '0',
  `dazhe` decimal(4,2) DEFAULT '0.00',
  `invite_hope` int(11) DEFAULT '0' COMMENT '期望推荐人数',
  `invite_nums1` int(11) NOT NULL DEFAULT '0' COMMENT '1级推荐人数',
  `invite_nums2` int(11) NOT NULL DEFAULT '0' COMMENT '2级推荐人数',
  `invite_nums3` int(11) NOT NULL DEFAULT '0' COMMENT '3级推荐人数',
  `invite_frist_recharge` decimal(20,2) DEFAULT '0.00',
  `invite_money1` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '1级推荐充值总额',
  `invite_money2` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '2级推荐充值总额',
  `invite_money3` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '3级推荐充值总额',
  `invite_reward1` decimal(20,4) NOT NULL DEFAULT '0.0000' COMMENT '1级推荐奖励总额',
  `invite_reward2` decimal(20,4) NOT NULL DEFAULT '0.0000' COMMENT '2级推荐奖励总额',
  `invite_reward3` decimal(20,4) NOT NULL DEFAULT '0.0000' COMMENT '3级推荐奖励总额',
  `successions` int(10) UNSIGNED NOT NULL DEFAULT '1' COMMENT '连续登录天数',
  `maxsuccessions` int(10) UNSIGNED NOT NULL DEFAULT '1' COMMENT '最大连续登录天数',
  `prevtime` bigint(20) DEFAULT NULL COMMENT '上次登录时间',
  `logintime` bigint(20) DEFAULT NULL COMMENT '登录时间',
  `loginip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '登录IP',
  `loginfailure` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '失败次数',
  `joinip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '加入IP',
  `jointime` bigint(20) DEFAULT NULL COMMENT '加入时间',
  `createtime` bigint(20) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(20) DEFAULT NULL COMMENT '更新时间',
  `token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'Token',
  `uuid` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '状态',
  `verification` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '验证',
  `stat` int(11) NOT NULL,
  `customerCert` varchar(299) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '验证号码',
  `customerName` varchar(299) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '付款人姓名',
  `accountNum` varchar(299) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'PIX账户',
  `accountType` varchar(299) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'PIX账户类型',
  `gfs` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pay_account_id` int(11) NOT NULL DEFAULT '0',
  `rechargeStatus` int(11) NOT NULL DEFAULT '0',
  `withdrawStatus` int(11) NOT NULL DEFAULT '0',
  `teleStatus` int(11) NOT NULL DEFAULT '0',
  `signInRefreshTimestamp` int(11) NOT NULL DEFAULT '0',
  `prizeRefreshTimestamp` int(11) DEFAULT NULL,
  `bak` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `end_data` int(11) DEFAULT '0',
  `trial_status` int(11) DEFAULT '0',
  `sha_status` int(11) DEFAULT '0',
  `sec_stauts` int(11) DEFAULT '0',
  `link_id` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `pixel_id` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `url` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lottery_chance` int(11) DEFAULT '3',
  `sms_flag` int(11) NOT NULL DEFAULT '0',
  `googleid` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `num1_fs` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `num2_fs` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `num3_fs` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `chest_invited` int(11) NOT NULL DEFAULT '0' COMMENT '宝箱活动作为邀请人成功推荐人数',
  `chest_rewarded` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '宝箱活动作为邀请人获取到的奖励金额',
  `chest_status` int(11) NOT NULL DEFAULT '0' COMMENT '宝箱活动作为被邀请人的宝箱活动状态：0: 非被邀请人，1:未达标的被邀请人，2: 已达标的被邀请人'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='会员表' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_users`
--

CREATE TABLE `fa_users` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `pid` int(11) DEFAULT '0',
  `group_id` int(10) UNSIGNED DEFAULT '0' COMMENT '组别ID',
  `username` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '用户名',
  `nickname` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '昵称',
  `password` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '密码',
  `salt` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '密码盐',
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '电子邮箱',
  `mobile` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '手机号',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '头像',
  `level` tinyint(3) UNSIGNED DEFAULT '0' COMMENT '等级',
  `gender` tinyint(3) UNSIGNED DEFAULT '0' COMMENT '性别',
  `birthday` date DEFAULT NULL COMMENT '生日',
  `bio` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '格言',
  `money` decimal(10,2) DEFAULT '0.00' COMMENT '余额',
  `score` int(11) DEFAULT '0' COMMENT '积分',
  `successions` int(10) UNSIGNED DEFAULT '1' COMMENT '连续登录天数',
  `maxsuccessions` int(10) UNSIGNED DEFAULT '1' COMMENT '最大连续登录天数',
  `prevtime` bigint(20) DEFAULT NULL COMMENT '上次登录时间',
  `logintime` bigint(20) DEFAULT NULL COMMENT '登录时间',
  `loginip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '登录IP',
  `loginfailure` tinyint(3) UNSIGNED DEFAULT '0' COMMENT '失败次数',
  `joinip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '加入IP',
  `jointime` bigint(20) DEFAULT NULL COMMENT '加入时间',
  `createtime` bigint(20) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(20) DEFAULT NULL COMMENT '更新时间',
  `token` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'Token',
  `status` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '状态',
  `verification` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '验证'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='会员表' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_user_chests`
--

CREATE TABLE `fa_user_chests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `chests_id` int(11) NOT NULL,
  `reward` decimal(10,2) NOT NULL,
  `created` int(64) NOT NULL COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户宝箱领取记录';

-- --------------------------------------------------------

--
-- Table structure for table `fa_user_group`
--

CREATE TABLE `fa_user_group` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '组名',
  `rules` text COLLATE utf8mb4_unicode_ci COMMENT '权限节点',
  `createtime` bigint(20) DEFAULT NULL COMMENT '添加时间',
  `updatetime` bigint(20) DEFAULT NULL COMMENT '更新时间',
  `status` enum('normal','hidden') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='会员组表' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_user_lottery_log`
--

CREATE TABLE `fa_user_lottery_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `lottery_id` int(11) NOT NULL,
  `draws_time` int(11) NOT NULL,
  `bonus` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fa_user_lottery_record`
--

CREATE TABLE `fa_user_lottery_record` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `invite_count` int(11) NOT NULL COMMENT '邀请人数',
  `invite_count_total` int(11) NOT NULL COMMENT '邀请人数上限',
  `invite_recharge_count` int(11) NOT NULL COMMENT '邀请充值次数',
  `invite_recharge_total` int(11) NOT NULL COMMENT '充值次数上限',
  `lottery_count` int(11) NOT NULL COMMENT '已经使用的抽奖次数',
  `total_winnings` decimal(10,2) NOT NULL COMMENT '最高中奖次数',
  `flowing_water` decimal(10,2) NOT NULL COMMENT '流水金额',
  `current_activity_start` int(11) NOT NULL COMMENT '活动开始时间',
  `current_activity_end` int(11) NOT NULL COMMENT '活动结束时间',
  `completed_tasks` int(11) NOT NULL COMMENT '是否完成任务',
  `total_draws` int(11) NOT NULL COMMENT '总需抽奖次数',
  `max_reward_per_draw` decimal(10,2) NOT NULL COMMENT '每次抽奖最大值',
  `min_reward_per_draw` decimal(10,2) NOT NULL COMMENT '每次抽奖最小值',
  `lottery_threshold` decimal(10,2) NOT NULL COMMENT '起抽金额阈值',
  `reward_multiplier` decimal(10,2) NOT NULL COMMENT '奖金流水倍数',
  `max_reward_first_draw` decimal(10,2) NOT NULL COMMENT '首抽最大金额',
  `min_reward_first_draw` decimal(10,2) NOT NULL COMMENT '首抽最小金额',
  `pool_amount` decimal(10,2) NOT NULL COMMENT '奖池奖金总数',
  `current_amount` decimal(10,2) NOT NULL COMMENT '当前剩余金额'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fa_user_money_log`
--

CREATE TABLE `fa_user_money_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '会员ID',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变更余额',
  `before` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变更前余额',
  `after` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变更后余额',
  `memo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '备注',
  `createtime` bigint(20) DEFAULT NULL COMMENT '创建时间',
  `type` int(11) NOT NULL COMMENT '类型'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='会员余额变动表' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_user_rule`
--

CREATE TABLE `fa_user_rule` (
  `id` int(10) UNSIGNED NOT NULL,
  `pid` int(11) DEFAULT NULL COMMENT '父ID',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '名称',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '标题',
  `remark` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  `ismenu` tinyint(1) DEFAULT NULL COMMENT '是否菜单',
  `createtime` bigint(20) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(20) DEFAULT NULL COMMENT '更新时间',
  `weigh` int(11) DEFAULT '0' COMMENT '权重',
  `status` enum('normal','hidden') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='会员规则表' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_user_score_log`
--

CREATE TABLE `fa_user_score_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '会员ID',
  `score` int(11) NOT NULL DEFAULT '0' COMMENT '变更积分',
  `before` int(11) NOT NULL DEFAULT '0' COMMENT '变更前积分',
  `after` int(11) NOT NULL DEFAULT '0' COMMENT '变更后积分',
  `memo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '备注',
  `createtime` bigint(20) DEFAULT NULL COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='会员积分变动表' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_user_token`
--

CREATE TABLE `fa_user_token` (
  `token` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Token',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '会员ID',
  `createtime` bigint(20) DEFAULT NULL COMMENT '创建时间',
  `expiretime` bigint(20) DEFAULT NULL COMMENT '过期时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='会员Token表' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_user_totals`
--

CREATE TABLE `fa_user_totals` (
  `id` int(11) NOT NULL,
  `datestr` varchar(20) NOT NULL COMMENT '日期',
  `user_id` int(11) NOT NULL COMMENT '会员id',
  `reward` decimal(20,2) DEFAULT '0.00' COMMENT '总奖励',
  `reward1` decimal(20,2) DEFAULT '0.00' COMMENT '1级奖励',
  `reward2` decimal(20,2) DEFAULT '0.00' COMMENT '2级奖励',
  `reward3` decimal(20,2) DEFAULT '0.00' COMMENT '3级奖励',
  `num1Recharge` int(11) DEFAULT '0' COMMENT '有效注册',
  `num2Recharge` int(11) DEFAULT '0',
  `num3Recharge` int(11) DEFAULT '0',
  `firstRechargeReward` decimal(20,2) DEFAULT '0.00' COMMENT '首充奖励',
  `flow` decimal(20,2) DEFAULT '0.00' COMMENT '充值金额',
  `flow1` decimal(20,2) DEFAULT '0.00' COMMENT '1级充值金额',
  `flow2` decimal(20,2) DEFAULT '0.00' COMMENT '2级充值金额',
  `flow3` decimal(20,2) DEFAULT '0.00' COMMENT '3级充值金额',
  `flowReward` decimal(20,4) DEFAULT '0.0000' COMMENT '充值奖励',
  `flowReward1` decimal(20,4) DEFAULT '0.0000' COMMENT '1级充值奖励',
  `flowReward2` decimal(20,4) DEFAULT '0.0000' COMMENT '2级充值奖励',
  `flowReward3` decimal(20,4) DEFAULT '0.0000' COMMENT '3级充值奖励',
  `updatetime` int(11) DEFAULT '0' COMMENT '更新时间',
  `dazhe` decimal(4,2) DEFAULT '0.00' COMMENT '打折',
  `zhe_num1Recharge` int(11) DEFAULT '0',
  `zhe_firstRechargeReward` decimal(20,2) DEFAULT '0.00',
  `is_set` int(11) DEFAULT '0' COMMENT '0：未更新  1：已更新',
  `add_time` int(11) DEFAULT NULL,
  `up_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fa_version`
--

CREATE TABLE `fa_version` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `oldversion` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '旧版本号',
  `newversion` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '新版本号',
  `packagesize` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '包大小',
  `content` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '升级内容',
  `downloadurl` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '下载地址',
  `enforce` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '强制更新',
  `createtime` bigint(20) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(20) DEFAULT NULL COMMENT '更新时间',
  `weigh` int(11) NOT NULL DEFAULT '0' COMMENT '权重',
  `status` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='版本表' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_vips`
--

CREATE TABLE `fa_vips` (
  `id` int(11) NOT NULL,
  `name` varchar(299) NOT NULL COMMENT '名称',
  `running_water` decimal(20,2) NOT NULL COMMENT '需达流水',
  `running_money` decimal(20,2) NOT NULL COMMENT '需达充值金额',
  `true_water` int(11) DEFAULT '0',
  `true_money` int(11) DEFAULT '0',
  `withdraw` decimal(10,2) NOT NULL COMMENT '提现手续',
  `day_withdraw` int(11) NOT NULL COMMENT '每日提现次数',
  `withdraw_limt` decimal(20,2) NOT NULL COMMENT '提现每日限额',
  `week` decimal(10,2) NOT NULL COMMENT '周奖励',
  `moon` decimal(10,2) NOT NULL COMMENT '月奖励',
  `vip_up` decimal(20,2) NOT NULL COMMENT '会员升级奖励',
  `single_withdraw` decimal(10,2) NOT NULL COMMENT '提现单笔限额',
  `level` int(11) NOT NULL COMMENT '实际等级',
  `image` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='vip管理' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `fa_volist`
--

CREATE TABLE `fa_volist` (
  `id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `receiveFlag` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fa_ac`
--
ALTER TABLE `fa_ac`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `fa_activity_config`
--
ALTER TABLE `fa_activity_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fa_admin`
--
ALTER TABLE `fa_admin`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `user_id` (`user_id`) USING BTREE,
  ADD UNIQUE KEY `username` (`username`) USING BTREE;

--
-- Indexes for table `fa_admin_log`
--
ALTER TABLE `fa_admin_log`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `name` (`username`) USING BTREE;

--
-- Indexes for table `fa_area`
--
ALTER TABLE `fa_area`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `pid` (`pid`) USING BTREE;

--
-- Indexes for table `fa_attachment`
--
ALTER TABLE `fa_attachment`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `fa_auth_group`
--
ALTER TABLE `fa_auth_group`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `fa_auth_group_access`
--
ALTER TABLE `fa_auth_group_access`
  ADD UNIQUE KEY `uid_group_id` (`uid`,`group_id`) USING BTREE,
  ADD KEY `uid` (`uid`) USING BTREE,
  ADD KEY `group_id` (`group_id`) USING BTREE;

--
-- Indexes for table `fa_auth_rule`
--
ALTER TABLE `fa_auth_rule`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `name` (`name`) USING BTREE,
  ADD KEY `pid` (`pid`) USING BTREE,
  ADD KEY `weigh` (`weigh`) USING BTREE;

--
-- Indexes for table `fa_bank`
--
ALTER TABLE `fa_bank`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `fa_category`
--
ALTER TABLE `fa_category`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `weigh` (`weigh`,`id`) USING BTREE,
  ADD KEY `pid` (`pid`) USING BTREE;

--
-- Indexes for table `fa_ce`
--
ALTER TABLE `fa_ce`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `fa_chests`
--
ALTER TABLE `fa_chests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status` (`status`,`limits`);

--
-- Indexes for table `fa_command`
--
ALTER TABLE `fa_command`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `fa_config`
--
ALTER TABLE `fa_config`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `name` (`name`) USING BTREE;

--
-- Indexes for table `fa_contents`
--
ALTER TABLE `fa_contents`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `fa_conts`
--
ALTER TABLE `fa_conts`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `fa_convert`
--
ALTER TABLE `fa_convert`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `fa_dotop`
--
ALTER TABLE `fa_dotop`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `orderNo` (`orderNo`);

--
-- Indexes for table `fa_dotops`
--
ALTER TABLE `fa_dotops`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `fa_ems`
--
ALTER TABLE `fa_ems`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `fa_event`
--
ALTER TABLE `fa_event`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fa_event_err`
--
ALTER TABLE `fa_event_err`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fa_gamelist`
--
ALTER TABLE `fa_gamelist`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `fa_game_record`
--
ALTER TABLE `fa_game_record`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `id` (`id`) USING BTREE,
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `bet_id` (`bet_id`),
  ADD KEY `uid` (`uid`) USING BTREE;

--
-- Indexes for table `fa_invite_record`
--
ALTER TABLE `fa_invite_record`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `fa_kaika`
--
ALTER TABLE `fa_kaika`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `fa_lb`
--
ALTER TABLE `fa_lb`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `fa_msg`
--
ALTER TABLE `fa_msg`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `fa_mx`
--
ALTER TABLE `fa_mx`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `fa_order`
--
ALTER TABLE `fa_order`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `id_2` (`id`),
  ADD KEY `orderNo` (`orderNo`),
  ADD KEY `user_id` (`user_id`) USING BTREE;

--
-- Indexes for table `fa_order_notify`
--
ALTER TABLE `fa_order_notify`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fa_prize`
--
ALTER TABLE `fa_prize`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fa_prize_log`
--
ALTER TABLE `fa_prize_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fa_prize_transports`
--
ALTER TABLE `fa_prize_transports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fa_rechlist`
--
ALTER TABLE `fa_rechlist`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `fa_shipping_address`
--
ALTER TABLE `fa_shipping_address`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fa_sign`
--
ALTER TABLE `fa_sign`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fa_sms`
--
ALTER TABLE `fa_sms`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `fa_totals`
--
ALTER TABLE `fa_totals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fa_user`
--
ALTER TABLE `fa_user`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `email` (`email`) USING BTREE,
  ADD KEY `mobile` (`mobile`) USING BTREE,
  ADD KEY `id` (`id`),
  ADD KEY `token` (`token`),
  ADD KEY `uuid` (`uuid`),
  ADD KEY `gfs` (`gfs`),
  ADD KEY `chest_status` (`chest_status`);

--
-- Indexes for table `fa_users`
--
ALTER TABLE `fa_users`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `username` (`username`) USING BTREE,
  ADD KEY `email` (`email`) USING BTREE,
  ADD KEY `mobile` (`mobile`) USING BTREE;

--
-- Indexes for table `fa_user_chests`
--
ALTER TABLE `fa_user_chests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `chests_id` (`chests_id`);

--
-- Indexes for table `fa_user_group`
--
ALTER TABLE `fa_user_group`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `fa_user_lottery_log`
--
ALTER TABLE `fa_user_lottery_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fa_user_lottery_record`
--
ALTER TABLE `fa_user_lottery_record`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fa_user_money_log`
--
ALTER TABLE `fa_user_money_log`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `fa_user_rule`
--
ALTER TABLE `fa_user_rule`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `fa_user_score_log`
--
ALTER TABLE `fa_user_score_log`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `fa_user_token`
--
ALTER TABLE `fa_user_token`
  ADD PRIMARY KEY (`token`) USING BTREE;

--
-- Indexes for table `fa_user_totals`
--
ALTER TABLE `fa_user_totals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fa_version`
--
ALTER TABLE `fa_version`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `fa_vips`
--
ALTER TABLE `fa_vips`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `fa_volist`
--
ALTER TABLE `fa_volist`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `fa_ac`
--
ALTER TABLE `fa_ac`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_activity_config`
--
ALTER TABLE `fa_activity_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_admin`
--
ALTER TABLE `fa_admin`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- AUTO_INCREMENT for table `fa_admin_log`
--
ALTER TABLE `fa_admin_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- AUTO_INCREMENT for table `fa_area`
--
ALTER TABLE `fa_area`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- AUTO_INCREMENT for table `fa_attachment`
--
ALTER TABLE `fa_attachment`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- AUTO_INCREMENT for table `fa_auth_group`
--
ALTER TABLE `fa_auth_group`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_auth_rule`
--
ALTER TABLE `fa_auth_rule`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_bank`
--
ALTER TABLE `fa_bank`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_category`
--
ALTER TABLE `fa_category`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_ce`
--
ALTER TABLE `fa_ce`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_chests`
--
ALTER TABLE `fa_chests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_command`
--
ALTER TABLE `fa_command`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- AUTO_INCREMENT for table `fa_config`
--
ALTER TABLE `fa_config`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_contents`
--
ALTER TABLE `fa_contents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_conts`
--
ALTER TABLE `fa_conts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_convert`
--
ALTER TABLE `fa_convert`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_dotop`
--
ALTER TABLE `fa_dotop`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_dotops`
--
ALTER TABLE `fa_dotops`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_ems`
--
ALTER TABLE `fa_ems`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- AUTO_INCREMENT for table `fa_event`
--
ALTER TABLE `fa_event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_event_err`
--
ALTER TABLE `fa_event_err`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_gamelist`
--
ALTER TABLE `fa_gamelist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_game_record`
--
ALTER TABLE `fa_game_record`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '注单ID';

--
-- AUTO_INCREMENT for table `fa_invite_record`
--
ALTER TABLE `fa_invite_record`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_kaika`
--
ALTER TABLE `fa_kaika`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_lb`
--
ALTER TABLE `fa_lb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_msg`
--
ALTER TABLE `fa_msg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_mx`
--
ALTER TABLE `fa_mx`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_order`
--
ALTER TABLE `fa_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_order_notify`
--
ALTER TABLE `fa_order_notify`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_prize`
--
ALTER TABLE `fa_prize`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_prize_log`
--
ALTER TABLE `fa_prize_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_prize_transports`
--
ALTER TABLE `fa_prize_transports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_rechlist`
--
ALTER TABLE `fa_rechlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_shipping_address`
--
ALTER TABLE `fa_shipping_address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_sign`
--
ALTER TABLE `fa_sign`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_sms`
--
ALTER TABLE `fa_sms`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- AUTO_INCREMENT for table `fa_totals`
--
ALTER TABLE `fa_totals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_user`
--
ALTER TABLE `fa_user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- AUTO_INCREMENT for table `fa_users`
--
ALTER TABLE `fa_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- AUTO_INCREMENT for table `fa_user_chests`
--
ALTER TABLE `fa_user_chests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_user_group`
--
ALTER TABLE `fa_user_group`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_user_lottery_log`
--
ALTER TABLE `fa_user_lottery_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_user_lottery_record`
--
ALTER TABLE `fa_user_lottery_record`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_user_money_log`
--
ALTER TABLE `fa_user_money_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_user_rule`
--
ALTER TABLE `fa_user_rule`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_user_score_log`
--
ALTER TABLE `fa_user_score_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_user_totals`
--
ALTER TABLE `fa_user_totals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_version`
--
ALTER TABLE `fa_version`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- AUTO_INCREMENT for table `fa_vips`
--
ALTER TABLE `fa_vips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fa_volist`
--
ALTER TABLE `fa_volist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
