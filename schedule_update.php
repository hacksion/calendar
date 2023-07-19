<?php
require('common.php');
$data = ['result' => 0, 'action' => 'new', 'msg' => ''];
$DB = new TM\DB;

foreach ($_POST as $key => $value) {
    $data[$key] = is_array($value) ? implode(',', $value) : htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, "UTF-8");
}


if ($data['schedule_datetime'] && $data['title']) {
    if ($data['id']) {
        if ($data['action'] == 'delete') {
            $data['result'] = $DB->delete('schedule_t', ['id' => $data['id']]);
            $data['msg'] = $data['result'] > 0 ? '削除しました' : '削除できませんでした';
        } else {
            $data['result'] = $DB->update(
                'schedule_t',
                ['id' => $data['id']],
                [
                    'title' => $data['title'],
                    'schedule_datetime' => date('Y-m-d H:i:s', strtotime($data['schedule_datetime'])),
                    'content' => $data['content']
                ]
            );
            $data['msg'] = $data['result'] > 0 ? '更新しました' : '更新できませんでした';
            $data['action'] = 'update';
        }
    } else {
        $data['result'] = $data['id'] = $DB->insert(
            'schedule_t',
            [
                'user_id' => $_SESSION['schedule']['user_id'],
                'title' => $data['title'],
                'schedule_datetime' => date('Y-m-d H:i:s', strtotime($data['schedule_datetime'])),
                'content' => $data['content']
            ]
        );
        $data['msg'] = $data['result'] > 0 ? '登録しました' : '登録できませんでした';
    }
}


echo json_encode($data);
