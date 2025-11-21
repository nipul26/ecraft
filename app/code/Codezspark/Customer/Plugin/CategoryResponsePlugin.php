<?php

namespace Codezspark\Customer\Plugin;

use Magento\Catalog\Api\CategoryManagementInterface;
use Magento\Framework\Webapi\Rest\Response;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class CategoryResponsePlugin
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @param Response $response
     * @param LoggerInterface $logger
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        Response $response,
        LoggerInterface $logger,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->response = $response;
        $this->logger = $logger;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * After plugin to modify the category response format
     *
     * @param CategoryManagementInterface $subject
     * @param $result
     * @param int|null $rootCategoryId
     * @param int|null $depth
     * @return mixed
     */
    public function afterGetTree(
        CategoryManagementInterface $subject,
        $result,
        $rootCategoryId = null, 
        $depth = null
    ) {
        try {
            if ($result) {
                // Use the DataObjectProcessor to convert the category object into an array
                $dataArray = $this->dataObjectProcessor->buildOutputDataArray(
                    $result,
                    \Magento\Catalog\Api\Data\CategoryTreeInterface::class
                );
                
                $response = [
                    'status' => true,
                    'message' => 'Category list fetched successfully.',
                    'response' => [
                        'data' => $dataArray 
                    ]
                ];
            } else {
                $response = [
                    'status' => false,
                    'message' => 'Unable to retrieve category data',
                    'response' => [
                        'data' => null
                    ]
                ];
            }
        } catch (LocalizedException $e) {
             $this->logger->error('Category Response Plugin : ' . $e->getMessage());
        }

        return $this->response->setBody(json_encode($response))->sendResponse();
    }
}