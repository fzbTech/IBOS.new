<?php

/**
 * 移动端默认控制器文件
 *
 * @author Aeolus <Aeolus@ibos.com.cn>
 * @link http://www.ibos.com.cn/
 * @copyright Copyright &copy; 2012-2013 IBOS Inc
 */
/**
 * 移动端模块默认控制器类
 *
 * @package application.modules.mobile.controllers
 * @author Aeolus <Aeolus@ibos.com.cn>
 * @version $Id$
 */

namespace application\modules\mobile\controllers;

use application\core\model\Log;
use application\core\utils\Cloud;
use application\core\utils\Env;
use application\core\utils\Ibos;
use application\core\utils\Module;
use application\core\utils\StringUtil;
use application\core\utils\WebSite;
use application\modules\department\utils\Department as DeptUtils;
use application\modules\main\utils\Main;
use application\modules\main\model\Setting;
use application\modules\message\model\UserData;
use application\modules\mobile\utils\Mobile;
use application\modules\position\utils\Position as PositionUtils;
use application\modules\user\components\UserIdentity;
use application\modules\user\model\User;
use application\modules\user\model\UserBinding;
use application\modules\user\utils\User as UserUtil;

class DefaultController extends BaseController
{

    /**
     * 登陆成功后返回的数据
     * @return array
     */
    private function getLoginReturns()
    {
        $return = array(
            'login' => true,
            'formhash' => FORMHASH,
            'uid' => Ibos::app()->user->uid,
            'user' => User::model()->fetchByUid(Ibos::app()->user->uid),
            'APPID' => Ibos::app()->setting->get('setting/iboscloud/appid')
        );

        if (Env::getRequest('issetuser') != "true") {
            $userData = UserUtil::getUserByPy(null, false);
            $return['userData'] = $userData;
        }

        if (Module::getIsEnabled('weibo')) {
            $udata = UserData::model()->getUserData();
        }

        $return['user']['following_count'] = isset($udata['following_count']) ? $udata['following_count'] : 0;
        $return['user']['follower_count'] = isset($udata['follower_count']) ? $udata['follower_count'] : 0;
        $return['user']['weibo_count'] = isset($udata['weibo_count']) ? $udata['weibo_count'] : 0;
        $return['departmentData'] = DeptUtils::getDepartmentByPy();
        $return['positionData'] = PositionUtils::getPositionByPy();
        $return['unit'] = StringUtil::utf8Unserialize(Setting::model()->fetchSettingValueByKey('unit'));

        return $return;
    }

    /**
     * 登陆处理
     * @return void
     */
    public function actionLogin()
    {
        if (!Ibos::app()->user->isGuest) {
            $this->ajaxReturn($this->getLoginReturns(), Mobile::dataType());
        }

        $account = Ibos::app()->setting->get('setting/account');
        // 用户名
        $userName = Env::getRequest('username');
        // 密码
        $passWord = Env::getRequest('password');
        $gps = Env::getRequest('gps');
        $address = Env::getRequest('address');
        // 日志
        $ip = Ibos::app()->setting->get('clientip');

        $cookieTime = 0;
        if (!$passWord || $passWord != \CHtml::encode($passWord)) {
            $this->ajaxReturn(array('login' => false, 'msg' => Ibos::lang('Passwd illegal', 'user.default')), Mobile::dataType());
        }
        // 开始验证
        // 登录类型
        if (StringUtil::isMobile($userName)) {
            $loginType = 4;
        } else if (StringUtil::isEmail($userName)) {
            $loginType = 2;
        } else {
            $loginType = 1;
        };
        $identity = new UserIdentity($userName, $passWord, $loginType);
        $result = $identity->authenticate(false);

        if ($result > 0) {
            $user = Ibos::app()->user;
            // 设置会话过期时间
            Main::setCookie('autologin', 1, $cookieTime);
            $user->login($identity, $cookieTime);
            $urlForward = Env::referer();
            $log = array(
                'terminal' => 'app',
                'password' => StringUtil::passwordMask($passWord),
                'ip' => $ip,
                'user' => $userName,
                'loginType' => "username",
                'address' => $address,
                'gps' => $gps
            );
            Log::write($log, 'login', sprintf('module.user.%d', Ibos::app()->user->uid));

            $this->sendLoginNotify();
            $this->ajaxReturn($this->getLoginReturns(), Mobile::dataType());
        } else {
            if ($result === 0) {
                $this->ajaxReturn(array('login' => false, 'msg' => Ibos::lang('User not fount', 'user.default', array('{username}' => $userName))), Mobile::dataType());
            } else if ($result === -1) {
                $this->ajaxReturn(array('login' => false, 'msg' => Ibos::lang('User lock', 'user.default', array('{username}' => $userName))), Mobile::dataType());
            } else if ($result === -2) {
                $this->ajaxReturn(array('login' => false, 'msg' => Ibos::lang('User disabled', '', array('{username}' => $userName))), Mobile::dataType());
            } else if ($result === -3) {
                $log = array(
                    'user' => $userName,
                    'password' => StringUtil::passwordMask($passWord),
                    'ip' => $ip
                );
                Log::write($log, 'illegal', 'module.user.login');
                $this->ajaxReturn(array('login' => false, 'msg' => Ibos::lang('User name or password is not correct', 'user.default')), Mobile::dataType());
            }
        }
    }

    /**
     * 登出操作
     * @return void
     */
    public function actionLogout()
    {
        Ibos::app()->user->logout();
        Main::setCookie('autologin', 0, 0);
        $this->ajaxReturn(array('login' => false), Mobile::dataType());
    }

    /**
     * 默认页,主要用来判断是否登录
     * @return void
     */
    public function actionIndex()
    {
        $access = parent::getAccess();
        if ($access > 0) {
            $this->ajaxReturn(array('login' => true, 'formhash' => FORMHASH, 'uid' => Ibos::app()->user->uid, 'user' => user::model()->fetchByUid(Ibos::app()->user->uid)), Mobile::dataType());
        } else {
            $this->ajaxReturn(array('login' => false, 'msg' => '登录已超时，请重新登录'), Mobile::dataType());
            exit();
        }
    }

    public function actionToken()
    {
        $devtoken = Env::getRequest('devtoken');
        $platform = Env::getRequest('platform');
        $uniqueid = Env::getRequest('uniqueid');
//		app.CLOUDURL + "?s=/api/push/token&type=jsonp&callback=?&appid="+ app.APPID +"&token="+ app.TOKEN +"&uid=" + uid + "&devtoken=" + result + "&platform=ios&uniqueid=";
        $param = array(
            'uid' => Ibos::app()->user->uid,
            'devtoken' => $devtoken,
            'platform' => $platform,
            'uniqueid' => $uniqueid
        );

        $rs = Cloud::getInstance()->fetch('Api/Push/Token', $param, 'post');
        if (!is_array($rs)) {
            $this->ajaxReturn(array('isSucess' => true), Mobile::dataType());
        }
        $this->ajaxReturn(array('isSucess' => false), Mobile::dataType());
    }

    /**
     * 发送登陆消息
     */
    protected function sendLoginNotify()
    {
        $uid = Ibos::app()->user->uid;
        $app = 'wxqy';
        $bdVal = UserBinding::model()->fetchBindValue($uid, $app);
        if (!empty($bdVal)) {
            $corpid = Setting::model()->fetchSettingValueByKey('corpid');
            $msg = '您的账号在' . date('Y年m月d日 H:i:s', TIMESTAMP) . '通过手机端登陆。登陆IP地址为：' . Ibos::app()->setting->get('clientip');
            $param = array(
                'userIds' => array($bdVal),
                'appFlag' => 'helper',
                'var' => array(
                    'message' => $msg,
                ),
                'corpid' => $corpid,
            );
            $route = 'Api/WxPush/push';
            $res = WebSite::getInstance()->fetch($route, json_encode($param), 'post');
        }
    }

}
