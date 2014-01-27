<?php
/**
 * @package api
 * @subpackage filters
 */
class kCategoryEntryAdvancedFilter extends AdvancedSearchFilterItem
{
	const DYNAMIC_ATTRIBUTES = 'dynamic_attributes';
	const CREATED_AT = 'createdAt';

	/**
	 * @var string
	 */
	protected $categoriesMatchOr = null;
	
	/**
	 * @var string
	 */
	protected $categoryEntryStatusIn = null;

	/**
	 * @var string
	 */
	protected $orderBy = null;

	/**
	 * @var int
	 */
	protected $categoryIdEqual = null;

	/**
	 * Compose a dynamic attribute field name
	 * E.g.: cat_32_createdAt
	 *
	 * @param int $categoryId
	 * @param int $categoryEntryStatus
	 * @return string dynamic_attributes.cat_{cat id}_createdAt (e.g. dynamic_attributes.cat_32_createdAt)
	 */
	public static function getCategoryCreatedAtDynamicAttributeName( $categoryId )
	{
		return "cat_{$categoryId}_createdAt";
	}

	/* (non-PHPdoc)
	 * @see AdvancedSearchFilterItem::applyCondition()
	 */
	public function applyCondition(IKalturaDbQuery $query)
	{
		if ( $this->orderBy )
		{
			$orderByField = substr($this->orderBy, 1);
			$orderBy = $this->orderBy[0] == '+' ? Criteria::ASC : Criteria::DESC;

			if ( $orderByField == self::CREATED_AT )
			{
				$categoryId = $this->categoryIdEqual;

				// NOTE: Currently supporting a single ACTIVE status. That is the
				//       reason why we take the categoryEntryStatusIn value as is.
				//       (*) See KalturaCategoryEntryAdvancedFilter::validateForUsage()
				$categoryEntryStatus = trim( $this->categoryEntryStatusIn );
		
				$dynAttribCriteriaFieldName = self::DYNAMIC_ATTRIBUTES . '.' . self::getCategoryCreatedAtDynamicAttributeName( $categoryId );
				$query->addNumericOrderBy( $dynAttribCriteriaFieldName, $orderBy);

				$query->addColumnWhere(entryPeer::CATEGORIES_IDS, $categoryId, KalturaCriteria::IN_LIKE);
			}
		}
		else if ( !is_null($this->categoriesMatchOr) && !is_null($this->categoryEntryStatusIn))
		{
			$categoriesTocategoryEntryStatus = entryFilter::categoryFullNamesToIdsParsed ($this->categoriesMatchOr, $this->categoryEntryStatusIn );

			if($categoriesTocategoryEntryStatus == '')
				$categoriesTocategoryEntryStatus = category::CATEGORY_ID_THAT_DOES_NOT_EXIST;
		
		$query->addColumnWhere(entryPeer::CATEGORIES_IDS, explode(',', $categoriesTocategoryEntryStatus), KalturaCriteria::IN_LIKE);
		}
	}
	
	/**
	 * @return string $categoriesMatchOr
	 */
	public function getCategoriesMatchOr()
	{
		return $this->categoriesMatchOr;
	}

	/**
	 * @param string $categoriesMatchOr
	 */
	public function setCategoriesMatchOr($categoriesMatchOr)
	{
		$this->categoriesMatchOr = $categoriesMatchOr;
	}
	
	/**
	 * @return string $categoryEntryStatusIn
	 */
	public function getCategoryEntryStatusIn()
	{
		return $this->categoryEntryStatusIn;
	}

	/**
	 * @param string $categoryEntryStatusIn
	 */
	public function setCategoryEntryStatusIn($categoryEntryStatusIn)
	{
		$this->categoryEntryStatusIn = $categoryEntryStatusIn;
	}

	/**
	 * @param string $orderBy
	 */
	public function setOrderBy($orderBy)
	{
		$this->orderBy = $orderBy;
	}
	
	/**
	 * @return string $orderBy
	 */
	public function getOrderBy()
	{
		return $this->orderBy;
	}

	/**
	 * @param int $categoryIdEqual
	 */
	public function setCategoryIdEqual($categoryIdEqual)
	{
		$this->categoryIdEqual = $categoryIdEqual;
	}
	
	/**
	 * @return int $categoryIdEqual
	 */
	public function getCategoryIdEqual()
	{
		return $this->categoryIdEqual;
	}
}
