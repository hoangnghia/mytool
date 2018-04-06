<?php

namespace cmstests\src\frontend\blocks;

use luya\bootstrap3\tests\Bootstrap3BlockTestCase;

class SpacingBlockTest extends Bootstrap3BlockTestCase
{
    public $blockClass = 'luya\bootstrap3\blocks\SpacingBlock';
    
    public function testEmptyRender()
    {
        $this->assertSame('<p class="spacing-block spacing-block--1"><br></p>', $this->renderFrontendNoSpace());
    }

    public function testSetSpace1()
    {
        $this->block->setVarValues(['spacing' => 1]);
        $this->assertSame('<p class="spacing-block spacing-block--1"><br></p>', $this->renderFrontendNoSpace());
    }
    
    public function testSetSpace2()
    {
        $this->block->setVarValues(['spacing' => 2]);
        $this->assertSame('<p class="spacing-block spacing-block--2"><br><br></p>', $this->renderFrontendNoSpace());
    }
    
    public function testSetSpace3()
    {
        $this->block->setVarValues(['spacing' => 3]);
        $this->assertSame('<p class="spacing-block spacing-block--3"><br><br><br></p>', $this->renderFrontendNoSpace());
    }
}
