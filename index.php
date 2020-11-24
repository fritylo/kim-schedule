<?php
use Pug\Facade as PugFacade;

require './vendor/autoload.php';

$html = PugFacade::renderFile('index.pug');
echo $html;
?>