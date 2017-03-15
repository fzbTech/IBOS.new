<?php

use application\core\utils\Ibos;
use application\core\utils\StringUtil;
use application\core\utils\Url;
use application\modules\user\utils\User as UserUtil;

?>
<!doctype html>
</html>
<html lang="en">
    <head>
        <meta charset="<?php echo CHARSET; ?>">
        <title><?php echo Ibos::app()->setting->get('title'); ?></title>
        <link rel="shortcut icon" href="<?php echo STATICURL; ?>/image/favicon.ico?<?php echo VERHASH; ?>">
        <link rel="apple-touch-icon-precomposed" href="<?php echo STATICURL; ?>/image/common/ios_icon.png">
        <meta name="generator" content="IBOS <?php echo VERSION; ?>" />
        <meta name="author" content="IBOS Team" />
        <meta name="copyright" content="2013 IBOS Inc." />
        <!-- IE 8 以下跳转至浏览器升级页 -->
        <!--[if lt IE 8]>
            <script>
                window.location.href = "<?php echo Ibos::app()->urlManager->createUrl("main/default/unsupportedBrowser"); ?>"
            </script>
        <![endif]-->

    </head>
    <body class="ibbody">
        <div class="ibcontainer">
            <!-- Header -->
            <div class="header" id="header">
                <div class="wrap">
                    <div class="logo">
                        <?php $unit = Ibos::app()->setting->get('setting/unit'); ?>
                        <a href="<?php echo Ibos::app()->setting->get('siteurl'); ?>"><img src="<?php

                            if (!empty($unit['logourl'])): echo $unit['logourl'];
                            else:

                                ?><?php echo STATICURL; ?>/image/logo.png<?php endif; ?>?<?php echo VERHASH; ?>" alt="IBOS"></a>
                    </div>
                    <!-- Nav -->
                    <?php $navs = Ibos::app()->setting->get('cache/nav'); ?>
                    <?php if ($navs): ?>
                        <div class="nvw">
                            <ul class="nv nl" id="nv">
                                <?php foreach ($navs as $index => $nav): ?>
                                    <?php

                                    if ($nav['disabled']) {
                                        continue;
                                    }
                                    $nav['url'] = Url::getUrl($nav['url']);

                                    ?>
                                    <li data-target="#sub_nav_<?php echo $index; ?>">
                                        <a href="<?php echo $nav['url']; ?>" target="<?php

                                        echo $nav['targetnew'] ? '_blank' : '_self';

                                        ?>"><?php echo $nav['name']; ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div>
                            <?php foreach ($navs as $index => $nav): ?>
                                <?php // var_dump( $nav['child'] ); ?>
                                <?php if (!empty($nav['child'])): ?>
                                    <ul class="subnv" id="sub_nav_<?php echo $index; ?>">
                                        <?php foreach ($nav['child'] as $subNav): ?>
                                            <?php

                                            if ($subNav['disabled']) {
                                                continue;
                                            }
                                            $hasPurv = UserUtil::checkNavPurv($subNav);
                                            $subNav['url'] = Url::getUrl($subNav['url']);

                                            ?>
                                            <?php if ($hasPurv): ?>
                                                <li><a target="<?php

                                                    echo $subNav['targetnew'] ? '_blank'
                                                            : '_self';

                                                    ?>" href="<?php echo $subNav['url']; ?>"><?php echo $subNav['name']; ?></a></li>
                                                <?php else: ?>
                                                <li class="disabled"><a href="javascript:;" title="暂无权限访问"><?php echo $subNav['name']; ?></a></li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <!-- Nav end -->
                    <div class="usi">
                        <div class="btn-group">
                            <a href="javascript:;" data-toggle="dropdown" id="user_login_ctrl">
                                <?php

                                echo StringUtil::cutStr(Ibos::app()->user->realname,
                                    6);

                                ?>
                                <i class="caret caret-small"></i>
                            </a>
                        </div>
                        <a href="<?php echo Ibos::app()->createUrl('message/mention/index'); ?>" class="cbtn o-message">
<?php echo Ibos::lang('Message', 'default'); ?>
                        </a>
                    </div>
                    <div class="posr">
                        <div id="message_container" class="reminder" style="display: none;">
                            <a href="javascript:void(0)" onclick="Ibosapp.dropnotify.hide()" class="o-close-small"></a>
                            <ul class="reminder-list" >
                                <li rel="new_folower_count" ><span></span>，<a href="<?php echo Ibos::app()->urlManager->createUrl('weibo/personal/follower'); ?>" class="anchor">查看粉丝</a></li>
                                <li rel="unread_comment" ><span></span>，<a href="<?php echo Ibos::app()->urlManager->createUrl('message/comment/index'); ?>" class="anchor">查看消息</a></li>
                                <li rel="unread_message"><span></span>，<a href="<?php echo Ibos::app()->urlManager->createUrl('message/pm/index'); ?>" class="anchor">查看消息</a></li>
                                <li rel="unread_atme"><span></span>，<a href="<?php echo Ibos::app()->urlManager->createUrl('message/mention/index'); ?>" class="anchor">查看消息</a></li>
                                <li rel="user"><span></span>，<a href="<?php echo Ibos::app()->urlManager->createUrl('message/notify/index'); ?>" class="anchor">查看消息</a></li>
                                <li rel="diary"><span></span>，<a href="<?php echo Ibos::app()->urlManager->createUrl('message/notify/index'); ?>" class="anchor">查看详情</a></li>
                                <li rel="report"><span></span>，<a href="<?php echo Ibos::app()->urlManager->createUrl('message/notify/index'); ?>" class="anchor">查看详情</a></li>
                                <li rel="calendar"><span></span>，<a href="<?php echo Ibos::app()->urlManager->createUrl('message/notify/index'); ?>" class="anchor">查看详情</a></li>
                                <li rel="workflow"><span></span>，<a href="<?php echo Ibos::app()->urlManager->createUrl('message/notify/index'); ?>" class="anchor">查看详情</a></li>
                                <li rel="article"><span></span>，<a href="<?php echo Ibos::app()->urlManager->createUrl('message/notify/index'); ?>" class="anchor">查看详情</a></li>
                                <li rel="officialdoc"><span></span>，<a href="<?php echo Ibos::app()->urlManager->createUrl('message/notify/index'); ?>" class="anchor">查看详情</a></li>
                                <li rel="email"><span></span>，<a href="<?php echo Ibos::app()->urlManager->createUrl('message/notify/index'); ?>" class="anchor">查看详情</a></li>
                                <li rel="unread_group_atme"><span></span>，<a href="" class="anchor">查看消息</a></li>
                                <li rel="unread_group_comment"><span></span>，<a href="" class="anchor">查看消息</a></li>
                                <li rel="car"><span></span>，<a href="" class="anchor">查看消息</a></li>
                                <li rel="assets"><span></span>，<a href="" class="anchor">查看消息</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- 用户登录状态卡 -->
                <div class="uil-card" id="user_login_card" style="display:none;">
                    <div class="uil-card-header">
                        <div class="media">
                            <a href="<?php echo Ibos::app()->user->space_url; ?>" class="pull-left avatar-circle">
                                <img src="<?php echo Ibos::app()->user->avatar_middle; ?>">
                            </a>
                            <div class="media-body">
                                <h5 class="media-heading"><strong><?php echo Ibos::app()->user->realname; ?></strong></h5>
                                <p class="fss"><?php

                                    echo trim(Ibos::app()->user->deptname . ':' . Ibos::app()->user->posname,
                                        ':');

                                    ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="uil-card-body">
                        <div class="mbm">
                            <span class="exp-val"><em><?php echo Ibos::app()->user->credits; ?></em>/<?php echo Ibos::app()->user->next_group_credit; ?></span>
                            <span><i class="lv lv<?php echo Ibos::app()->user->level; ?>"></i> <?php echo Ibos::app()->user->group_title; ?></span>
                        </div>
                        <div class="progress" title="Progress-bar">
                            <div class="progress-bar <?php

                            if (Ibos::app()->user->upgrade_percent > 90):

                                ?>progress-bar-danger<?php else: ?>progress-bar-success<?php endif; ?>" style="width: <?php echo Ibos::app()->user->upgrade_percent; ?>%;"></div>
                        </div>
                        <div class="btn-group btn-group-justified">
                            <a href="<?php echo Ibos::app()->user->space_url; ?>" class="btn"><i class="om-user"></i>个人中心</a>
                            <?php if (Ibos::app()->user->isadministrator): ?><a class="btn" target="_blank" href="<?php echo Ibos::app()->urlManager->createUrl('dashboard/'); ?>" ><i class="om-key"></i><?php

                                echo Ibos::lang('Control center', 'default');

                                ?></a><?php endif; ?>
                            <a href="<?php

                            echo Ibos::app()->urlManager->createUrl('user/default/logout',
                                array('formhash' => FORMHASH));

                            ?>" class="btn">
                                <i class="om-shutdown"></i><?php

                                echo Ibos::lang('Quit', 'default');

                                ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Header end -->
            <!-- load script end -->
            <div class="wrap" id="mainer">
                <div class="mtw">
                    <h2 class="mt pull-left"><?php echo $pageTitle; ?></h2>
                    <span class="pull-right"><?php echo Ibos::app()->setting->get('lunar'); ?></span>
                </div>

                <!-- Mainer -->
                <!-- 这里就是内容模板 -->
                <div class="mpc clearfix" id="page_content">

                </div>
                <!-- 这里就是内容模板 -->

                <!-- Mainer end -->
            </div>
            <!-- Footer -->
            <div class="footer wrap" id="footer">
                <!-- Breadcrumb -->
                <div class="brc clearfix">
                    <a href="<?php echo Ibos::app()->setting->get('siteurl'); ?>" title="">
                        <i class="o-logo"></i>
                    </a>
                    <?php foreach ($breadCrumbs as $key => $value): ?>
                        <a href="<?php echo isset($value['url']) ? $value['url'] : 'javascript:;' ?>"><?php echo $value['name']; ?></a>
<?php endforeach; ?>
                </div>
                <!-- Quick link -->
                <div class="copyright">
                    <div class="quick-link">
                        <a target="_blank" href="http://doc.ibos.com.cn/"><?php

                            echo Ibos::lang('Ibos help', 'default');

                            ?></a>
                        <span class="ilsep">|</span>
                        <a target="_blank" href="http://www.ibos.com.cn"><?php

                            echo Ibos::lang('Ibos feedback', 'default');

                            ?></a>
                        <span class="ilsep">|</span>
                        <a target="_blank" href="<?php echo Ibos::app()->urlManager->createUrl('dashboard/'); ?>" ><?php

                            echo Ibos::lang('Control center', 'default');

                            ?></a>
                        <span class="ilsep">|</span>
                        <a href="javascript:;" data-action="showCert"><?php

                            echo Ibos::lang('Certificate of authorization',
                                'default');

                            ?></a>
                        <span class="ilsep">|</span>
                        <a target="_blank" href="http://www.ibos.com.cn/file/99"><?php

                            echo Ibos::lang('Chrome frame', 'default');

                            ?></a>
                    </div>
                    Powered by <strong>IBOS <?php echo VERSION; ?> <?php echo VERSION_TYPE; ?></strong>
                    <?php if (YII_DEBUG): ?>
                        Processed in <code><?php echo Ibos::app()->performance->endClockAndGet(); ?></code> second(s).
                        <code><?php echo Ibos::app()->performance->getDbstats(); ?></code> queries.
<?php endif; ?>
                </div>
            </div>
        </div>
    </body>
</html>
