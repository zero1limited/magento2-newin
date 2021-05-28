<?php
namespace Zero1\NewIn\Model\Rule\Condition\Product\SpecialAttribute;

use Smile\ElasticsuiteCatalogRule\Api\Rule\Condition\Product\SpecialAttributeInterface;
use Smile\ElasticsuiteCore\Search\Request\Query\QueryFactory;
use Smile\ElasticsuiteCore\Search\Request\QueryInterface;

class IsNewIn implements SpecialAttributeInterface
{
    /**
     * @var \Smile\ElasticsuiteCore\Search\Request\Query\QueryFactory
     */
    private $queryFactory;

    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    private $booleanSource;

    /**
     * HasImage constructor.
     *
     * @param \Smile\ElasticsuiteCore\Search\Request\Query\QueryFactory $queryFactory  Query Factory
     * @param \Magento\Config\Model\Config\Source\Yesno                 $booleanSource Boolean Source
     */
    public function __construct(QueryFactory $queryFactory, \Magento\Config\Model\Config\Source\Yesno $booleanSource)
    {
        $this->queryFactory  = $queryFactory;
        $this->booleanSource = $booleanSource;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeCode()
    {
        return 'is_new_in';
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQuery()
    {
        $query = $this->queryFactory->create(
            QueryInterface::TYPE_BOOL,
            [
                'must' => [
                    $this->queryFactory->create(
                        QueryInterface::TYPE_RANGE,
                        ['field' => 'news_to_date', 'bounds' => ['gte' => date("Y-m-d")]]
                    ),
                ],
            ]
        );
        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function getOperatorName()
    {
        return ' ';
    }

    /**
     * {@inheritdoc}
     */
    public function getInputType()
    {
        return 'select';
    }

    /**
     * {@inheritdoc}
     */
    public function getValueElementType()
    {
        return 'hidden';
    }

    /**
     * {@inheritdoc}
     */
    public function getValueName()
    {
        return ' ';
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getValueOptions()
    {
        return $this->booleanSource->toOptionArray();
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return __('New In (news_to_date is in future)');
    }
}
