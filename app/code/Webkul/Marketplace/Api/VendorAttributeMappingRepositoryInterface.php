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
 * VendorAttributeMappingRepository Repository Interface
 */
interface VendorAttributeMappingRepositoryInterface
{
    /**
     * Get by id
     *
     * @param int $id
     * @return \Webkul\Marketplace\Model\VendorAttributeMapping
     */
    public function getById($id);
    /**
     * Save
     *
     * @param \Webkul\Marketplace\Model\VendorAttributeMapping $subject
     * @return \Webkul\Marketplace\Model\VendorAttributeMapping
     */
    public function save(\Webkul\Marketplace\Model\VendorAttributeMapping $subject);
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
     * @param \Webkul\Marketplace\Model\VendorAttributeMapping $subject
     * @return boolean
     */
    public function delete(\Webkul\Marketplace\Model\VendorAttributeMapping $subject);
    /**
     * Delete by id
     *
     * @param int $id
     * @return boolean
     */
    public function deleteById($id);
}
