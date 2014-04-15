<?php
use \mageekguy\atoum;

$runner->addTestsFromDirectory(__DIR__.'/Tests/Units');

$script
    ->addDefaultReport()
        ->addField(new atoum\report\fields\runner\result\logo())
        ->addField(new atoum\report\fields\runner\coverage\html(
                'Couverture de code',
                __DIR__.'/web/code-coverage'
            )
        )
;

$script->noCodeCoverageForNamespaces('mageekguy', 'Symfony', 'Doctrine');
$script->bootstrapFile(__DIR__ . DIRECTORY_SEPARATOR . '.atoum.bootstrap.php');
