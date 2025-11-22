<?php
namespace CodezSparkMobile\WishlistApi\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Wishlist\Model\WishlistFactory;
use CodezSparkMobile\WishlistApi\Api\WishlistManagementInterface;
use CodezSparkMobile\WishlistApi\Api\Data\WishlistInterface;
use CodezSparkMobile\WishlistApi\Api\Data\WishlistItemInterface;
use Magento\Catalog\Helper\Product\Configuration;
use Magento\Catalog\Helper\Product\ConfigurationPool;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Webapi\Rest\Response;
use Magento\Wishlist\Helper\Data as WishlistHelper;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote\Item;

class WishlistManagement implements WishlistManagementInterface
{
    /**
     * @var WishlistFactory
     */
    private $wishlistFactory;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * @var Configuration
     */
    private $productHelper;

    /**
     * @var ConfigurationPool
     */
    private $configurationPool;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var WishlistHelper
     */
    protected $wishlistHelper;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var Item
     */
    protected $quoteItem;

    /**
     * WishlistManagement constructor.
     *
     * @param WishlistFactory $wishlistFactory
     * @param ProductRepositoryInterface $productRepository
     * @param ManagerInterface $eventManager
     * @param Configuration $productHelper
     * @param ConfigurationPool $configurationPool
     * @param StoreManagerInterface $storeManager
     * @param Response $response
     * @param WishlistHelper $wishlistHelper
     * @param CartRepositoryInterface $cartRepository
     * @param Item $quoteItem
     */
    public function __construct(
        WishlistFactory $wishlistFactory,
        ProductRepositoryInterface $productRepository,
        ManagerInterface $eventManager,
        Configuration $productHelper,
        ConfigurationPool $configurationPool,
        StoreManagerInterface $storeManager,
        Response $response,
        WishlistHelper $wishlistHelper,
        CartRepositoryInterface $cartRepository,
        Item $quoteItem,
    ) {
        $this->wishlistFactory = $wishlistFactory;
        $this->productRepository = $productRepository;
        $this->eventManager = $eventManager;
        $this->productHelper = $productHelper;
        $this->configurationPool = $configurationPool;
        $this->storeManager = $storeManager;
        $this->response = $response;
        $this->wishlistHelper = $wishlistHelper;
        $this->cartRepository = $cartRepository;
        $this->quoteItem = $quoteItem;
    }

    /**
     * Get wishlist for a customer
     *
     * @param int $customerId
     * @return \Magento\Wishlist\Model\Wishlist|WishlistInterface
     * @throws NoSuchEntityException
     */
    public function get(int $customerId)
    {
        $wishlist = $this->wishlistFactory->create()->loadByCustomerId($customerId);

        if (!$wishlist->getId()) {
           $data = [
                'status'  => false,
                'message' => __('Customer does not yet have a wishlist'),
                'data'   => []
            ];
            return $this->response->setBody(json_encode($data))->sendResponse();
        }

        $helperPool = $this->configurationPool;

        $storeUrl = $this->storeManager->getStore()->getBaseUrl();
        $mediaUrl = $storeUrl . '/pub/media/catalog/product/';

        $productItems = [];

        foreach ($wishlist->getItemCollection()->getItems() as $item) {
            $productId = $item->getProductId();

            try {
                $product = $this->productRepository->getById($productId);

                $helperInstance = match ($product->getTypeId()) {
                    'bundle' => \Magento\Bundle\Helper\Catalog\Product\Configuration::class,
                    'downloadable' => \Magento\Downloadable\Helper\Catalog\Product\Configuration::class,
                    default => \Magento\Catalog\Helper\Product\Configuration::class
                };

                $productItems[] = [
                    'wishlist_item_id' => $item->getId(),
                    'product_id' => $productId,
                    'name' => $product->getData('name'),
                    'price' => number_format($item->getProduct()->getFinalPrice() ?? 0, 2, '.', ''),
                    'sku' => $product->getSku(),
                    'qty' => $item->getQty(),
                    'special_price' => number_format($item->getProduct()->getSpecialPrice() ?? 0, 2, '.', ''),
                    'image' => $mediaUrl . $product->getData('image'),
                    'thumbnail' => $mediaUrl . $product->getData('thumbnail'),
                    'small_image' => $mediaUrl . $product->getData('small_image'),
                    'product_type' => $product->getTypeId(),
                    'product_options' => $this->getConfiguredOptions($item, $helperPool, $helperInstance)
                ];
            } catch (\Exception $e) {
                // Ignore errors for individual products
            }
        }
        $wishlist['items'] = $productItems;
        $data['status'] = true;
        $data['message'] = __('Customer wishlist retrieved successfully.');
        $data['data'] = $wishlist->getData();
        
        return $this->response->setBody(json_encode($data))->sendResponse();;
    }

    /**
     * Get configured options for a wishlist item
     *
     * @param mixed $item
     * @param mixed $helperPool
     * @param mixed $mainHelper
     * @return mixed
     */
    public function getConfiguredOptions($item, $helperPool, $mainHelper)
    {
        $helper = $helperPool->get($mainHelper);
        $options = $helper->getOptions($item);

        foreach ($options as $index => $option) {
            if (is_array($option) && array_key_exists('value', $option)) {
                if (!(array_key_exists('has_html', $option) && $option['has_html'] === true)) {
                    if (is_array($option['value'])) {
                        foreach ($option['value'] as $key => $value) {
                            $option['value'][$key] = $value;
                        }
                    }
                }
                $options[$index]['value'] = $option['value'];
            }
        }

        return $options;
    }

    /**
     * Add item to wishlist
     *
     * @param int $customerId
     * @param \CodezSparkMobile\WishlistApi\Api\Data\RequestInterface $item
     * @return \Magento\Wishlist\Model\Wishlist|\CodezSparkMobile\WishlistApi\Api\Data\WishlistInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function add(int $customerId, $item)
    {
        $wishlist = $this->wishlistFactory->create()->loadByCustomerId($customerId, true);
        $product = $this->productRepository->get($item->getSku());
        $customAttributes = $item->getCustomAttributes();
        $superAttributes = [];
        $bundleOptionQtys = [];
        $bundleOptions = [];
        if ($customAttributes) {

            foreach ($customAttributes as $customAttribute) {
                if (strpos($customAttribute->getAttributeCode(), 'super_attribute_') === 0) {
                    $superAttributeId = str_replace('super_attribute_', '', $customAttribute->getAttributeCode());
                    $superAttributes[$superAttributeId] = $customAttribute->getValue();
                }

                if (strpos($customAttribute->getAttributeCode(), 'bundle_option_qty_') === 0) {
                    $bundleOptionQty = str_replace('bundle_option_qty_', '', $customAttribute->getAttributeCode());
                    $bundleOptionQtys[$bundleOptionQty] = $customAttribute->getValue();
                    continue;
                }

                if (strpos($customAttribute->getAttributeCode(), 'bundle_option_') === 0) {
                    $bundleOption = str_replace('bundle_option_', '', $customAttribute->getAttributeCode());
                    $bundleOption = explode('_', $bundleOption);

                    if (count($bundleOption) === 1) {
                        $bundleOptions[$bundleOption[0]] = $customAttribute->getValue();
                    } elseif (count($bundleOption) === 2) {
                        $bundleOptions[$bundleOption[0]][$bundleOption[1]] = $customAttribute->getValue();
                    }
                    continue;
                }
            }
        }

        $buyRequest = new DataObject();
        if ($superAttributes) {
            $buyRequest->setData('super_attribute', $superAttributes);
        }
        if ($bundleOptionQtys) {
            $buyRequest->setData('bundle_option_qty', $bundleOptionQtys);
        }
        if ($bundleOptions) {
            $buyRequest->setData('bundle_option', $bundleOptions);
        }
        $buyRequest->setData('qty', $item->getQty());
        $result = $wishlist->addNewItem($product->getId(), $buyRequest);

        $this->wishlistHelper->calculate();
        $wishlist->save();

        $this->eventManager->dispatch(
            'wishlist_add_product',
            ['wishlist' => $wishlist, 'product' => $product, 'item' => $result]
        );

        $product = $result->getProduct();
        $wishlistObj = $result->getWishlist();

        $responseData = $result->getData();

        if ($product) {
            $responseData['product'] = $product->getData();
        }
        if ($wishlistObj) {
            $responseData['wishlist'] = $wishlistObj->getData();
        }

        $data['status'] = true;
        $data['message'] = __('Product added to wishlist successfully.');
        $data['data'] = $responseData;

        return $this->response
            ->setBody(json_encode($data))
            ->sendResponse();
    }

    /**
     * Update an item in the wishlist.
     *
     * @param int $customerId
     * @param int $itemId
     * @param WishlistItemInterface $item
     * @return WishlistInterface
     */
    public function update(int $customerId, int $itemId, WishlistItemInterface $item)
    {
        $wishlist = $this->wishlistFactory->create()->loadByCustomerId($customerId, true);

        $wishlistItem = $wishlist->getItem($itemId);
        if (!$wishlistItem) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __('Wishlist item with ID %1 not found', $itemId)
            );
        }

        $product = $this->productRepository->get($item->getSku());
        $customAttributes = $item->getCustomAttributes();

        $superAttributes = [];
        $bundleOptionQtys = [];
        $bundleOptions = [];

        if ($customAttributes) {
            foreach ($customAttributes as $customAttribute) {
                if (strpos($customAttribute->getAttributeCode(), 'super_attribute_') === 0) {
                    $superAttributeId = str_replace('super_attribute_', '', $customAttribute->getAttributeCode());
                    $superAttributes[$superAttributeId] = $customAttribute->getValue();
                }

                if (strpos($customAttribute->getAttributeCode(), 'bundle_option_qty_') === 0) {
                    $bundleOptionQty = str_replace('bundle_option_qty_', '', $customAttribute->getAttributeCode());
                    $bundleOptionQtys[$bundleOptionQty] = $customAttribute->getValue();
                    continue;
                }

                if (strpos($customAttribute->getAttributeCode(), 'bundle_option_') === 0) {
                    $bundleOption = str_replace('bundle_option_', '', $customAttribute->getAttributeCode());
                    $bundleOption = explode('_', $bundleOption);

                    if (count($bundleOption) === 1) {
                        $bundleOptions[$bundleOption[0]] = $customAttribute->getValue();
                    } elseif (count($bundleOption) === 2) {
                        $bundleOptions[$bundleOption[0]][$bundleOption[1]] = $customAttribute->getValue();
                    }
                    continue;
                }
            }
        }

        $buyRequest = new \Magento\Framework\DataObject();
        if ($superAttributes) {
            $buyRequest->setData('super_attribute', $superAttributes);
        }
        if ($bundleOptionQtys) {
            $buyRequest->setData('bundle_option_qty', $bundleOptionQtys);
        }
        if ($bundleOptions) {
            $buyRequest->setData('bundle_option', $bundleOptions);
        }

        $buyRequest->setData('qty', $item->getQty());
        $result = $wishlist->updateItem($itemId, $buyRequest);
        
        $wishlist->save();
        $this->wishlistHelper->calculate();

        $this->eventManager->dispatch(
            'wishlist_update_item',
            ['wishlist' => $wishlist, 'product' => $product, 'item' => $wishlist->getItem($itemId)]
        );

        $responseData = $result->getData();
        if ($product) {
            $responseData['product'] = $product->getData();
        }

        $data['status'] = true;
        $data['message'] = __('Product added to wishlist successfully.');
        $data['data'] = $responseData;
        
        return $this->response
            ->setBody(json_encode($data))
            ->sendResponse();
    }

    /**
     * Move item from quote to wishlist
     *
     * @param int $customerId
     * @param int $quoteId
     * @param int $itemId
     * @return WishlistInterface
     * @throws LocalizedException
     */
    public function move(int $customerId, int $quoteId, int $itemId)
    {
        $wishlist = $this->wishlistFactory->create()->loadByCustomerId($customerId, true);
        $buyRequest = new DataObject();
        $quote = $this->cartRepository->get($quoteId);
        $quoteItems = $quote->getAllVisibleItems();
        $status = false;
        $data = [];

        try {
            foreach ($quoteItems as $quoteItem) {
                $_productId = $quoteItem->getProductId();
                $product = $this->productRepository->getById($_productId);

                if (!$product->isVisibleInCatalog()) {
                    $data['status'] = false;
                    $data['message'] = __("Sorry, this item can't be added to wishlist");
                    $data['response'] = [];

                    return $this->response->setBody(json_encode($data))->sendResponse();
                }

                if ($quoteItem->getId() == $itemId) {
                    $buyRequest = $quoteItem->getBuyRequest();
                    $status = true;
                    break;
                }
            }

            if (!$status) {
                $data['status'] = false;
                $data['message'] = __("Cart item not found.");
                $data['response'] = [];

                return $this->response->setBody(json_encode($data))->sendResponse();
            }

            $options = $buyRequest->getOptions();
            if ($options) {
                foreach ($options as $key => $option) {
                    if (is_array($option) && isset($option['date_internal'])) {
                        unset($options[$key]);
                        $options[$key] = $option['date_internal'];
                    }
                }
                $buyRequest->setData('options', $options);
            }

            $result = $wishlist->addNewItem($product, $buyRequest);

            if (is_string($result)) {
                $data['status'] = false;
                $data['message'] = __($result);
                $data['response'] = [];

                return $this->response->setBody(json_encode($data))->sendResponse();
            }

            if ($wishlist->isObjectNew()) {
                $wishlist->save();
            }

            try {
                $quoteItem = $this->quoteItem->load($itemId);
                $quoteItem->delete();
            } catch (\Exception $e) {
                $data['status'] = false;
                $data['message'] = __("Unable to remove item from cart: %1", $e->getMessage());
                $data['response'] = [];
                return $this->response->setBody(json_encode($data))->sendResponse();
            }

            $this->eventManager->dispatch(
                'wishlist_add_product',
                ['wishlist' => $wishlist, 'product' => $product, 'item' => $result]
            );

            $product = $result->getProduct();
            $wishlistObj = $result->getWishlist();

            $responseData = $result->getData();

            if ($product) {
                $responseData['product'] = $product->getData();
            }
            if ($wishlistObj) {
                $responseData['wishlist'] = $wishlistObj->getData();
            }

            $data['status'] = true;
            $data['message'] = __('Product moved to wishlist successfully.');
            $data['response'] = $responseData;

            return $this->response
                ->setBody(json_encode($data))
                ->sendResponse();

        } catch (\Exception $e) {

            $data['status'] = false;
            $data['message'] = __("");
            $data['response'] = [];

            return $this->response->setBody(json_encode($data))->sendResponse();
        }
    }


    /**
     * Delete item from wishlist
     *
     * @param int $customerId
     * @param int $itemId
     * @return mixed
     */
    public function delete(int $customerId, int $itemId)
    {
        $data = [];

        try {
            $wishlist = $this->wishlistFactory->create()->loadByCustomerId($customerId);
            $item = $wishlist->getItem($itemId);

            if (!$item) {
                $data['status'] = false;
                $data['message'] = __('No wishlist item found with ID %1', $itemId);
                $data['response'] = [];

                return $this->response->setBody(json_encode($data))->sendResponse();
            }

            $item->delete();

            $data['status'] = true;
            $data['message'] = __('Wishlist item deleted successfully.');
            $data['response'] = [];

            return $this->response->setBody(json_encode($data))->sendResponse();

        } catch (\Exception $e) {

            $data['status'] = false;
            $data['message'] = __("Please try again later.");
            $data['response'] = [];

            return $this->response->setBody(json_encode($data))->sendResponse();
        }
    }
}
