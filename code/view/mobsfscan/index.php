{include file='public/head' /}

<?php
$dengjiArr = ['ERROR', 'Low', 'Medium', 'High', 'Critical'];

$fileList = str_replace('/data/codeCheck/', '', $fileList);
$CategoryList = str_replace('data.tools.semgrep.', '', $CategoryList);
$fileTypeList = getFileType($fileList);
?>
<?php
$searchArr = [
    'action' => $_SERVER['REQUEST_URI'],
    'method' => 'get',
    'inputs' => [
        ['type' => 'text', 'name' => 'search', 'placeholder' => "搜索的内容"],
        ['type' => 'select', 'name' => 'level', 'options' => $dengjiArr, 'frist_option' => '危险等级'],
        ['type' => 'select', 'name' => 'Category', 'options' => $CategoryList, 'frist_option' => '漏洞类别'],
        ['type' => 'select', 'name' => 'code_id', 'options' => $projectList, 'frist_option' => '项目列表'],
        ['type' => 'select', 'name' => 'filename', 'options' => $fileList, 'frist_option' => '文件筛选'],
        ['type' => 'select', 'name' => 'filetype', 'options' => $fileTypeList, 'frist_option' => '文件后缀'],
        ['type' => 'select', 'name' => 'check_status', 'options' => $check_status_list, 'frist_option' => '审计状态', 'frist_option_value' => -1],
    ]];
?>
{include file='public/search' /}

<div class="row tuchu">
    <div class="col-md-12 ">
        {include file='public/batch_del' /}
        <table class="table table-bordered table-hover table-striped">
            <thead>
            <tr>
                <th width="80">
                    <label>
                        <input type="checkbox" value="-1" onclick="quanxuan(this)">全选
                    </label>
                </th>
                <th>ID</th>
                <th>所属项目</th>
                <th>漏洞类型</th>
                <th>cwe</th>
                <th>漏洞描述</th>
                <th>input_case</th>
                <th>masvs</th>
                <th>owasp_mobile</th>
                <th>参考地址</th>
                <th>危险等级</th>
                <th>扫描时间</th>
                <th>状态</th>
                <th style="width: 200px">操作</th>
            </tr>
            </thead>
            <?php foreach ($list as $value) {
                $project = $projectList[$value['code_id']];
                ?>
                <tr>
                    <td>
                        <label>
                            <input type="checkbox" class="ids" name="ids[]" value="<?php echo $value['id'] ?>">
                        </label>
                    </td>
                    <td><?php echo $value['id'] ?></td>
                    <td>
                        <a href="<?php echo url('code/index', ['id' => $value['code_id']]) ?>">
                            <?php echo $value['code_name'] ?></a>
                    </td>
                    <td><?php echo $value['type']; ?></td>
                    <td><?php echo $value['cwe']; ?></td>
                    <td><?php echo $value['description']; ?></td>
                    <td><?php echo $value['input_case']; ?></td>
                    <td><?php echo $value['masvs']; ?></td>
                    <td><?php echo $value['owasp_mobile']; ?></td>
                    <td><?php echo $value['reference']; ?></td>
                    <td><?php echo $value['severity']; ?></td>
                    <td><?php echo $value['create_time'] ?></td>
                    <td>
                        <select class="changCheckStatus form-select" data-id="<?php echo $value['id'] ?>">
                            <option value="0" <?php echo $value['check_status'] == 0 ? 'selected' : ''; ?> >未审核
                            </option>
                            <option value="1" <?php echo $value['check_status'] == 1 ? 'selected' : ''; ?> >有效漏洞
                            </option>
                            <option value="2" <?php echo $value['check_status'] == 2 ? 'selected' : ''; ?> >无效漏洞
                            </option>
                        </select>
                    </td>
                    <td>
                        <!--<a href="<?php /*echo url('code/semgrep_details', ['id' => $value['id']]) */?>"
                           class="btn btn-sm btn-outline-primary">查看漏洞</a>-->
                        <a href="<?php echo url('del', ['id' => $value['id']]) ?>"
                           class="btn btn-sm btn-outline-danger">删除</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>

<input type="hidden" id="to_examine_url" value="<?php echo url('to_examine/mobsfscan') ?>">

{include file='public/to_examine' /}
{include file='public/fenye' /}
{include file='public/footer' /}

<script>
    function quanxuan(obj) {
        var child = $('.table').find('.ids');
        child.each(function (index, item) {
            if (obj.checked) {
                item.checked = true
            } else {
                item.checked = false
            }
        })
    }

    function batch_del() {
        var child = $('.table').find('.ids');
        var ids = ''
        child.each(function (index, item) {
            if (item.value != -1 && item.checked) {
                if (ids == '') {
                    ids = item.value
                } else {
                    ids = ids + ',' + item.value
                }
            }
        })

        $.ajax({
            type: "post",
            url: "<?php echo url('batch_del')?>",
            data: {ids: ids},
            dataType: "json",
            success: function (data) {
                alert(data.msg)
                if (data.code == 1) {
                    window.setTimeout(function () {
                        location.reload();
                    }, 2000)
                }
            }
        });
    }
</script>