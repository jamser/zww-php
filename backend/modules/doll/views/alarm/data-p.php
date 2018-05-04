<div>
    <div>
        <div>
            <table>
                <thead>
                <tr>
                    <th>
                        <span>日期</span>
                    </th>
                    <th>
                        <span>机器ID</span>
                    </th>
                    <th>
                        <span>机器编号</span>
                    </th>
                    <th>
                        <span>游戏次数</span>
                    </th>
                    <th>
                        <span>抓中概率</span>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($models as $model) {
                    /* @var $model \common\models\doll\MachineStatistic */
                    ?>
                    <tr>
                        <td>
                            <?php echo date('Y-m-d', $model['start_time']) ?>
                        </td>
                        <td>
                            <?php echo $model['machine_id'] ?>
                        </td>
                        <td>
                            <?php echo $model['machine_code'] ?>
                        </td>
                        <td>
                            <?php echo $model['play_count'] ?>
                        </td>
                        <td>
                            <?php
                            $rate = $model['play_count']>0 ? round(($model['grab_count']/$model['play_count'])*100,2):0;
                            echo $rate;
                            ?>%
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>