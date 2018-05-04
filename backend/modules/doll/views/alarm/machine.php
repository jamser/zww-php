<div>
    <div>
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
                        <span>娃娃名称</span>
                    </th>
                    <th>
                        <span>机器状态</span>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($models as $model) {
                    /* @var $model \common\models\doll\MachineStatistic */
                    ?>
                    <tr>
                        <td>
                            <?php echo $model['id'] ?>
                        </td>
                        <td>
                            <?php echo $model['machine_code'] ?>
                        </td>
                        <td>
                            <?php echo $model['name'] ?>
                        </td>
                        <td>
                            <?php echo $model['machine_status'] ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>