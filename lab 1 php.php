<?php
// ======= ОСНОВНЫЕ КОНСТАНТЫ =======
const UNIVERSITY = "Алматинский технологический университет";
const DISCIPLINE = "Программирование на PHP";

// ======= ДАННЫЕ СТУДЕНТА (ЗАМЕНИТЕ НА СВОИ) =======
$studentName = "Касым Рахман";
$group = "ИС-24-22";
$course = 3;
$variant = 7; // Номер варианта

// ======= ИСХОДНЫЕ ДАННЫЕ ДЛЯ ВАРИАНТА 7 =======
// Тема: Расходы на топливо
// Формула: result = distance * fuelConsumption / 100 * fuelPrice
$distance = 350;           // Расстояние в километрах (км)
$fuelConsumption = 8.5;    // Расход топлива на 100 км (литры)
$fuelPrice = 215;          // Цена 1 литра топлива (тенге)

// ======= ВЫЧИСЛЕНИЕ РЕЗУЛЬТАТА =======
$result = $distance * $fuelConsumption / 100 * $fuelPrice;

// ======= ОПРЕДЕЛЕНИЕ СТАТУСА =======
// Статус зависит от итоговой суммы
if ($result > 15000) {
    $status = "Высокие расходы на топливо";
} elseif ($result > 5000) {
    $status = "Средние расходы на топливо";
} else {
    $status = "Низкие расходы на топливо";
}

// ======= ДОПОЛНИТЕЛЬНЫЕ РАСЧЁТЫ ДЛЯ НАГЛЯДНОСТИ =======
$fuelNeeded = ($distance / 100) * $fuelConsumption; // Общее количество литров
$costPerKm = $result / $distance; // Стоимость 1 км пути
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Лабораторная работа №1 - Карточка студента</title>
    <style>
        /* ======= СТИЛИ ДЛЯ СТРАНИЦЫ ======= */
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background: #f4f6f8;
        }
        .card {
            padding: 25px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
        }
        .header h2 {
            color: #495057;
            font-weight: normal;
            margin: 5px 0 0;
        }
        .info-block {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }
        .info-block h3 {
            margin-top: 0;
            color: #495057;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .info-item {
            padding: 5px 0;
        }
        .info-item strong {
            display: inline-block;
            min-width: 120px;
            color: #495057;
        }
        .result-block {
            margin: 20px 0;
            padding: 15px;
            background: #e7f3ff;
            border-radius: 8px;
            border-left: 4px solid #28a745;
        }
        .result-item {
            font-size: 1.1em;
            padding: 5px 0;
        }
        .result-item strong {
            color: #0056b3;
        }
        .status-success {
            color: #28a745;
            font-weight: bold;
            font-size: 1.2em;
        }
        .status-warning {
            color: #ffc107;
            font-weight: bold;
            font-size: 1.2em;
        }
        .status-danger {
            color: #dc3545;
            font-weight: bold;
            font-size: 1.2em;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 0.9em;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
        }
        .variant-badge {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 0.85em;
        }
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
            margin: 10px 0;
        }
        .detail-box {
            background: white;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            border: 1px solid #dee2e6;
        }
        .detail-box .number {
            font-size: 1.5em;
            font-weight: bold;
            color: #007bff;
        }
        .detail-box .label {
            font-size: 0.85em;
            color: #6c757d;
        }
        @media (max-width: 600px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            .detail-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <div class="card">
        <!-- ======= ЗАГОЛОВОК ======= -->
        <div class="header">
            <h1><?= UNIVERSITY ?></h1>
            <h2><?= DISCIPLINE ?></h2>
            <p style="margin-top: 5px;">
                <span class="variant-badge">Вариант №<?= $variant ?></span>
            </p>
        </div>

        <!-- ======= ИНФОРМАЦИЯ О СТУДЕНТЕ ======= -->
        <div class="info-block">
            <h3>📋 Информация о студенте</h3>
            <div class="info-grid">
                <div class="info-item"><strong>ФИО:</strong> <?= $studentName ?></div>
                <div class="info-item"><strong>Группа:</strong> <?= $group ?></div>
                <div class="info-item"><strong>Курс:</strong> <?= $course ?></div>
                <div class="info-item"><strong>Тема варианта:</strong> Расходы на топливо</div>
            </div>
        </div>

        <!-- ======= ИСХОДНЫЕ ДАННЫЕ ======= -->
        <div class="info-block" style="border-left-color: #17a2b8;">
            <h3>📊 Исходные данные</h3>
            <div class="detail-grid">
                <div class="detail-box">
                    <div class="number"><?= $distance ?></div>
                    <div class="label">Расстояние (км)</div>
                </div>
                <div class="detail-box">
                    <div class="number"><?= $fuelConsumption ?></div>
                    <div class="label">Расход топлива (л/100 км)</div>
                </div>
                <div class="detail-box">
                    <div class="number"><?= $fuelPrice ?></div>
                    <div class="label">Цена топлива (₸/л)</div>
                </div>
            </div>
        </div>

        <!-- ======= РЕЗУЛЬТАТЫ РАСЧЁТОВ ======= -->
        <div class="result-block">
            <h3>💰 Результаты расчётов</h3>
            <div class="result-item">
                <strong>Общие расходы на топливо:</strong> 
                <?= number_format($result, 2, ',', ' ') ?> ₸
            </div>
            <div class="result-item">
                <strong>Необходимо топлива:</strong> 
                <?= number_format($fuelNeeded, 2, ',', ' ') ?> литров
            </div>
            <div class="result-item">
                <strong>Стоимость 1 км пути:</strong> 
                <?= number_format($costPerKm, 2, ',', ' ') ?> ₸
            </div>
        </div>

        <!-- ======= СТАТУС ======= -->
        <div class="info-block" style="border-left-color: #ffc107;">
            <h3>📌 Итоговый статус</h3>
            <p style="font-size: 1.1em;">
                <?php 
                // Выбираем класс CSS в зависимости от статуса
                if ($result > 15000) {
                    echo '<span class="status-danger">' . $status . '</span>';
                } elseif ($result > 5000) {
                    echo '<span class="status-warning">' . $status . '</span>';
                } else {
                    echo '<span class="status-success">' . $status . '</span>';
                }
                ?>
            </p>
            <p style="font-size: 0.9em; color: #6c757d; margin-top: 5px;">
                (При расходах до 5000 ₸ — низкие, до 15000 ₸ — средние, выше 15000 ₸ — высокие)
            </p>
        </div>

        <!-- ======= ДАТА ======= -->
        <div class="footer">
            Дата формирования отчёта: <?= date("d.m.Y в H:i") ?><br>
            <span style="font-size: 0.85em;">Лабораторная работа №1 • Программирование на PHP</span>
        </div>
    </div>

</body>
</html>