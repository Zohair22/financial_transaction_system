<?php

namespace Modules\Plaid\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;
use Throwable;

/**
 * Thrown when Plaid returns an error payload or when the integration layer cannot process a response.
 */
class PlaidApiException extends RuntimeException
{
    public function __construct(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        public readonly ?string $plaidErrorCode = null,
        public readonly ?string $plaidErrorType = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @param  array<string, mixed>  $plaidErrorBody  Top-level or nested Plaid `error` object
     */
    public static function fromPlaidErrorArray(array $plaidErrorBody): self
    {
        $message = (string) ($plaidErrorBody['error_message'] ?? 'Plaid API error');
        $code = (string) ($plaidErrorBody['error_code'] ?? '');
        $type = isset($plaidErrorBody['error_type']) ? (string) $plaidErrorBody['error_type'] : null;

        return new self(
            $message.($code !== '' ? ' ('.$code.')' : ''),
            0,
            null,
            $code !== '' ? $code : null,
            $type,
        );
    }

    public static function fromThrowable(Throwable $e): self
    {
        return $e instanceof self
            ? $e
            : new self($e->getMessage(), (int) $e->getCode(), $e);
    }

    public function render(Request $request): JsonResponse
    {
        $status = $this->plaidErrorCode !== null ? 502 : 500;

        return response()->json([
            'message' => $this->getMessage(),
            'plaid_error_code' => $this->plaidErrorCode,
            'plaid_error_type' => $this->plaidErrorType,
        ], $status);
    }
}
