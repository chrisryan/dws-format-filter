<?php
require_once __DIR__ . '/../FilterTestCase.php';

class VariableVariablesTest extends FilterTestCase
{
    public function testEncapsulatedStringVarVar()
    {
        $input = <<< 'NOWDOC'
<?php
echo ${ "bar" };

NOWDOC;
        $output = <<< 'NOWDOC'
<?php
echo ${"bar"};

NOWDOC;
        $this->assertFormat($input, $output);
    }

    public function testEncapsulatedObjectReferenceStringVarVar()
    {
        $input = <<< 'NOWDOC'
<?php
echo $bar->{ "{$foo}" };

NOWDOC;
        $output = <<< 'NOWDOC'
<?php
echo $bar->{"{$foo}"};

NOWDOC;
        $this->assertFormat($input, $output);
    }
}
