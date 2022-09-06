<?php
namespace Zero1\NewIn\Model\Cron;

use \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use \Magento\CatalogInventory\Api\StockRegistryInterface;
use \Magento\Catalog\Model\Product\Attribute\Source\Status;

class SetDates
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistryInterface;

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository
     */
    public function __construct(
        CollectionFactory $productCollectionFactory,
        StockRegistryInterface $stockRegistryInterface
    ){
        $this->productCollectionFactory = $productCollectionFactory;
        $this->stockRegistryInterface = $stockRegistryInterface;
    }

    public function execute()
    {        
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addAttributeToSelect('*');

        $date = new \DateTime();
        $interval = new \DateInterval('P30D');
        $date->sub($interval); // - 30 days

        $productCollection->addFieldToFilter('created_at', array('from' => $date->format('Y-m-d H:i:s')));

        foreach($productCollection as $product) {
            if($product->getStatus() != Status::STATUS_ENABLED) {
                continue;
            }

            try {
                $stockItem = $this->stockRegistryInterface->getStockItem($product->getId());
                if(!$stockItem->getIsInStock()) {
                    continue;
                }
            } catch(\Exception $e) {
                continue;
            }

            if($product->getNewsToDate() === null) {
                $date = new \DateTime();
                $interval = new \DateInterval('P90D');
                $date->add($interval); // + 30 days

                try {
                    $product->setNewsToDate($date->format('Y-m-d H:i:s'));
                    $product->save();
                } catch(\Exception $e) {
                    continue;
                }
            }
        }
    }
}
