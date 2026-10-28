<?php

namespace App\Services\UsefulTools\BusinessData;

interface BusinessDataProviderInterface
{
    /**
     * Unique identifier for the provider (e.g., 'google_places', 'yelp').
     */
    public function getIdentifier(): string;

    /**
     * Human-friendly display label.
     */
    public function getName(): string;

    /**
     * Check if provider credentials / configuration are active.
     */
    public function isConfigured(): bool;

    /**
     * Diagnostic note or configuration requirements.
     */
    public function getStatusDescription(): string;

    /**
     * Collect business records based on search parameters.
     *
     * @param array $params [keyword, category, city, state, country, max_results]
     * @return array [
     *     'success' => bool,
     *     'records' => array, // List of normalized business attribute arrays
     *     'total_found' => int,
     *     'error' => ?string,
     *     'credits_used' => float,
     *     'cost_usd' => float,
     *     'metadata' => array
     * ]
     */
    public function searchBusinesses(array $params): array;
}
