<?php
declare(strict_types=1);

$studentName   = "Касым Рахман";
$course        = 3;
$program       = "Информационные системы";
$averageGrade  = 85.75;
$hasDebt       = false;
$middleName    = null;

const PASSING_SCORE = 50;
const DEPOSIT_RATE  = 10.0;

$depositAmount = 1000000.0;
$depositYears  = 4;
$currency      = "₸";

$laboratoryGrade   = 85;
$independentGrade  = 78;
$examGrade         = 92;

$result = $laboratoryGrade * 0.30
        + $independentGrade * 0.20
        + $examGrade * 0.50;

$status = $result >= PASSING_SCORE
    ? "Дисциплина освоена"
    : "Дисциплина не освоена";

$debtText = $hasDebt === true ? "есть" : "нет";

$income   = $depositAmount * DEPOSIT_RATE / 100 * $depositYears;
$totalSum = $depositAmount + $income;

$inputValue   = "125";
$integerValue = (int) $inputValue;
$floatValue   = (float) $inputValue;
$booleanValue = (bool) $inputValue;

$cmp1 = ("10" == 10);
$cmp2 = ("10" === 10);
$cmp3 = (0 == false);
$cmp4 = (0 === false);
$cmp5 = (null == false);
$cmp6 = (null === false);

$fullName = $studentName . " — " . $program;

$formattedResult  = number_format($result, 2, ".", " ");
$formattedIncome  = number_format($income, 2, ".", " ");
$formattedTotal   = number_format($totalSum, 2, ".", " ");
$formattedDeposit = number_format($depositAmount, 2, ".", " ");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Лабораторная работа №2 — Вариант 9</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #444; padding: 8px 12px; text-align: left; }
        th { background: #f0f0f0; }
        h2 { margin-top: 25px; }
        code { background: #f4f4f4; padding: 2px 4px; }
    </style>
</head>
<body>

<h1>Лабораторная работа №2 — Вариант 9 «Банковский вклад»</h1>

<h2>1. Информация о студенте</h2>
<ul>
    <li>ФИО: <b><?= htmlspecialchars($studentName, ENT_QUOTES, 'UTF-8') ?></b></li>
    <li>Курс: <b><?= $course ?></b></li>
    <li>Программа: <b><?= htmlspecialchars($program, ENT_QUOTES, 'UTF-8') ?></b></li>
    <li>Средний балл: <b><?= number_format($averageGrade, 2, '.', ' ') ?></b></li>
    <li>Задолженность: <b><?= $debtText ?></b></li>
</ul>

<h2>2. Итоговая оценка</h2>
<p>
    Формула: <code>R = 0.30·L + 0.20·S + 0.50·E</code><br>
    L = <?= $laboratoryGrade ?>, S = <?= $independentGrade ?>, E = <?= $examGrade ?><br>
    <b>Итоговая оценка:</b> <?= $formattedResult ?><br>
    <b>Статус:</b> <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>
</p>

<h2>3. Расчёт банковского вклада</h2>
<table>
    <tr><th>Параметр</th><th>Значение</th></tr>
    <tr><td>Сумма вклада</td><td><?= $formattedDeposit ?> <?= $currency ?></td></tr>
    <tr><td>Процентная ставка</td><td><?= DEPOSIT_RATE ?> % годовых</td></tr>
    <tr><td>Срок</td><td><?= $depositYears ?> лет</td></tr>
    <tr><td><b>Доход</b></td><td><b><?= $formattedIncome ?> <?= $currency ?></b></td></tr>
    <tr><td><b>Итоговая сумма</b></td><td><b><?= $formattedTotal ?> <?= $currency ?></b></td></tr>
</table>

<h2>4. Преобразование типов</h2>
<p>
    Исходное: <code>$inputValue = "<?= $inputValue ?>"</code> (string)<br>
    <code>(int)  "125"</code> → <b><?= $integerValue ?></b> (int)<br>
    <code>(float)"125"</code> → <b><?= $floatValue ?></b> (float)<br>
    <code>(bool) "125"</code> → <b><?= $booleanValue ? 'true' : 'false' ?></b> (bool)
</p>

<h2>5. Сравнения</h2>
<table>
    <tr><th>Выражение</th><th>Результат</th><th>Объяснение</th></tr>
    <tr><td><code>"10" == 10</code></td>    <td><?= $cmp1 ? 'true' : 'false' ?></td><td>Нестрогое: строка приводится к числу</td></tr>
    <tr><td><code>"10" === 10</code></td>   <td><?= $cmp2 ? 'true' : 'false' ?></td><td>Строгое: типы разные</td></tr>
    <tr><td><code>0 == false</code></td>    <td><?= $cmp3 ? 'true' : 'false' ?></td><td>Нестрогое: оба приводятся к 0</td></tr>
    <tr><td><code>0 === false</code></td>   <td><?= $cmp4 ? 'true' : 'false' ?></td><td>Строгое: int ≠ bool</td></tr>
    <tr><td><code>null == false</code></td> <td><?= $cmp5 ? 'true' : 'false' ?></td><td>Нестрогое: оба «ложные»</td></tr>
    <tr><td><code>null === false</code></td><td><?= $cmp6 ? 'true' : 'false' ?></td><td>Строгое: null ≠ bool</td></tr>
</table>

<h2>6. Форматированный вывод через printf()</h2>
<p>
<?php
printf(
    "Студент: %s | Курс: %d | Итоговая оценка: %.2f | Статус: %s",
    $studentName,
    $course,
    $result,
    $status
);
?>
</p>

</body>
</html>