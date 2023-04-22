<?php

declare(strict_types=1);

include_once __DIR__ . '/stubs/Validator.php';

class LibraryTest extends TestCaseSymconValidation
{
    public function testValidateLibrary(): void
    {
        $this->validateLibrary(__DIR__ . '/..');
    }

    public function testValidateDisableControl(): void
    {
        $this->validateModule(__DIR__ . '/../DisableControl');
    }

    public function testValidateHideControl(): void
    {
        $this->validateModule(__DIR__ . '/../HideControl');
    }

    public function testValidateLinkDisableControl(): void
    {
        $this->validateModule(__DIR__ . '/../LinkDisableControl');
    }

    public function testValidateLinkHideControl(): void
    {
        $this->validateModule(__DIR__ . '/../LinkHideControl');
    }
}