<?php

namespace CodezSparkMobile\WishlistApi\Api\Data;

use CodezSparkMobile\WishlistApi\Api\Data\WishlistItemInterface;

interface WishlistInterface
{
    /**
     * Get wishlist ID.
     *
     * @return int
     */
    public function getId();

    /**
     * Set wishlist ID.
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get customer ID.
     *
     * @return int
     */
    public function getCustomerId();

    /**
     * Set customer ID.
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Get wishlist (group) name.
     *
     * @return string
     */
    public function getName();

    /**
     * Set wishlist (group) name.
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get store ID.
     *
     * @return int
     */
    public function getStoreId();

    /**
     * Set store ID.
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * Get wishlist items.
     *
     * @return WishlistItemInterface[]
     */
    public function getItems();

    /**
     * Set wishlist items.
     *
     * @param WishlistItemInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
