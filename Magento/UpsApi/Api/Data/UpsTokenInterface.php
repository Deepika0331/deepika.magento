<?php

declare(strict_types=1);

namespace Meticulosity\UpsApi\Api\Data;

/**
 * Interface UpsTokenInterface
 * @package Meticulosity\UpsApi\Api\Data
 */
interface UpsTokenInterface
{
   

    const ID = 'id';
    const ISSUED_AT = 'issued_at';
    const ACCESS_TOKEN = 'access_token';
    const EXPIRES_IN = 'expires_in';
    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return string
     */
    public function getIssuedAt(): string;

    /**
     * @return string
     */
    public function getAccessToken(): string;

    /**
     * @return string
     */
    public function getExpiresIn(): string;
}
