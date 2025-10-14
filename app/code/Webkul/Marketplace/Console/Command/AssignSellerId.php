<?php
declare(strict_types=1);
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Marketplace\Console\Command;

use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Webkul\Marketplace\Model\Attribute\Backend\SellerIdAttribute;
use Symfony\Component\Console\Helper\ProgressBar;

class AssignSellerId extends Command
{
    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;
    /**
     * @var \Webkul\Marketplace\Model\Product
     */
    protected $sellerProduct;
    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilder;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteria;

    /**
     * Construct function
     *
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Webkul\Marketplace\Model\ProductFactory $sellerProduct
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteria
     */
    public function __construct(
        \Magento\Framework\App\State $appState,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Webkul\Marketplace\Model\ProductFactory $sellerProduct,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteria
    ) {
        $this->appState = $appState;
        $this->productRepository = $productRepository;
        $this->sellerProduct = $sellerProduct;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteria = $searchCriteria;
        parent::__construct();
    }
    /**
     * Configure
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('assign:mp:product');
        $this->setDescription('Assign seller id on already created products to Admin / Sellers.');
        parent::configure();
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $exitCode = 0;
            $this->setAreaCode();
            $output->writeln('<info>Products are assigning to Admin / Sellers.</info>');
            $output->writeln('<comment>Please wait...</comment>');
            $this->searchCriteria = $this->searchCriteria->create();
            $products = $this->productRepository->getList($this->searchCriteria)->getItems();
            $oldProducts = [];
            $steps = count($products);
            $progressBar = new ProgressBar($output, $steps);
            $progressBar->setBarWidth(50);
            $progressBar->setFormat('verbose');
            $progressBar->setProgressCharacter('<info>➤</info>');
            $progressBar->setBarCharacter('<info>=</info>');
            $sellerProdCollection = $this->sellerProduct->create()
            ->getCollection()
            ->addFieldToSelect(["mageproduct_id", "seller_id"]);
            if ($sellerProdCollection->getSize()) {
                foreach ($sellerProdCollection as $product) {
                    $oldProducts[$product["mageproduct_id"]] = $product->getSellerId();
                }
            }
            $progressBar->start();
            foreach ($products as $product) {
                if (empty($product->getSellerId())) {
                    $assignProduct = !empty($oldProducts[$product->getId()])?$oldProducts[$product->getId()]:0;
                    $product->setCustomAttribute(
                        SellerIdAttribute::SELLER_PRODUCT_ATTRIBUTE,
                        $assignProduct
                    );
                    $this->productRepository->save($product);
                    $progressBar->advance();
                }
            }
            $progressBar->finish();
        } catch (LocalizedException $e) {
            $output->writeln(sprintf(
                '<error>%s</error>',
                $e->getMessage()
            ));
            $exitCode = 1;
        }
        return $exitCode;
    }
    /**
     * Set areacode if not set
     *
     * @return void
     */
    public function setAreaCode()
    {
        try {
            $this->appState->getAreaCode();
        } catch (\Exception $e) {
            $this->appState->setAreaCode("adminhtml");
        }
    }
}
