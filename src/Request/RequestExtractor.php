<?php

namespace App\Request;

class RequestExtractor
{
    public function __construct(
        private Request $request
    ) {
    }

    public function extractSort(array $default = null): array
    {
        if (is_null($default)) {
            $default = ['id' => 'ASC'];
        }

        $sortParameter = $this->request->getParameterBody('Sort', $default);

        if (is_array($sortParameter)) {
            return array_change_key_case($sortParameter, CASE_LOWER);
        }

        return $default;
    }

    public function extractFilter(): array
    {
        $filterParameter = $this->request->getParameterBody('Filter', false);

        if (is_array($filterParameter)) {
            return array_change_key_case($filterParameter, CASE_LOWER);
        }

        return [];
    }

    public function extractPage(int $default = 1): int
    {
        $page = $this->request->query->get('page');

        if (is_null($page)) {
            $page = $default;
        }

        return $page;
    }

    public function extractLimit(int $limitOfLimit = 50, $default = 5): int
    {
        $limit = $this->request->query->get('limit');

        if (is_null($limit)) {
            $limit = $default;
        }

        $limit = (int) $limit;

        if ($limit > $limitOfLimit) {
            $limit = $limitOfLimit;
        }

        return $limit;
    }
}
