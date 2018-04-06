<?php

namespace luya\addressbook\tests\admin;

use luya\addressbook\tests\AddressbookTestCase;
use luya\testsuite\traits\MigrationFileCheckTrait;

class MigrationFilesTest extends AddressbookTestCase
{
    use MigrationFileCheckTrait;

    public function testMigrationFiles()
    {
        $this->checkMigrationFolder("@addressbookadmin/migrations");
    }
}

