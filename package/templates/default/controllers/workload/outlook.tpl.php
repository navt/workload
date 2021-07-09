<!DOCTYPE html>
<html>
<head>
<title><?php echo LANG_WORKLOAD_CONTROLLER; ?> &mdash; <?php $this->sitename(); ?></title>
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
    Всего запросов: <?php echo $data["total_querys"]; ?><br>
    </div>
    <table class="table">
        <tr>
            <th>Тип запроса к серверу</th>
            <th>Кол-во запросов</th>
            <th>% от общего кол-ва запросов</th>
            <th>Мах загрузка системы в %</th>
            <th>Время фиксации mах загрузки</th>
        </tr>
        <?php foreach ($data["querys"] as $type => $row): ?>
        <tr>
            <td><?php echo $type; ?></td>
            <td><?php echo $row["qty"]; ?></td>
            <td>
                <?php echo ($data["total_querys"] == 0) ? 
                    0 : sprintf("%01.2f", 100*($row["qty"]/$data["total_querys"])); ?>
            </td>
            <td><?php echo ($row["max_la"] == -1) ? "-" : $row["max_la"]; ?></td>
            <td><?php echo $row["mft"]; ?></td>
        </tr>
        <?php endforeach ?>
    </table> 
</body>