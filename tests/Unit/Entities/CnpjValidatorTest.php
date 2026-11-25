<?php

declare(strict_types=1);

namespace Tests\Unit\Entities;

use App\Entities\CnpjValidator;
use PHPUnit\Framework\TestCase;

final class CnpjValidatorTest extends TestCase
{
    public function testAcceptsValidCnpjWithOrWithoutMask(): void
    {
        $this->assertTrue(CnpjValidator::isValid('11.222.333/0001-81'));
        $this->assertTrue(CnpjValidator::isValid('11222333000181'));
    }

    public function testRejectsCnpjWithWrongCheckDigits(): void
    {
        $this->assertFalse(CnpjValidator::isValid('11.222.333/0001-82'));
    }

    public function testRejectsCnpjWithAllDigitsEqual(): void
    {
        $this->assertFalse(CnpjValidator::isValid('11.111.111/1111-11'));
    }

    public function testRejectsCnpjWithWrongLength(): void
    {
        $this->assertFalse(CnpjValidator::isValid('123'));
    }
}
