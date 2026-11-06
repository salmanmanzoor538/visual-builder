<?php

namespace Tests\Unit;

use App\Builder\Blocks\HeadingBlock;
use App\Builder\Registry\BlockRegistry;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class BlockRegistryTest extends TestCase
{
    #[Test]
    public function it_resolves_registered_blocks(): void
    {
        $registry = (new BlockRegistry)->register(new HeadingBlock);
        $this->assertSame('heading', $registry->get('heading')->type());
    }

    #[Test]
    public function it_rejects_unknown_blocks(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new BlockRegistry)->get('script');
    }
}
