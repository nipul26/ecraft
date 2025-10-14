<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Marketplace\Api;

/**
 * ProductRemarkRepository Repository Interface
 */
interface ProductRemarkRepositoryInterface
{
    /**
     * Get by id
     *
     * @param int $id
     * @return \Webkul\Marketplace\Model\ProductRemark
     */
    public function getById($id);
    /**
     * Save
     *
     * @param \Webkul\Marketplace\Model\ProductRemark $subject
     * @return \Webkul\Marketplace\Model\ProductRemark
     */
    public function save(\Webkul\Marketplace\Model\ProductRemark $subject);
    /**
     * Get list
     *
     * @param Magento\Framework\Api\SearchCriteriaInterface $creteria
     * @return Magento\Framework\Api\SearchResults
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $creteria);
    /**
     * Delete
     *
     * @param \Webkul\Marketplace\Model\ProductRemark $subject
     * @return boolean
     */
    public function delete(\Webkul\Marketplace\Model\ProductRemark $subject);
    /**
     * Delete by id
     *
     * @param int $id
     * @return boolean
     */
    public function deleteById($id);
}
