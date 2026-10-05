<?php
declare(strict_types=1);

/* ---------- 1. Безопасный вывод ---------- */
function h(string $value): string
{
    return htmlspecialchars(
        $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

/* ---------- 2. Безопасное извлечение строки ---------- */
function postString(string $key): ?string
{
    $value = $_POST[$key] ?? null;
    return is_string($value) ? $value : null;
}

/* ---------- 3. Allow-list (бизнес-правила) ---------- */
$allowedGroups = ['WC-23-21', 'WC-23-22', 'WC-23-23'];
$allowedDisciplines = [
    'php'      => 'Программирование на PHP',
    'db'       => 'Базы данных',
    'web'      => 'Веб-разработка',
];
$allowedFormats = ['offline', 'online'];

/* ---------- 4. Хранилища значений и ошибок ---------- */
$values = [
    'full_name' => '',
    'email'     => '',
    'group'     => '',
    'discipline'=> '',
    'format'    => '',
];
$errors  = [];
$success = false;

/* ---------- 5. Обработка только POST ---------- */
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {

    /* --- Извлечение + нормализация --- */
    $fullName   = postString('full_name');
    $email      = postString('email');
    $group      = postString('group');
    $discipline = postString('discipline');
    $format     = postString('format');

    $values['full_name']  = $fullName   === null ? '' : trim($fullName);
    $values['email']      = $email      === null ? '' : trim($email);
    $values['group']      = $group      ?? '';
    $values['discipline'] = $discipline ?? '';
    $values['format']     = $format     ?? '';

    /* --- Валидация ФИО --- */
    if ($fullName === null || $values['full_name'] === '') {
        $errors['full_name'] = 'Укажите ФИО.';
    } elseif (mb_strlen($values['full_name']) > 100) {
        $errors['full_name'] = 'ФИО не должно превышать 100 символов.';
    }

    /* --- Валидация e-mail --- */
    if ($email === null || $values['email'] === '') {
        $errors['email'] = 'Укажите электронную почту.';
    } elseif (filter_var($values['email'], FILTER_VALIDATE_EMAIL) === false) {
        $errors['email'] = 'Введите корректный адрес электронной почты.';
    }

    /* --- Бизнес-правило: группа из allow-list --- */
    if (!in_array($values['group'], $allowedGroups, true)) {
        $errors['group'] = 'Выберите учебную группу из списка.';
    }

    /* --- Бизнес-правило: дисциплина из allow-list --- */
    if (!array_key_exists($values['discipline'], $allowedDisciplines)) {
        $errors['discipline'] = 'Выберите дисциплину из списка.';
    }

    /* --- Бизнес-правило: формат из allow-list --- */
    if (!in_array($values['format'], $allowedFormats, true)) {
        $errors['format'] = 'Выберите формат участия.';
    }

    /* --- Обязательный checkbox --- */
    if (!isset($_POST['agreement']) || $_POST['agreement'] !== '1') {
        $errors['agreement'] = 'Подтвердите согласие с правилами.';
    }

    $success = $errors === [];
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Регистрация на дисциплину</title>
    <style>
        body { font-family: sans-serif; max-width: 640px; margin: 2rem auto; padding: 0 1rem; }
        label { display: block; margin-bottom: .75rem; }
        input, select { padding: .4rem; width: 100%; box-sizing: border-box; }
        fieldset { margin-bottom: .75rem; }
        .error { color: #b00020; margin: .25rem 0 .75rem; }
        .success { color: #0a7a2f; font-weight: bold; }
        button { padding: .5rem 1rem; cursor: pointer; }
    </style>
</head>
<body>
<main>
    <h1>Регистрация на дисциплину</h1>

    <?php if ($success): ?>
        <p class="success">
            Заявка принята для <?= h($values['full_name']) ?>
            (<?= h($allowedDisciplines[$values['discipline']]) ?>, формат: <?= h($values['format']) ?>).
        </p>
    <?php else: ?>
        <form method="post" action="">
            <!-- ФИО -->
            <label>
                ФИО
                <input type="text" name="full_name" maxlength="100"
                       value="<?= h($values['full_name']) ?>" required>
            </label>
            <?php if (isset($errors['full_name'])): ?>
                <p class="error"><?= h($errors['full_name']) ?></p>
            <?php endif; ?>

            <!-- E-mail -->
            <label>
                E-mail
                <input type="email" name="email"
                       value="<?= h($values['email']) ?>" required>
            </label>
            <?php if (isset($errors['email'])): ?>
                <p class="error"><?= h($errors['email']) ?></p>
            <?php endif; ?>

            <!-- Группа -->
            <label>
                Группа
                <select name="group" required>
                    <option value="">Выберите группу</option>
                    <?php foreach ($allowedGroups as $item): ?>
                        <option value="<?= h($item) ?>"
                            <?= $values['group'] === $item ? 'selected' : '' ?>>
                            <?= h($item) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <?php if (isset($errors['group'])): ?>
                <p class="error"><?= h($errors['group']) ?></p>
            <?php endif; ?>

            <!-- Дисциплина -->
            <label>
                Дисциплина
                <select name="discipline" required>
                    <option value="">Выберите дисциплину</option>
                    <?php foreach ($allowedDisciplines as $key => $title): ?>
                        <option value="<?= h($key) ?>"
                            <?= $values['discipline'] === $key ? 'selected' : '' ?>>
                            <?= h($title) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <?php if (isset($errors['discipline'])): ?>
                <p class="error"><?= h($errors['discipline']) ?></p>
            <?php endif; ?>

            <!-- Формат -->
            <fieldset>
                <legend>Формат участия</legend>
                <?php foreach ($allowedFormats as $item): ?>
                    <label>
                        <input type="radio" name="format"
                               value="<?= h($item) ?>"
                               <?= $values['format'] === $item ? 'checked' : '' ?>>
                        <?= h($item) ?>
                    </label>
                <?php endforeach; ?>
            </fieldset>
            <?php if (isset($errors['format'])): ?>
                <p class="error"><?= h($errors['format']) ?></p>
            <?php endif; ?>

            <!-- Согласие -->
            <label>
                <input type="checkbox" name="agreement" value="1"
                    <?= (($_POST['agreement'] ?? '') === '1') ? 'checked' : '' ?>>
                Я согласен с правилами участия
            </label>
            <?php if (isset($errors['agreement'])): ?>
                <p class="error"><?= h($errors['agreement']) ?></p>
            <?php endif; ?>

            <button type="submit">Отправить</button>
        </form>
    <?php endif; ?>
</main>
</body>
</html>