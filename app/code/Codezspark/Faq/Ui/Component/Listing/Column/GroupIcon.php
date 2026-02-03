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

namespace Codezspark\Faq\Ui\Component\Listing\Column;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class GroupIcon extends Column
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Repository
     */
    protected $assetRepo;

    /**
     * @var UrlInterface
     */
    protected $_backendUrl;

    /**
     * GroupIcon constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param StoreManagerInterface $storeManager
     * @param Repository $assetRepo
     * @param UrlInterface $backendUrl
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        StoreManagerInterface $storeManager,
        Repository $assetRepo,
        UrlInterface $backendUrl,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->storeManager = $storeManager;
        $this->assetRepo = $assetRepo;
        $this->_backendUrl = $backendUrl;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     * @throws NoSuchEntityException
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $path = $this->storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ) . 'faq/tmp/icon/';
            $baseImage = $this->assetRepo->getUrl('Codezspark_Faq::images/faq.png');
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if ($item[$fieldName]) {
                    $item[$fieldName . '_src'] = $path . $item['icon'];
                    $item[$fieldName . '_alt'] = $item['groupname'];
                    $item[$fieldName . '_orig_src'] = $path . $item['icon'];
                } else {
                    $item[$fieldName . '_src'] = $baseImage;
                    $item[$fieldName . '_alt'] = 'Faq';
                    $item[$fieldName . '_orig_src'] = $baseImage;
                }
                $item[$fieldName . '_link'] = $this->_backendUrl->getUrl(
                    "codezspark_faq/faqgroup/edit",
                    ['faqgroup_id' => $item['faqgroup_id']]
                );
            }
        }

        return $dataSource;
    }
}
