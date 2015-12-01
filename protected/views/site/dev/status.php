<?php
$status = array(
    'tasks' => array(
        'total' => Executor::model()->count(),
        'done' => Executor::model()->count('status = \'' . Executor::STATUS_DONE . '\''),
    ),
    'pending' => Keyword::model()->count('status = \'' . Keyword::STATUS_PENDING . '\''),
    'alexa' => filemtime(Yii::app()->basePath . '/../uploads/alexa/top-1m.csv'),
);
?>

<ul class="list-group">
    <li class="list-group-item <?= $status['tasks']['done'] == $status['tasks']['total'] ? 'list-group-item-success' : 'list-group-item-warning' ?>">
        <span class="badge"><?= $status['tasks']['done'] ?> / <?= $status['tasks']['total'] ?></span>
        Tasks done
    </li>
    <li class="list-group-item <?= $status['pending'] < 10 ? 'list-group-item-success' : ($status['pending'] < 20 ? 'list-group-item-warning' : 'list-group-item-danger') ?>">
        <span class="badge"><?= $status['pending'] ?></span>
        Pending keywords
    </li>
    <li class="list-group-item <?= $status['alexa'] < time() + Time::SECONDS_IN_DAY ? 'list-group-item-success' : ($status['alexa'] < time() + Time::SECONDS_IN_WEEK ? 'list-group-item-warning' : 'list-group-item-danger') ?>">
        <span class="badge"><?= date(Time::FORMAT_PRETTY, $status['alexa']) ?></span>
        Alexa Rankings file updated on
    </li>
</ul>