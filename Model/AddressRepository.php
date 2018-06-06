<?php

namespace What3Words\What3Words\Model;

/** What3Words Extension classes */
use What3Words\What3Words\Api\AddressRepositoryInterface;
use What3Words\What3Words\Api\Data\AddressInterface;
use What3Words\What3Words\Model\ResourceModel\Address as AddressResource;
use What3Words\What3Words\Model\ResourceModel\Address\Collection as AddressCollection;
use What3Words\What3Words\Model\ResourceModel\Address\CollectionFactory as AddressCollectionFactory;
use What3Words\What3Words\Api\Data\AddressInterfaceFactory;

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
 * Class AddressRepository
 * @package What3Words\What3Words\Model
 * @author Vicki Tingle
 */
class AddressRepository implements AddressRepositoryInterface
{
   /** @var AddressResource  */
    protected $addressResource;

    /** @var AddressCollection  */
    protected $addressCollection;

    /** @var AddressInterfaceFactory  */
    protected $addressFactory;

    /** @var AddressCollectionFactory  */
    protected $collectionFactory;

    /** @var SearchResultsInterfaceFactory  */
    protected $searchResultsFactory;

    /** @var SearchCriteriaBuilder  */
    protected $searchCriteriaBuilder;

    /** @var array  */
    protected $instances = array();

    /**
     * AddressRepository constructor.
     * @param AddressResource $addressResource
     * @param AddressCollection $addressCollection
     * @param AddressCollectionFactory $collectionFactory
     * @param SearchResultsInterfaceFactory $searchResultsInterfaceFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param AddressInterfaceFactory $addressInterfaceFactory
     */
    public function __construct(
        AddressResource $addressResource,
        AddressCollection $addressCollection,
        AddressCollectionFactory $collectionFactory,
        SearchResultsInterfaceFactory $searchResultsInterfaceFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        AddressInterfaceFactory $addressInterfaceFactory
    ) {
        $this->addressResource = $addressResource;
        $this->addressCollection = $addressCollection;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsInterfaceFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->addressFactory = $addressInterfaceFactory;
    }

    /**
     * @param AddressInterface $addressItem
     * @return AddressInterface
     * @throws CouldNotSaveException
     */
    public function save(AddressInterface $addressItem)
    {
        try {
            $this->addressResource->save($addressItem);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__(
                'Could not save the address item: %1',
                $e->getMessage()
            ));
        }
        return $addressItem;
    }

    /**
     * Get an address item by its ID
     * @param int $addressItemId
     * @return AddressInterface
     * @throws NoSuchEntityException
     */
    public function getById($addressItemId)
    {
        try {
            $address = $this->addressFactory->create();
            $this->addressResource->load($address, $addressItemId);
        } catch(\Exception $exception) {
            throw new NoSuchEntityException(__(
                'Could not load the address item : %1',
                $exception->getMessage()
            ));
        }
        return $address;
    }

    /**
     * Delete an address item
     * @param AddressInterface $addressItem
     * @return bool
     * @throws CouldNotSaveException
     * @throws StateException
     */
    public function delete(AddressInterface $addressItem)
    {
        $id = $addressItem->getEntityId();
        try {
            unset($this->instances[$id]);
            $this->addressResource->delete($addressItem);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove address item with ID %1', $id)
            );
        }
        unset($this->instances[$id]);
        return true;
    }

    /**
     * Create a new address item
     * @return Address
     * @throws CouldNotSaveException
     */
    public function create()
    {
        try {
            $addressItem = $this->addressFactory->create();
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not create new address item'
            ));
        }
        return $addressItem;
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
     * @param AddressCollection $collection
     */
    private function addFiltersToCollection(SearchCriteriaInterface $searchCriteria, AddressCollection $collection)
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
     * @param AddressCollection $collection
     */
    private function addSortOrdersToCollection(SearchCriteriaInterface $searchCriteria, AddressCollection $collection)
    {
        foreach ((array) $searchCriteria->getSortOrders() as $sortOrder) {
            $direction = $sortOrder->getDirection() == SortOrder::SORT_ASC ? 'asc' : 'desc';
            $collection->addOrder($sortOrder->getField(), $direction);
        }
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param AddressCollection $collection
     */
    private function addPagingToCollection(SearchCriteriaInterface $searchCriteria, AddressCollection $collection)
    {
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->setCurPage($searchCriteria->getCurrentPage());
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param AddressCollection $collection
     * @return \Magento\Framework\Api\Search\SearchResult
     */
    private function buildSearchResult(SearchCriteriaInterface $searchCriteria, AddressCollection $collection)
    {
        /** @var \Magento\Framework\Api\Search\SearchResult $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * Get list of 3 word addresses with specified ID
     * @param $addressId
     * @return bool|\Magento\Framework\Api\Search\DocumentInterface[]|mixed|null
     */
    public function getW3wByAddressId($addressId)
    {
        if ($addressId) {
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('address_id', $addressId, 'eq')
                ->create();
            $items = $this->getList($searchCriteria);
            if (count($items->getItems()) > 0) {
                return $items->getItems();
            }
            return false;
        }
        return false;
    }
}
