<?php
namespace Yllumi\HeroicWebman;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class FrontendRoute {
    public function __construct(
        public string $route = '',
        public string $template = '',
        public bool $preload = false,
        public array $handler = []
    ) {}
}