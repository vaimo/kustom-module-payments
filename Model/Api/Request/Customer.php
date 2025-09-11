<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Api\Request;

use Klarna\Kp\Model\Api\Export;

/**
 * @internal
 */
class Customer implements CustomerInterface
{
    use Export;

    /**
     * @var string
     */
    private string $dob = '';
    /**
     * @var string
     */
    private string $gender = '';
    /**
     * @var string
     */
    private string $type = '';
    /**
     * @var string
     */
    private string $klarna_access_token = '';

    /**
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
                $this->exports[] = $key;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function setDateOfBirth(string $dob): void
    {
        $this->dob = $dob;
    }

    /**
     * @inheritdoc
     */
    public function setGender(string $gender): void
    {
        $this->gender = $gender;
    }

    /**
     * @inheritdoc
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * Get customer type
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type ?? '';
    }

    /**
     * @inheritdoc
     */
    public function setKlarnaAccessToken(string $klarnaAccessToken): void
    {
        $this->klarna_access_token = $klarnaAccessToken;
    }
}
