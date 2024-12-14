<?php

return [
    'defaults' => [
        'sorts' => '-created_at', // Default sorting (e.g., by created_at column in descending order)
        'max_results' => 100, // Maximum number of results to return in a single query
        'per_page' => 20, // Default items per page for pagination
    ],

    'namespace' => '', // Namespace for custom filters and sorts (if applicable)

    'column' => [
        'prefix' => '', // Prefix for column names, e.g., table names
        'virtual_key' => 'virtual', // Virtual key name for defining virtual columns
    ],

    'params' => [
        'include' => 'include', // Include parameter for eager loading related models
        'filter' => 'filter', // Filter parameter for applying filters to queries
        'sort' => 'sort', // Sort parameter for defining sorting options
        'fields' => 'fields', // Fields parameter for selecting specific fields to retrieve
        'page' => 'page', // Page parameter for pagination
    ],

    'pagination' => [
        'max_limit' => 100, // Maximum number of items per page for pagination
    ],

    'filter' => [
        // Define filters and their configuration here
    ],

    'sort' => [
        // Define sorting options and their configuration here
    ],
];