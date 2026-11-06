<?php

namespace Tests\Unit;

use App\Builder\Rendering\StyleCompiler;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class StyleCompilerTest extends TestCase
{
    #[Test]
    public function it_only_compiles_allowed_properties(): void
    {
        $css = (new StyleCompiler)->compile(['fontSize' => 24, 'color' => '#111827', 'position' => 'fixed', 'bad' => 'x;display:none']);
        $this->assertSame('font-size:24px;color:#111827', $css);
    }
}
