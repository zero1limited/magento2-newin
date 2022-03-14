<?php
namespace Zero1\NewIn\Model\Cron;

use \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use \Magento\CatalogInventory\Model\Stock\StockItemRepository;
use \Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\Exception\NoSuchEntityException;

class SetDates
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\StockItemRepository
     */
    protected $stockItemRepository;

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository
     */
    public function __construct(
        CollectionFactory $productCollectionFactory,
        StockItemRepository $stockItemRepository
    ){
        $this->productCollectionFactory = $productCollectionFactory;
        $this->stockItemRepository = $stockItemRepository;
    }

    public function execute()
    {        
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addAttributeToSelect('*');

        foreach($productCollection as $product) {
            if($product->getStatus() != Status::STATUS_ENABLED) {
                continue;
            }

            try {
                $stockItem = $this->stockItemRepository->get($product->getId());
            } catch(NoSuchEntityException $e) {
                continue;
            }

            if(!$stockItem->getIsInStock()) {
                continue;
            }

            if($product->getNewsToDate() === null) {
                $date = new \DateTime();
                $interval = new \DateInterval('P30D');
                $date->add($interval); // + 30 days

                $product->setNewsToDate($date->format('Y-m-d H:i:s'));
                $product->save();
            }
        }
    }
}