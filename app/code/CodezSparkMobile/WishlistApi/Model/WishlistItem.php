<?php

namespace CodezSparkMobile\WishlistApi\Model;

use CodezSparkMobile\WishlistApi\Api\Data\WishlistItemInterface;
use Magento\Framework\Api\AttributeInterface;

class WishlistItem implements WishlistItemInterface
{
    /**
     * @var int|null
     */
    private ?int $id = null;

    /**
     * @var string|null
     */
    private ?string $sku = null;

    /**
     * @var int|null
     */
    private int $customerId = 0;

    /**
     * @var float|null
     */
    private float $qty = 1.0;

    /**
     * @var mixed|null
     */
    private mixed $customAttributes = null;

    /**
     * Retrieve wishlist item ID.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set wishlist item ID.
     *
     * @param int $id
     * @return $this
     */
    public function setId(int $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Retrieve product SKU.
     *
     * @return string|null
     */
    public function getSku(): string
    {
        return $this->sku;
    }

    /**
     * Set product SKU.
     *
     * @param string $sku
     * @return $this
     */
    public function setSku(string $sku)
    {
        $this->sku = $sku;
        return $this;
    }

    /**
     * Retrieve customer ID.
     *
     * @return int
     */
    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    /**
     * Set customer ID.
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId(int $customerId)
    {
        $this->customerId = $customerId;
        return $this;
    }

    /**
     * Retrieve quantity.
     *
     * @return float
     */
    public function getQty(): float
    {
        return $this->qty;
    }

    /**
     * Set quantity.
     *
     * @param float $qty
     * @return $this
     */
    public function setQty(float $qty)
    {
        $this->qty = $qty;
        return $this;
    }

    /**
     * Retrieve custom attributes.
     *
     * @return mixed|null
     */
    public function getCustomAttributes(): mixed
    {
        return $this->customAttributes;
    }

    /**
     * Set custom attributes.
     *
     * @param mixed $attributes
     * @return $this
     */
    public function setCustomAttributes(mixed $attributes)
    {
        $this->customAttributes = $attributes;
        return $this;
    }
}
