<?php
namespace Zero1\NewIn\Model\Cron;

use \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class SetDates
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     */
    public function __construct(
        CollectionFactory $productCollectionFactory
    ){
        $this->productCollectionFactory = $productCollectionFactory;
    }

    public function execute()
    {        
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addAttributeToSelect('*');

        foreach($productCollection as $product) {
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