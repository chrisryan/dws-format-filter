<?php
require_once __DIR__ . '/../FilterTestCase.php';

class ControlStructureWhiteSpaceTest extends FilterTestCase
{
    public function testSpaceAtBeginningOfIf()
    {
        $input = <<< 'NOWDOC'
<?php
if (true) {

    echo 'hi';
}

NOWDOC;
        $output = <<< 'NOWDOC'
<?php
if (true) {
    echo 'hi';
}

NOWDOC;
        $this->assertFormat($input, $output);
    }
}
