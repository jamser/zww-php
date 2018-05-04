<div>
    <div>
        <div>
            <table>
                <thead>
                <tr>
                    <th>
                        <span>申诉时间</span>
                    </th>
                    <th>
                        <span>机器id</span>
                    </th>
                    <th>
                        <span>申诉原因</span>
                    </th>
                    <th>
                        <span>审核状态</span>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($models as $model) {
                    /* @var $model \common\models\doll\MachineStatistic */
                    ?>
                    <tr>
                        <td>
                            <?php echo $model['creat_date'] ?>
                        </td>
                        <td>
                            <?php echo $model['doll_id'] ?>
                        </td>
                        <td>
                            <?php echo $model['complaint_reason'] ?>
                        </td>
                        <td>
                            <?php
                            $status = $model['check_state'];
                            if($status == 0){
                                $status = '待审核';
                            }elseif($status == 1){
                                $status = '通过返币';
                            }elseif($status == 2){
                                $status = '通过补娃娃';
                            }else{
                                $status = '未通过';
                            }
                            echo $status;
                            ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>