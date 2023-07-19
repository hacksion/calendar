<?php
require('common.php');
$data = ['result' => 0, 'action' => 'date_change', 'msg' => ''];
$DB = new TM\DB;
foreach ($_POST as $key => $value)
    $data[$key] = is_array($value) ? implode(',', $value) : htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, "UTF-8");

if ($data['id']) {
    $data['result'] = $DB->update(
        'schedule_t',
        ['id' => $data['id']],
        ['schedule_datetime' => date('Y-m-d H:i:s', strtotime($data['schedule_datetime']))]
    );
    $data['msg'] = $data['result'] > 0 ? '更新しました' : '更新できませんでした';
}

echo json_encode($data);
