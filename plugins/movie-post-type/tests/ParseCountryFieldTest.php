<?php

use PHPUnit\Framework\TestCase;

class ParseCountryFieldTest extends TestCase
{
    public function testEmptyString()
    {
        $this->assertSame([], parseCountryField(''));
    }

    public function testWhitespaceOnly()
    {
        $this->assertSame([], parseCountryField('   '));
    }

    public function testSingleCountry()
    {
        $this->assertSame(['argentina'], parseCountryField('Argentina'));
    }

    public function testSlashSeparated()
    {
        $this->assertSame(['argentina', 'chile'], parseCountryField('Argentina/Chile'));
    }

    public function testCommaSeparated()
    {
        $this->assertSame(['usa', 'uk'], parseCountryField('USA, UK'));
    }

    public function testDashSeparated()
    {
        $this->assertSame(['españa', 'méxico'], parseCountryField('España - México'));
    }

    public function testWhitespaceTrimming()
    {
        $this->assertSame(['argentina', 'brasil'], parseCountryField('  Argentina / Brasil  '));
    }

    public function testTripleSeparator()
    {
        $this->assertSame(['a', 'b', 'c'], parseCountryField('A/B,C'));
    }

    public function testUnicodeCountries()
    {
        $this->assertSame(['日本', '대한민국'], parseCountryField('日本/대한민국'));
    }
}
