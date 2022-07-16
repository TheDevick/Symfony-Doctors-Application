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
}
