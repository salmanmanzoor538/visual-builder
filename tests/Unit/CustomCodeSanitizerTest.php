<?php

namespace Tests\Unit;

use App\Builder\Security\CustomCodeSanitizer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CustomCodeSanitizerTest extends TestCase
{
    #[Test]
    public function it_removes_active_content_and_event_handlers(): void
    {
        $clean = (new CustomCodeSanitizer)->html('<div onclick="steal()"><script>alert(1)</script><a href="javascript:bad()">Safe</a></div>');
        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringNotContainsString('<script', $clean);
        $this->assertStringNotContainsString('javascript:', $clean);
        $this->assertStringContainsString('Safe', $clean);
    }
}
