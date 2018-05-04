<html>
<meta charset="utf-8">
<body>
<div class="col-lg-12">
    <table class="table table-bordered table-striped table-hover" style="border: 1px solid black">
        <thead>
        <tr>
            <th class="span3 sortable" style="width: 50px;border: 1px solid black">
                <span class="line"></span>页面点击人数
            </th>
            <th class="span3 sortable" style="width: 50px;border: 1px solid black">
                <span class="line"></span>邀请码邀请人数
            </th>
        </tr>
        </thead>
        <tbody id="content" style="border: 1px solid black">
            <tr class="first" style="border: 1px solid black">
                <td class="align-right" style="border: 1px solid black">
                    <?php echo $inviteNum ?>
                </td>
                <td class="align-right" style="width: 20px;border: 1px solid black">
                    <?php echo $shareNum ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>
</body>
</html>