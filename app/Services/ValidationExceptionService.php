<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;

class ValidationExceptionService
{
    private ValidationException $exception;

    public function __construct(ValidationException $exception)
    {
        $this->exception = $exception;
    }

    public function prepareErorrsResponse(): array
    {
        $response = ['errors' => []];
        $errorsBag = $this->exception->errors();
        foreach ($errorsBag as $parameter => $errorMessages) {
            $response['errors'][] = $this->createErrorsFromMessages($parameter, $errorMessages);
        }

        return $response;
    }

    private function createErrorsFromMessages(string $parameter, array $errorMessages): array
    {
        return array_map(function ($message) use ($parameter) {
            
            return $parameter  . ': ' . $message;
            return [
                // 'status' => $this->exception->status,
                // 'source' => ['pointer' => $this->createPointer($parameter)],
                'detail' => $message
            ];
        }, $errorMessages);
    }

    private function createPointer(string $parameter): string
    {
        return '/' . str_replace('.', '/', $parameter);
    }
}
