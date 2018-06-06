<?php

namespace What3Words\What3Words\Model;

/** What3Words Extension classes */
use What3Words\What3Words\Api\OrderRepositoryInterface;
use What3Words\What3Words\Api\Data\OrderInterface;
use What3Words\What3Words\Api\Data\OrderInterfaceFactory;
use What3Words\What3Words\Model\ResourceModel\Order;
use What3Words\What3Words\Model\ResourceModel\Order\Collection as OrderCollection;
use What3Words\What3Words\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

/** Exception classes */
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Exception\StateException;

/** Search classes */
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SearchResultsInterfaceFactory;

/**
 * Class OrderRepository
 * @package What3Words\What3Words\Model
 * @author Vicki Tingle
 */
class OrderRepository implements OrderRepositoryInterface
{
    /** @var Order  */
    protected $orderResource;

    /** @var OrderCollection  */
    protected $orderCollection;

    /** @var OrderInterfaceFactory  */
    protected $orderFactory;

    /** @var OrderCollectionFactory  */
    protected $collectionFactory;

    /** @var SearchResultsInterfaceFactory  */
    protected $searchResultsFactory;

    /** @var SearchCriteriaBuilder  */
    protected $searchCriteriaBuilder;

    /** @var array  */
    protected $instances = array();

    /**
     * OrderRepository constructor.
     * @param Order $orderResource
     * @param OrderCollection $orderCollection
     * @param OrderInterfaceFactory $orderInterfaceFactory
     * @param OrderCollectionFactory $collectionFactory
     * @param SearchResultsInterfaceFactory $searchResultsInterfaceFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        Order $orderResource,
        OrderCollection $orderCollection,
        OrderInterfaceFactory $orderInterfaceFactory,
        OrderCollectionFactory $collectionFactory,
        SearchResultsInterfaceFactory $searchResultsInterfaceFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->orderResource = $orderResource;
        $this->orderCollection = $orderCollection;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsInterfaceFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->orderFactory = $orderInterfaceFactory;
    }

    /**
     * @param OrderInterface $orderItem
     * @return OrderInterface
     * @throws CouldNotSaveException
     */
    public function save(OrderInterface $orderItem)
    {
        try {
            $this->orderResource->save($orderItem);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__(
                'Could not save the order item: %1',
                $e->getMessage()
            ));
        }
        return $orderItem;
    }

    /**
     * @param int $orderItemId
     * @return OrderInterface
     * @throws NoSuchEntityException
     */
    public function getById($orderItemId)
    {
        try {
            $orderItem = $this->orderFactory->create();
            $this->orderResource->load($orderItem, $orderItemId);
        } catch(\Exception $exception) {
            throw new NoSuchEntityException(__(
                'Could not load the order item : %1',
                $exception->getMessage()
            ));
        }
        return $orderItem;
    }

    /**
     * @param OrderInterface $orderItem
     * @return bool
     * @throws CouldNotSaveException
     * @throws StateException
     */
    public function delete(OrderInterface $orderItem)
    {
        $id = $orderItem->getEntityId();
        try {
            unset($this->instances[$id]);
            $this->orderResource->delete($orderItem);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove order item with ID %1', $id)
            );
        }
        unset($this->instances[$id]);
        return true;
    }

    /**
     * @return OrderInterface
     * @throws CouldNotSaveException
     */
    public function create()
    {
        try {
            $orderItem = $this->orderFactory->create();
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not create new order item'
            ));
        }
        return $orderItem;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\Search\SearchResult
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionFactory->create();
        $this->addFiltersToCollection($searchCriteria, $collection);
        $this->addSortOrdersToCollection($searchCriteria, $collection);
        $this->addPagingToCollection($searchCriteria, $collection);
        $collection->load();
        return $this->buildSearchResult($searchCriteria, $collection);
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param OrderCollection $collection
     */
    private function addFiltersToCollection(SearchCriteriaInterface $searchCriteria, OrderCollection $collection)
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $fields[] = $filter->getField();
                $conditions[] = [$filter->getConditionType() => $filter->getValue()];
            }
            $collection->addFieldToFilter($fields, $conditions);
        }
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param OrderCollection $collection
     */
    private function addSortOrdersToCollection(SearchCriteriaInterface $searchCriteria, OrderCollection $collection)
    {
        foreach ((array) $searchCriteria->getSortOrders() as $sortOrder) {
            $direction = $sortOrder->getDirection() == SortOrder::SORT_ASC ? 'asc' : 'desc';
            $collection->addOrder($sortOrder->getField(), $direction);
        }
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param OrderCollection $collection
     */
    private function addPagingToCollection(SearchCriteriaInterface $searchCriteria, OrderCollection $collection)
    {
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->setCurPage($searchCriteria->getCurrentPage());
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param OrderCollection $collection
     * @return \Magento\Framework\Api\Search\SearchResult
     */
    private function buildSearchResult(SearchCriteriaInterface $searchCriteria, OrderCollection $collection)
    {
        /** @var \Magento\Framework\Api\Search\SearchResult $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * Get a list of W3W entries by order
     * @param $orderId
     * @return bool|\Magento\Framework\Api\Search\DocumentInterface[]|mixed|null
     */
    public function getW3wByOrderId($orderId)
    {
        if ($orderId) {
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('order_id', $orderId, 'eq')
                ->create();
            $items = $this->getList($searchCriteria);
            return $items->getItems();
        }
        return false;
    }
}
