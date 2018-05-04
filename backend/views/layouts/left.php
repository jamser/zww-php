<?php 
use common\models\Setting;
?>
<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <!--
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p>Alexander Pierce</p>

                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        -->
        <!-- search form -->
        <!--
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>
        -->
        <!-- /.search form -->

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu'],
                'items' => [
                    [
                        'label' => '管理员设置',
                        'url' => '#',
                        'items' => [
                            ['label' => '管理员列表', 'url' => ['/admin/user/index'],],
                            ['label' => '添加管理员', 'url' => ['/admin/user/add'],],
                            ['label' => '角色', 'url' => ['/admin/role/index'],],
                            ['label' => '授权', 'url' => ['/admin/assignment/index'],],
                        ],
                    ],
                    [
                        'label' => '数据统计',
                        'url' => '#',
                        'items' => [
                            ['label' => '概览', 'url' => ['/doll/statistic/overview'],],
                            ['label' => '统计柱状图', 'url' => ['/doll/statistic/chart'],],
                            ['label' => '支付日报', 'url' => ['/doll/pay/pay-daily'],],
                            ['label' => '用户支付数据', 'url' => ['/doll/pay/index'],],
                            ['label' => '渠道支付统计', 'url' => ['/doll/pay/channel-daily'],],
                            ['label' => '礼包购买统计', 'url' => ['/doll/pay/charge'],],
                            ['label' => '用户充值趋势图', 'url' => ['/doll/pay/charge-chart'],],
                            ['label' => '礼包购买趋势图', 'url' => ['/doll/pay/chart-n'],],
                            ['label' => '用户数据统计', 'url' => ['/doll/record/index'],],
                            ['label' => '用户购买统计', 'url' => ['/doll/record/coins-data'],],
                        ],
                    ],
                    [
                        'label' => '机器运维',
                        'url' => '#',
                        'items' => [
                            ['label' => '机器概率', 'url' => ['/doll/statistic/machine-rate'],],
                            ['label' => '监控报警', 'url' => ['/doll/monitor/index'],],
                            ['label' => '抓取记录', 'url' => ['/doll/monitor/catch-history'],],
                            ['label' => '推流生成', 'url' => ['/doll/rtmp/index'],],
                            ['label' => '推流情况', 'url' => ['/doll/rtmp/rtmp-index'],],
                            ['label' => '机器在线状况', 'url' => ['/doll/device/device'],],
                            ['label' => '小机器地址', 'url' => ['/doll/machine/get-validate-key'],],
                            ['label' => '通知人列表', 'url' => ['/doll/informs/index'],],
                        ],
                    ],
                    [
                        'label' => 'ERP',
                        'url' => '#',
                        'items' => [
                            ['label' => '娃娃列表', 'url' => ['/erp/doll-info/index'],],
                            ['label' => '订单列表', 'url' => ['/erp/order/index'],],
                            ['label' => '扣留订单列表', 'url' => ['/erp/order/detain-order'],],
                        ],
                    ],
                    [
                        'label' => '渠道',
                        'url' => '#',
                        'items' => [
                            ['label' => '渠道情况', 'url' => ['/doll/channel/index'],],
                            ['label' => '渠道近期注册', 'url' => ['/doll/channel/chart'],],
                            ['label' => '渠道日注册', 'url' => ['/doll/channel/chart-hour'],],
                            //['label' => '赠送记录', 'url' => ['/gift/send-list'],],
                        ],
                    ],
                    [
                        'label' => '日志',
                        'url' => '#',
                        'items' => [
                            ['label' => '游戏日志', 'url' => ['/doll/game/index'],],
                        ],
                    ],
                    //['label' => 'Menu Yii2', 'options' => ['class' => 'header']],
                    /**
                    ['label' => '文件', 'url' => ['/file/index']],
                    [
                        'label' => '礼物',
                        'url' => '#',
                        'items' => [
                            ['label' => '礼物', 'url' => ['/gift/index'],],
                            ['label' => '赠送记录', 'url' => ['/gift/send-list'],],
                        ],
                    ],
                    [
                        'label' => Setting::getValueByKey('callerName'),
                        'url' => '#',
                        'items' => [
                            ['label' => '列表', 'url' => ['/caller/list'],],
                            ['label' => '申请', 'url' => ['/caller/apply-list'],],
                        ],
                    ],
                    [
                        'label' => '财务',
                        'url' => '#',
                        'items' => [
                            ['label' => '用户提现', 'url' => ['/finance/user-withdraw-list'],],
                        ],
                    ],
                    [
                        'label' => '系统',
                        'url' => '#',
                        'items' => [
                            ['label' => '配置', 'url' => ['/setting/index'],],
                        ],
                    ],
                    
                    ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                    [
                        'label' => 'Same tools',
                        'icon' => 'fa fa-share',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Gii', 'icon' => 'fa fa-file-code-o', 'url' => ['/gii'],],
                            ['label' => 'Debug', 'icon' => 'fa fa-dashboard', 'url' => ['/debug'],],
                            [
                                'label' => 'Level One',
                                'icon' => 'fa fa-circle-o',
                                'url' => '#',
                                'items' => [
                                    ['label' => 'Level Two', 'icon' => 'fa fa-circle-o', 'url' => '#',],
                                    [
                                        'label' => 'Level Two',
                                        'icon' => 'fa fa-circle-o',
                                        'url' => '#',
                                        'items' => [
                                            ['label' => 'Level Three', 'icon' => 'fa fa-circle-o', 'url' => '#',],
                                            ['label' => 'Level Three', 'icon' => 'fa fa-circle-o', 'url' => '#',],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                     * 
                     */
                ],
            ]
        ) ?>

    </section>

</aside>
