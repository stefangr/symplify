<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenFuncCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\ForbiddenFuncCallRule;

final class ForbiddenFuncCallRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        $errorMessage = sprintf(ForbiddenFuncCallRule::ERROR_MESSAGE, 'dump');
        yield [__DIR__ . '/Fixture/DebugFuncCall.php', [[$errorMessage, 11]]];

        $errorMessage = sprintf(ForbiddenFuncCallRule::ERROR_MESSAGE, 'extract');
        yield [__DIR__ . '/Fixture/ExtractCall.php', [[$errorMessage, 11]]];
    }

    protected function getRule(): Rule
    {
        return new ForbiddenFuncCallRule(['extract', 'dump']);
    }
}
