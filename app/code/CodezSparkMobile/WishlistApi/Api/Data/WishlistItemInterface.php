<?php
declare(strict_types=1);

namespace CodezSparkMobile\WishlistApi\Api\Data;

use Magento\Framework\Api\AttributeInterface;

interface WishlistItemInterface
{
    /**
     * Get item ID.
     *
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * Set item ID.
     *
     * @param int $id
     * @return $this
     */
    public function setId(int $id);

    /**
     * Get product SKU.
     *
     * @return string
     */
    public function getSku(): string;

    /**
     * Set product SKU.
     *
     * @param string $sku
     * @return $this
     */
    public function setSku(string $sku);

    /**
     * Get customer ID.
     *
     * @return int
     */
    public function getCustomerId(): int;

    /**
     * Set customer ID.
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId(int $customerId);

    /**
     * Get quantity.
     *
     * @return float
     */
    public function getQty(): float;

    /**
     * Set quantity.
     *
     * @param float $qty
     * @return $this
     */
    public function setQty(float $qty);


    /**
     * Get custom attributes.
     *
     * @return mixed|null
     */
    public function getCustomAttributes();

    /**
     * Set custom attributes.
     *
     * @param mixed|null $attributes
     * @return $this
     */
    public function setCustomAttributes(string $attributes);
}
