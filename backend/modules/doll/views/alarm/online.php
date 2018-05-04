<div>
    <div>
        <div>
            <table>
                <thead>
                <tr>
                    <th>
                        <span>机器名</span>
                    </th>
                    <th>
                        <span>在线状态</span>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($models as $model) {
                    /* @var $model \common\models\doll\MachineStatistic */
                    ?>
                    <tr>
                        <td>
                            <?php echo $model['machine_name'] ?>
                        </td>
                        <td>
                            <?php echo $model['machine_state'] ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>