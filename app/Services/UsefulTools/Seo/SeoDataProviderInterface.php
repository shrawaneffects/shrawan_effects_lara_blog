<?php

namespace App\Services\UsefulTools\Seo;

interface SeoDataProviderInterface
{
    public function getIdentifier(): string;

    public function getName(): string;

    public function isConfigured(): bool;

    public function getStatusDescription(): string;
}
