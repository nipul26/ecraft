<?php
/**
 * CodezSpark
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the codezspark.com license that is
 * available through the world-wide-web at this URL:
 * https://codezspark.com/end-user-license-agreement
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    CodezSpark
 * @package     Codezspark_Faq
 * @copyright   Copyright (c) CodezSpark (https://codezspark.com/)
 * @license     https://codezspark.com/end-user-license-agreement
 */

namespace Codezspark\Faq\Model;

use Codezspark\Faq\Api\Data\FaqGroupInterface;
use Codezspark\Faq\Model\ResourceModel\FaqGroup as ResourceFaqGroup;
use Magento\Framework\Exception\CouldNotSaveException;
use Codezspark\Faq\Api\Data\FaqGroupSearchResultsInterfaceFactory;
use Codezspark\Faq\Model\ResourceModel\FaqGroup\CollectionFactory as FaqGroupCollectionFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SortOrder;
use Codezspark\Faq\Api\FaqGroupRepositoryInterface;

class FaqGroupRepository implements FaqGroupRepositoryInterface
{
    /**
     * @var \Codezspark\Faq\Model\ResourceModel\FaqGroup
     */
    protected $resource;

    /**
     * @var \Codezspark\Faq\Api\Data\FaqGroupSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var FaqGroupCollectionFactory
     */
    protected $faqGroupCollectionFactory;

    /**
     * @var FaqGroupFactory
     */
    protected $faqGroupFactory;

    /**
     * FaqGroupRepository constructor.
     * @param ResourceFaqGroup $resource
     * @param FaqGroupFactory $faqGroupFactory
     * @param FaqGroupCollectionFactory $faqGroupCollectionFactory
     * @param FaqGroupSearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        ResourceFaqGroup $resource,
        FaqGroupFactory $faqGroupFactory,
        FaqGroupCollectionFactory $faqGroupCollectionFactory,
        FaqGroupSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->resource = $resource;
        $this->faqGroupFactory = $faqGroupFactory;
        $this->faqGroupCollectionFactory = $faqGroupCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(FaqGroupInterface $faqGroup)
    {
        try {
            $this->resource->save($faqGroup);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the faqGroup: %1',
                $exception->getMessage()
            ));
        }
        return $faqGroup;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($faqGroupId)
    {
        $faqGroup = $this->faqGroupFactory->create();
        $this->resource->load($faqGroup, $faqGroupId);
        if (!$faqGroup->getId()) {
            throw new NoSuchEntityException(__('FaqGroup with id "%1" does not exist.', $faqGroupId));
        }
        return $faqGroup;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->faqGroupCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }

        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(FaqGroupInterface $faqGroup)
    {
        try {
            $this->resource->delete($faqGroup);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the FaqGroup: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($faqGroupId)
    {
        return $this->delete($this->getById($faqGroupId));
    }
}
