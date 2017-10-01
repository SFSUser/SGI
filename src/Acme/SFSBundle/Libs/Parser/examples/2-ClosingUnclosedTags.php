<?php
require_once "../Parser.php";

$parser = new JBBCode\Parser();
$parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());

$text = "The bbcode in here [b]is never closed!";
$parser->parse($text);

print $parser->getAsBBCode();
