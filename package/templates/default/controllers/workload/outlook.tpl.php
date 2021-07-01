<!DOCTYPE html>
<html>
<head>
    <title><?php echo LANG_WL_CONTROLLER; ?> &mdash; <?php $this->sitename(); ?></title>
    <style>
        .table{
            border: 1px solid #eee;
            table-layout: fixed;
            width: 100%;
            margin-bottom: 20px;
        }
        .table th {
            font-weight: 500;
            padding: 5px 10px;
            background: #efefef;
            border: 1px solid #dddddd;
            text-align: left;
        }
        .table td{
            padding: 5px 10px;
            border: 1px solid #eee;
            text-align: left;
        }
        .table tbody tr:nth-child(odd){
            background: #fff;
        }
        .table tbody tr:nth-child(even){
            background: #F7F7F7;
        }
        .fw {
            font-weight: 500;
            padding: 5px 10px;
        }
    </style>
</head>
<body>
    <div class="fw">
    Начало наблюдений: <?php echo $data["start_date"]; ?><br>
    Окончание наблюдний: <?php echo $data["finish_date"]; ?><br>
    Количество процессорв: <?php echo ($data["qty_cpu"] === false) ?
                                "не определено" : $data["qty_cpu"]; ?><br>
    Всего визитов: <?php echo $data["total_visits"]; ?><br>
    </div>
    <table class="table">
        <tr>
            <th>Визитёр</th>
            <th>Кол-во визитов</th>
            <th>% от общего кол-ва визитов</th>
            <th>Мах нагрузка процессоров в %</th>
        </tr>
        <?php foreach ($data["visits"] as $type => $row): ?>
        <tr>
            <td><?php echo $type; ?></td>
            <td><?php echo $row["qty"]; ?></td>
            <td>
                <?php echo ($data["total_visits"] == 0) ? 
                    0 : sprintf("%01.2f", 100*($row["qty"]/$data["total_visits"])); ?>
            </td>
            <td><?php echo ($row["max_la"] == -1) ? "-" : $row["max_la"]; ?></td>
        </tr>
        <?php endforeach ?>
    </table> 
</body>