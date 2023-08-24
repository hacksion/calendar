<?php
require('common.php');

//display current month if there is no GET
$Ymd_01 = date('Ym01');

if (isset($_POST['y']) && isset($_POST['m']) && checkdate($_POST['m'], 1, $_POST['y'])) {
    $Ymd_01 = date('Ymd', strtotime($_POST['y'] . sprintf('%02d', $_POST['m']) . '01'));
    unset($_POST['y']);
    unset($_POST['m']);
    unset($_SESSION['schedule']);
}

//ログインした状態を再現 ID = 1
$_SESSION['schedule']['user_id'] = 1;

if (isset($_SESSION['schedule']['y']) && isset($_SESSION['schedule']['m']) && checkdate($_SESSION['schedule']['m'], 1, $_SESSION['schedule']['y'])) {
    $Ymd_01 = date('Ymd', strtotime($_SESSION['schedule']['y'] . sprintf('%02d', $_SESSION['schedule']['m']) . '01'));
}

$_SESSION['schedule']['y'] = date('Y', strtotime($Ymd_01));
$_SESSION['schedule']['m'] = date('m', strtotime($Ymd_01));

$last_month = date('Ymt', strtotime("-1 month $Ymd_01"));
$next_month = date('Ymd', strtotime("+1 month $Ymd_01"));

$DB = new TM\DB;
$sql = "
SELECT 
`S`.`id`,
DATE_FORMAT(`S`.`schedule_datetime`, '%Y%m%d') AS `schedule_date`,
DATE_FORMAT(`S`.`schedule_datetime`, '%H:%i') AS `schedule_time`,
`S`.`title`,
`S`.`content`,
`UC`.`name`
FROM 
`schedule_t` AS `S`
LEFT OUTER JOIN `user_m` AS `UC` ON (`S`.`user_id` = `UC`.`id`)
WHERE 
`S`.`status` = ? AND
DATE_FORMAT(`S`.`schedule_datetime`, '%Y%m%d') BETWEEN ? AND ?
";
//表示月の前後月も取得する
$schedules = $DB->select($sql, [
    1,
    date('Ym01', strtotime($last_month)),
    date('Ymt', strtotime($next_month))
]);

//every day in the calendar
$date_set = [];

//last month
$start_weekday = date('w', strtotime($Ymd_01));
if ($start_weekday > 0) {
    for ($i = date('Ymd', strtotime("-" . ($start_weekday - 1) . " day $last_month")); $last_month >= $i; $i = date('Ymd', strtotime("+1 day $i"))) {
        $date_set[] = $i;
    }
}

//this month
for ($i = date('Ymd', strtotime($Ymd_01)); date('Ymt', strtotime($Ymd_01)) >= $i; $i = date('Ymd', strtotime("+1 day $i"))) {
    $date_set[] = $i;
}

//next month
$last_weekday = date('w', strtotime(date('Ymt', strtotime($Ymd_01))));
if ($last_weekday < 6) {
    for ($i = $next_month; date('Ymd', strtotime("+" . (6 - $last_weekday - 1) . " day $next_month")) >= $i; $i = date('Ymd', strtotime("+1 day $i"))) {
        $date_set[] = $i;
    }
}

//create tbody in table
//ondrop="Drop(event, id)   draggable="true"
$calendar = '<tr>';
foreach ($date_set as $key => $value) {
    $row_last = date('Ymd', strtotime(end($date_set))) == date('Ymd', strtotime($value)) ? 1 : 0;
    $calendar .= '<td draggable="false" dropzone="move" ondragleave="dragLeave(event)" ondragover="dragOver(event)" ondrop="drop(event)" ondragstart="dragStart(event)" class="droparea ';
    $calendar .= date('w', strtotime($value)) == 0 ? 'text-danger ' : '';
    $calendar .= date('Ym', strtotime($value)) != date('Ym', strtotime($Ymd_01)) ? 'bg-light' : '';
    $calendar .= '" data-td_date="' . date('Y-m-d', strtotime($value)) . '">';
    $calendar .= '<p class="create_schedule" data-bs-toggle="modal" data-bs-target="#scheduleModal" data-id="" data-schedule_datetime="' . date('Y-m-d', strtotime($value)) . 'T' . date('H:i') . '">';
    $calendar .= date('Ym', strtotime($Ymd_01)) == date('Ym', strtotime($value)) ? date('j', strtotime($value)) : (date('Y', strtotime($Ymd_01)) != date('Y', strtotime($value)) ? date('Y/n/j', strtotime($value)) : date('n/j', strtotime($value)));
    $calendar .= '</p>';
    //schedule
    if ($schedules) {
        foreach ($schedules as $schedule) {
            if ($schedule['schedule_date'] == $value) {
                $calendar .= '<div class="schedule_data" data-bs-toggle="modal" data-bs-target="#scheduleModal" data-id="' . $schedule['id'] . '" data-schedule_datetime="' . date('Y-m-d', strtotime($schedule['schedule_date'])) . 'T' . date('H:i', strtotime($schedule['schedule_time'])) . '" draggable="true">';
                $calendar .= '<span id="title_' . $schedule['id'] . '">' . $schedule['title'] . '</span>';
                $calendar .= '<span class="d-none" id="content_' . $schedule['id'] . '">' . $schedule['content'] . '</span>';
                $calendar .= '</div>';
            }
        }
    }
    $calendar .= '</td>';
    if (($key + 1) % 7 == 0) {
        $calendar .= $row_last ? '</tr>' : '</tr><tr>';
    }
}

//create options in select tag
$year_options = '';
for ($i = date('Y', strtotime('-10 year')); $i <= date('Y', strtotime('+3 year')); $i++) {
    $year_options .= '<option value="' . $i . '"' . (date('Y', strtotime($Ymd_01)) == $i ? ' selected' : '') . '>' . $i . ' 年</option>';
}
$month_options = '';
for ($i = 1; $i < 13; $i++) {
    $month_options .= '<option value="' . $i . '"' . (date('n', strtotime($Ymd_01)) == $i ? ' selected' : '') . '>' . $i . ' 月</option>';
}
?>
<!doctype html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Calendar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link href="./css/style.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-4">
                <form method="POST" action="" name="select_date">
                    <div class="input-group my-3">
                        <select name="y" class="form-select">
                            <?php echo $year_options; ?>
                        </select>
                        <select name="m" class="form-select">
                            <?php echo $month_options; ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="col-4 my-3">
                <button type="button" class="btn btn-secondary" id="select_last_month">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-bar-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z" />
                    </svg>
                </button>
                <button type="button" class="btn btn-secondary" id="select_today">今月</button>
                <button type="button" class="btn btn-secondary" id="select_next_month">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-bar-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z" />
                    </svg>
                </button>
            </div>
        </div>

        <table class="table calendar">
            <thead>
                <tr>
                    <th class="text-danger">日</th>
                    <th>月</th>
                    <th>火</th>
                    <th>水</th>
                    <th>木</th>
                    <th>金</th>
                    <th>土</th>
                </tr>
            </thead>
            <tbody>
                <?php echo $calendar; ?>
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">

        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="scheduleModalLabel">スケジュール</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="input-group mb-3">
                        <span class="input-group-text">タイトル</span>
                        <input type="text" name="title" value="" class="form-control">
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text">日付</span>
                        <input type="datetime-local" name="schedule_datetime" value="" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="content_box" class="form-label">内容</label>
                        <textarea name="content" class="form-control" id="content_box" rows="3"></textarea>
                    </div>

                    <input type="hidden" name="id" value="">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="delete_btn">削除</button>
                    <button type="button" class="btn btn-primary" id="save_btn">保存</button>
                </div>
            </div>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous">
    </script>
    <script src="./js/common.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const select_y = document.querySelector('select[name=y]');
        const select_m = document.querySelector('select[name=m]');
        const select_date = document.forms.select_date;
        const select_today = document.getElementById('select_today');
        const select_last_month = document.getElementById('select_last_month');
        const select_next_month = document.getElementById('select_next_month');
        const scheduleModal = document.getElementById('scheduleModal');
        const input_id = document.querySelector('input[name=id]');
        const input_title = document.querySelector('input[name=title]');
        const schedule_datetime = document.querySelector('input[name=schedule_datetime]');
        const textarea_content = document.querySelector('textarea[name=content]');
        const delete_btn = document.getElementById('delete_btn');

        select_y.onchange = () => select_date.submit();

        select_m.onchange = () => select_date.submit();

        select_today.onclick = () => {
            let date = new Date();
            let year = date.getFullYear();
            let month = date.getMonth() + 1;
            selectedYearMonth('set', year, month);
            select_date.submit();
        }

        select_last_month.onclick = () => {
            let Ym = selectedYearMonth();
            let date = new Date(Ym[0], (Ym[1] - 2), 1);
            selectedYearMonth('set', date.getFullYear(), date.getMonth() + 1);
            select_date.submit();
        }

        select_next_month.onclick = () => {
            let Ym = selectedYearMonth();
            let date = new Date(Ym[0], Ym[1], 1);
            selectedYearMonth('set', date.getFullYear(), date.getMonth() + 1);
            select_date.submit();
        }

        save_btn.onclick = e => {
            if (input_title.value) {
                scheduleUpdate();
                document.querySelector('[data-bs-dismiss="modal"]').click();
            }
        }

        scheduleModal.addEventListener('show.bs.modal', e => {
            let id = e.relatedTarget.dataset.id;
            input_id.value = id;
            input_title.value = id ? document.getElementById('title_' + id).textContent : '';
            schedule_datetime.value = e.relatedTarget.dataset.schedule_datetime;
            let span_content = id ? document.getElementById('content_' + id) : '';
            textarea_content.value = id ? span_content.textContent : '';
            if (!id) delete_btn.classList.add('d-none');
        });

        scheduleModal.addEventListener('hidden.bs.modal', e => {
            input_id.value = '';
            input_title.value = '';
            schedule_datetime.value = '';
            textarea_content.value = '';
            delete_btn.classList.remove('d-none');
        });

        delete_btn.onclick = e => {
            Swal.fire({
                title: '削除してもいいですか?',
                showCancelButton: true,
                cancelButtonText: 'いいえ',
                confirmButtonText: 'はい',
                confirmButtonColor: '#dc3545',
                allowOutsideClick: false
            }).then(ret => {
                if (ret.value && input_id.value) {
                    let fdo = new FormData();
                    fdo.append('action', 'delete');
                    fdo.append('id', input_id.value);
                    asyncPost('schedule_update.php', fdo, json => {
                        if (json.result > 0) {
                            Swal.fire(swalOption('success', json.msg)).then(ses => {
                                if (ses.value) {
                                    document.querySelector('[data-id="' + json.id + '"]').remove();
                                    document.querySelector('[data-bs-dismiss="modal"]').click();
                                }
                            });
                            return;
                        } else {
                            Swal.fire(swalOption('warning', json.msg)).then(ses => {
                                if (ses.value) document.querySelector(
                                    '[data-bs-dismiss="modal"]').click();
                            });
                            return;
                        }
                        Swal.fire(swalOption('error', 'Reload Rrowser')).then(ses => {
                            if (ses.value) location.reload();
                        });
                    });
                }
            });
        }
    </script>

</body>

</html>
