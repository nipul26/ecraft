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
namespace Webkul\Marketplace\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Publish extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Constructor.
     *
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface       $urlBuilder
     * @param array              $components
     * @param array              $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                if ($item["is_publish"]) {
                    $item[$fieldName.'_html'] = '<span class="grid-severity-notice"><span>'.
                                                    __("Published").
                                                '</span></span>';
                } else {
                    $item[$fieldName.'_html'] = "<button class='button'><span>".__('Publish')."</span></button>";
                    $item[$fieldName.'_title'] = __('Are you sure you want to publish?');
                    $item[$fieldName.'_submitlabel'] = __('OK');
                    $item[$fieldName.'_cancellabel'] = __('Cancel');
                    $item[$fieldName.'_entityid'] = $item['entity_id'];
                    $item[$fieldName.'_formaction'] = $this->urlBuilder->getUrl('marketplace/news/publish');
                }
            }
        }
        return $dataSource;
    }
}
