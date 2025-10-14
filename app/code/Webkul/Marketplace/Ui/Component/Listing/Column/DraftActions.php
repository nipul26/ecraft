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
namespace Webkul\Marketplace\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Webkul\Marketplace\Helper\Data as HelperData;

/**
 * Class Actions
 */
class DraftActions extends Column
{
    /**
     * Url path edit
     */
    public const URL_PATH_EDIT = 'marketplace/product/add';
    /**
     * Url path delete
     */
    public const URL_PATH_DELETE = 'marketplace/product/deletedraftproduct';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var HelperData
     */
    protected $mpHelper;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param HelperData $mpHelper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        HelperData $mpHelper,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->mpHelper = $mpHelper;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $draftProd = $this->mpHelper->getDraftProductCollection($item['entity_id']);
                $draftProdData = $this->mpHelper->jsonToArray($draftProd->getContent());
                $item[$this->getData('name')] = [
                    'edit' => [
                        'href' => $this->urlBuilder->getUrl(
                            static::URL_PATH_EDIT,
                            [
                                'set' => $draftProdData['set']??1,
                                'type' => $draftProdData['type']??"simple",
                                'draft_id' => $item['entity_id']
                            ]
                        ),
                        'label' => __('Edit')
                    ],
                    'delete' => [
                        'href' => $this->urlBuilder->getUrl(
                            static::URL_PATH_DELETE,
                            [
                                'draft_id' => $item['entity_id']
                            ]
                        ),
                        'label' => __('Delete')
                    ]
                ];
            }
        }

        return $dataSource;
    }
}
