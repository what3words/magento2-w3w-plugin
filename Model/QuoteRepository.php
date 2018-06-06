<?php

namespace What3Words\What3Words\Model;

/** What3Words Extension classes */
use What3Words\What3Words\Api\QuoteRepositoryInterface;
use What3Words\What3Words\Api\Data\QuoteInterface;
use What3Words\What3Words\Model\ResourceModel\Quote as QuoteResource;
use What3Words\What3Words\Model\ResourceModel\Quote\Collection as QuoteCollection;
use What3Words\What3Words\Model\ResourceModel\Quote\CollectionFactory as QuoteCollectionFactory;
use What3Words\What3Words\Api\Data\QuoteInterfaceFactory;

/** Exception classes */
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\NoSuchEntityException;

/** Classes for getList */
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SearchResultsInterfaceFactory;

/**
 * Class QuoteRepository
 * @package What3Words\What3Words\Model
 * @author Vicki Tingle
 */
class QuoteRepository implements QuoteRepositoryInterface
{
    /** @var QuoteResource  */
    protected $quoteResource;

    /** @var QuoteCollection  */
    protected $quoteCollection;

    /** @var QuoteInterfaceFactory  */
    protected $quoteFactory;

    /** @var QuoteCollectionFactory  */
    protected $collectionFactory;

    /** @var SearchResultsInterfaceFactory  */
    protected $searchResultsFactory;

    /** @var SearchCriteriaBuilder  */
    protected $searchCriteriaBuilder;

    /** @var array  */
    protected $instances = array();

    /**
     * QuoteRepository constructor.
     * @param QuoteResource $quoteResource
     * @param QuoteCollection $quoteCollection
     * @param QuoteCollectionFactory $collectionFactory
     * @param SearchResultsInterfaceFactory $searchResultsInterfaceFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param QuoteInterfaceFactory $quoteInterfaceFactory
     */
    public function __construct(
        QuoteResource $quoteResource,
        QuoteCollection $quoteCollection,
        QuoteCollectionFactory $collectionFactory,
        SearchResultsInterfaceFactory $searchResultsInterfaceFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        QuoteInterfaceFactory $quoteInterfaceFactory
    ) {
        $this->quoteResource = $quoteResource;
        $this->quoteCollection = $quoteCollection;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsInterfaceFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->quoteFactory = $quoteInterfaceFactory;
    }

    /**
     * @param QuoteInterface $quoteItem
     * @return QuoteInterface
     * @throws CouldNotSaveException
     */
    public function save(QuoteInterface $quoteItem)
    {
        try {
            $this->quoteResource->save($quoteItem);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__(
                'Could not save the quote item: %1',
                $e->getMessage()
            ));
        }
        return $quoteItem;
    }

    /**
     * @param int $quoteItemId
     * @return QuoteInterface
     * @throws NoSuchEntityException
     */
    public function getById($quoteItemId)
    {
        try {
            $quoteItem = $this->quoteFactory->create();
            $this->quoteResource->load($quoteItem, $quoteItemId);
        } catch(\Exception $exception) {
            throw new NoSuchEntityException(__(
                'Could not load the quote item : %1',
                $exception->getMessage()
            ));
        }
        return $quoteItem;
    }

    /**
     * @param QuoteInterface $quoteItem
     * @return bool
     * @throws CouldNotSaveException
     * @throws StateException
     */
    public function delete(QuoteInterface $quoteItem)
    {
        $id = $quoteItem->getEntityId();
        try {
            unset($this->instances[$id]);
            $this->quoteResource->delete($quoteItem);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove quote item with ID %1', $id)
            );
        }
        unset($this->instances[$id]);
        return true;
    }

    /**
     * @return QuoteInterface
     * @throws CouldNotSaveException
     */
    public function create()
    {
        try {
            $quoteItem = $this->quoteFactory->create();
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not create new quote item'
            ));
        }
        return $quoteItem;
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
     * @param QuoteCollection $collection
     */
    private function addFiltersToCollection(SearchCriteriaInterface $searchCriteria, QuoteCollection $collection)
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
     * @param QuoteCollection $collection
     */
    private function addSortOrdersToCollection(SearchCriteriaInterface $searchCriteria, QuoteCollection $collection)
    {
        foreach ((array) $searchCriteria->getSortOrders() as $sortOrder) {
            $direction = $sortOrder->getDirection() == SortOrder::SORT_ASC ? 'asc' : 'desc';
            $collection->addOrder($sortOrder->getField(), $direction);
        }
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param QuoteCollection $collection
     */
    private function addPagingToCollection(SearchCriteriaInterface $searchCriteria, QuoteCollection $collection)
    {
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->setCurPage($searchCriteria->getCurrentPage());
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param QuoteCollection $collection
     * @return \Magento\Framework\Api\Search\SearchResult
     */
    private function buildSearchResult(SearchCriteriaInterface $searchCriteria, QuoteCollection $collection)
    {
        /** @var \Magento\Framework\Api\Search\SearchResult $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * Get list of 3 word addresses from a specified quote
     * @param $quoteId int
     * @return bool|\Magento\Framework\Api\Search\DocumentInterface[]|mixed|null
     */
    public function getW3wByQuoteId($quoteId)
    {
        if ($quoteId) {
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('quote_id', $quoteId, 'eq')
                ->create();
            $items = $this->getList($searchCriteria);
            return $items->getItems();
        }
        return false;
    }
}
