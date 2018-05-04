<div>
    <table>
        <thead>
        <tr>
            <th>
                <span>机器ID</span>
            </th>
            <th>
                <span>机器编号</span>
            </th>
            <th>
                <span>机器状态</span>
            </th>
            <th>
                <span>机器</span>
            </th>
            <th>
                <span>娃娃名称</span>
            </th>
            <th>
                <span>推流状态</span>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data as $k=>$v) {
            /* @var $model \common\models\doll\MachineStatistic */
            ?>
            <tr>
                <td>
                    <?php echo $v['machine_id'] ?>
                </td>
                <td>
                    <?php echo $v['machine_code'] ?>
                </td>
                <td>
                    <?php echo $v['machine_status'] ?>
                </td>
                <td>
                    <?php echo $v['machine_url'] ?>
                </td>
                <td>
                    <?php echo $v['name'] ?>
                </td>
                <td>
                    <?php
                    $status = $v['rtmp_state'];
                    if($status == '断流'){
                        echo "<span>无输入流</span>";
                    }
                    ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>