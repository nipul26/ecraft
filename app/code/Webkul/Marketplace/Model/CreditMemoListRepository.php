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
namespace Webkul\Marketplace\Model;

/**
 * CreditMemoListRepository Repo Class
 */
class CreditMemoListRepository implements \Webkul\Marketplace\Api\CreditMemoListRepositoryInterface
{
    /**
     * @var \Webkul\Marketplace\Model\CreditMemoListFactory
     */
    protected $modelFactory = null;
    /**
     * @var \Webkul\Marketplace\Model\ResourceModel\CreditMemoList\CollectionFactory
     */
    protected $collectionFactory = null;

    /**
     * Initialize
     *
     * @param \Webkul\Marketplace\Model\CreditMemoListFactory $modelFactory
     * @param \Webkul\Marketplace\Model\ResourceModel\CreditMemoList\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Webkul\Marketplace\Model\CreditMemoListFactory $modelFactory,
        \Webkul\Marketplace\Model\ResourceModel\CreditMemoList\CollectionFactory $collectionFactory
    ) {
        $this->modelFactory = $modelFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Get by id
     *
     * @param int $id
     * @return \Webkul\Marketplace\Model\CreditMemoList
     */
    public function getById($id)
    {
        $model = $this->modelFactory->create()->load($id);
        if (!$model->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __('The data with the "%1" ID doesn\'t exist.', $id)
            );
        }
        return $model;
    }

    /**
     * Save
     *
     * @param \Webkul\Marketplace\Model\CreditMemoList $subject
     * @return \Webkul\Marketplace\Model\CreditMemoList
     */
    public function save(\Webkul\Marketplace\Model\CreditMemoList $subject)
    {
        try {
            $subject->save();
        } catch (\Exception $exception) {
             throw new \Magento\Framework\Exception\CouldNotSaveException(__($exception->getMessage()));
        }
        return $subject;
    }

    /**
     * Get list
     *
     * @param Magento\Framework\Api\SearchCriteriaInterface $creteria
     * @return Magento\Framework\Api\SearchResults
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $creteria)
    {
        $collection = $this->collectionFactory->create();
        return $collection;
    }

    /**
     * Delete
     *
     * @param \Webkul\Marketplace\Model\CreditMemoList $subject
     * @return boolean
     */
    public function delete(\Webkul\Marketplace\Model\CreditMemoList $subject)
    {
        try {
            $subject->delete();
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete by id
     *
     * @param int $id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }
}
