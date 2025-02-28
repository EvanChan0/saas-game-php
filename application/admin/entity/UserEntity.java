package app.admin.entity;

import java.util.Date;

import io.swagger.annotations.ApiModel;
import io.swagger.annotations.ApiModelProperty;
import lombok.Data;

@Data
@ApiModel(value = "会员表")
public class UserEntity {

    @ApiModelProperty(value = "ID")
    private Object id;

    private Integer pid;

    @ApiModelProperty(value = "组别ID")
    private Integer groupId;

    @ApiModelProperty(value = "用户名")
    private String username;

    @ApiModelProperty(value = "昵称")
    private String nickname;

    @ApiModelProperty(value = "密码")
    private String password;

    @ApiModelProperty(value = "密码盐")
    private String salt;

    @ApiModelProperty(value = "电子邮箱")
    private String email;

    @ApiModelProperty(value = "手机号")
    private String mobile;

    @ApiModelProperty(value = "头像")
    private String avatar;

    @ApiModelProperty(value = "等级")
    private Object level;

    @ApiModelProperty(value = "性别")
    private Object gender;

    @ApiModelProperty(value = "生日")
    private Date birthday;

    @ApiModelProperty(value = "格言")
    private String bio;

    @ApiModelProperty(value = "余额")
    private Double money;

    @ApiModelProperty(value = "冻结金额")
    private Double moneyTrue;

    @ApiModelProperty(value = "提现总额")
    private Double withdrawamount;

    @ApiModelProperty(value = "充值总额")
    private Double rechargeamount;

    @ApiModelProperty(value = "首充金额")
    private Double rechargeamountfrist;

    @ApiModelProperty(value = "奖励金额")
    private Double inviteamount;

    private Double tuiamount;

    @ApiModelProperty(value = "流水总额")
    private Double score;

    @ApiModelProperty(value = "游戏输赢")
    private Double scoreWl;

    private Object scoreBigwin;

    @ApiModelProperty(value = "抽水金额累计")
    private Double choushui;

    private Double chouFee;

    private Integer mbMaxCount;

    private Integer mbCount;

    private Double mbLastmoney;

    private Double mbTotals;

    private Integer userRtp;

    private Integer dama;

    private Double dazhe;

    @ApiModelProperty(value = "期望推荐人数")
    private Integer inviteHope;

    @ApiModelProperty(value = "1级推荐人数")
    private Integer inviteNums1;

    @ApiModelProperty(value = "2级推荐人数")
    private Integer inviteNums2;

    @ApiModelProperty(value = "3级推荐人数")
    private Integer inviteNums3;

    private Double inviteFristRecharge;

    @ApiModelProperty(value = "1级推荐充值总额")
    private Double inviteMoney1;

    @ApiModelProperty(value = "2级推荐充值总额")
    private Double inviteMoney2;

    @ApiModelProperty(value = "3级推荐充值总额")
    private Double inviteMoney3;

    @ApiModelProperty(value = "1级推荐奖励总额")
    private Double inviteReward1;

    @ApiModelProperty(value = "2级推荐奖励总额")
    private Double inviteReward2;

    @ApiModelProperty(value = "3级推荐奖励总额")
    private Double inviteReward3;

    @ApiModelProperty(value = "连续登录天数")
    private Object successions;

    @ApiModelProperty(value = "最大连续登录天数")
    private Object maxsuccessions;

    @ApiModelProperty(value = "上次登录时间")
    private Long prevtime;

    @ApiModelProperty(value = "登录时间")
    private Long logintime;

    @ApiModelProperty(value = "登录IP")
    private String loginip;

    @ApiModelProperty(value = "失败次数")
    private Object loginfailure;

    @ApiModelProperty(value = "加入IP")
    private String joinip;

    @ApiModelProperty(value = "加入时间")
    private Long jointime;

    @ApiModelProperty(value = "创建时间")
    private Long createtime;

    @ApiModelProperty(value = "更新时间")
    private Long updatetime;

    @ApiModelProperty(value = "Token")
    private String token;

    private String uuid;

    @ApiModelProperty(value = "状态")
    private String status;

    @ApiModelProperty(value = "验证")
    private String verification;

    private Integer stat;

    @ApiModelProperty(value = "验证号码")
    private String customercert;

    @ApiModelProperty(value = "付款人姓名")
    private String customername;

    @ApiModelProperty(value = "PIX账户")
    private String accountnum;

    @ApiModelProperty(value = "PIX账户类型")
    private String accounttype;

    private String gfs;

    private Integer payAccountId;

    private Integer rechargestatus;

    private Integer withdrawstatus;

    private Integer telestatus;

    private Integer signinrefreshtimestamp;

    private Integer prizerefreshtimestamp;

    private String bak;

    private Integer endData;

    private Integer trialStatus;

    private Integer shaStatus;

    private Integer secStauts;

    private String linkId;

    private String pixelId;

    private String url;

    private Integer lotteryChance;

    private Integer smsFlag;

    private String googleid;

    private Double num1Fs;

    private Double num2Fs;

    private Double num3Fs;

    @ApiModelProperty(value = "宝箱活动作为邀请人成功推荐人数")
    private Integer chestInvited;

    @ApiModelProperty(value = "宝箱活动作为邀请人获取到的奖励金额")
    private Double chestRewarded;

    @ApiModelProperty(value = "宝箱活动作为被邀请人的宝箱活动状态：0: 非被邀请人，1:未达标的被邀请人，2: 已达标的被邀请人")
    private Integer chestStatus;


}

