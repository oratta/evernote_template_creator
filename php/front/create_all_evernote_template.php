<?php
require_once __DIR__ . '/../EvernoteTemplateCreator.php';
$app = new EvernoteTemplateCreator();

echo "all template create start!\n";
$app->createAllTemplate();
echo "all template create end \n";