<?php

if (!function_exists('get_breadcrumbs')) {
    /**
     * Get all breadcrumbs current execution
     *
     * @return array<mixed>
     */
    function get_breadcrumbs(): array
    {
        return \MonoProcessor\Breadcrumbs::getInstance()->getBreadcrumbs();
    }
}
