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
 * CreditMemoListRepository Repository Interface
 */
interface CreditMemoListRepositoryInterface
{
    /**
     * Get by id
     *
     * @param int $id
     * @return \Webkul\Marketplace\Model\CreditMemoList
     */
    public function getById($id);
    /**
     * Save
     *
     * @param \Webkul\Marketplace\Model\CreditMemoList $subject
     * @return \Webkul\Marketplace\Model\CreditMemoList
     */
    public function save(\Webkul\Marketplace\Model\CreditMemoList $subject);
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
     * @param \Webkul\Marketplace\Model\CreditMemoList $subject
     * @return boolean
     */
    public function delete(\Webkul\Marketplace\Model\CreditMemoList $subject);
    /**
     * Delete by id
     *
     * @param int $id
     * @return boolean
     */
    public function deleteById($id);
}
