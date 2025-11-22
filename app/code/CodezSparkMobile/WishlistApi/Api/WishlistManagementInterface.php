<?php
namespace CodezSparkMobile\WishlistApi\Api;

use CodezSparkMobile\WishlistApi\Api\Data\WishlistInterface;
use CodezSparkMobile\WishlistApi\Api\Data\WishlistItemInterface;
use Magento\Wishlist\Model\Wishlist;

interface WishlistManagementInterface
{
    /**
     * Get wishlist items for a customer.
     *
     * @param int $customerId
     * @return Wishlist|WishlistInterface
     */
    public function get(
        int $customerId
    );

    /**
     * Add an item to the wishlist.
     *
     * @param int $customerId
     * @param WishlistItemInterface $item
     * @return WishlistInterface
     */
    public function add(
        int $customerId,
        $item
    );

    /**
     * Update an item in the wishlist.
     *
     * @param int $customerId
     * @param int $itemId
     * @param WishlistItemInterface $item
     * @return WishlistInterface
     */
    public function update(int $customerId, int $itemId, WishlistItemInterface $item);

    /**
     * Move an item from wishlist to cart.
     *
     * @param int $customerId
     * @param int $quoteId
     * @param int $itemId
     * @return WishlistInterface
     */
    public function move(int $customerId, int $quoteId, int $itemId);


    /**
     * Delete an item from the wishlist.
     *
     * @param int $customerId
     * @param int $itemId
     * @return mixed
     */
    public function delete(
        int $customerId,
        int $itemId
    );
}
