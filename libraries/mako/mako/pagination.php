<?php

namespace mako
{
	use \Mako;
	use \mako\I18n;
	
	/**
	* Pagination class.
	*
	* @author     Frederic G. Østby
	* @copyright  (c) 2008-2011 Frederic G. Østby
	* @license    http://www.makoframework.com/license
	*/

	class Pagination
	{
		//---------------------------------------------
		// Class variables
		//---------------------------------------------

		/**
		* Name of the $_GET key holding the current page number.
		*/

		protected $key;

		/**
		* Offset.
		*/

		protected $offset;
		
		/**
		* Current page.
		*/

		protected $currentPage;

		/**
		* Number of pages.
		*/

		protected $pages;
		
		/**
		* Number of items per page.
		*/
		
		protected $itemsPerPage;
		
		/**
		* Maximum number of page links.
		*/
		
		protected $maxPageLinks;

		//---------------------------------------------
		// Class constructor, destructor etc ...
		//---------------------------------------------

		/**
		* Constructor.
		*
		* @access  public
		*/

		public function __construct()
		{
			$config = Mako::config('pagination');
			
			$this->key          = $config['page_key'];
			$this->currentPage  = max((int) (isset($_GET[$this->key]) ? $_GET[$this->key] : 1), 1);
			$this->itemsPerPage = $config['items_per_page'];
			$this->maxPageLinks = $config['max_page_links'];
		}

		//---------------------------------------------
		// Class methods
		//---------------------------------------------

		/**
		* Calculates the offset and number of pages and returns the offset.
		*
		* @access  public
		* @param   int     Number of items
		* @param   int     Number of items to display on each page
		* @return  int
		*/

		public function getOffset($itemCount, $itemsPerPage = null)
		{
			$itemsPerPage = ($itemsPerPage === null) ? max($this->itemsPerPage, 1) : max($itemsPerPage, 1);
			$this->pages  = ceil(($itemCount / $itemsPerPage));
			$this->offset = ($this->currentPage - 1) * $itemsPerPage;

			return $this->offset;
		}

		/**
		* Returns an associative array of pagination links.
		*
		* @access  public
		* @param   string  (optional) URL segments
		* @param   array   (optional) Associative array used to build URL-encoded query string
		* @param   string  (optional) Argument separator
		* @return  array
		*/

		public function getLinks($url = '', array $params = null, $separator = '&amp;')
		{
			$links = array();
			
			$params = (array) $params; // Cast params to array
			
			// Number of pages
			
			$links['num_pages'] = I18n::getText('%d Pages', array($this->pages));
			
			// First and previous page
			
			if($this->currentPage > 1)
			{
				$links['first_page'] = array
				(
					'name' => I18n::getText('First Page'), 
					'url'  => Mako::url($url, array_merge($params, array($this->key => 1)), $separator),
				);
				
				$links['previous_page'] = array
				(
					'name' => '&laquo;',
					'url'  => Mako::url($url, array_merge($params, array($this->key => ($this->currentPage - 1))), $separator),
				);
			}
			
			// Last and next page
			
			if($this->currentPage < $this->pages)
			{
				$links['last_page'] = array
				(
					'name' => I18n::getText('Last Page'),
					'url'  => Mako::url($url, array_merge($params, array($this->key => $this->pages)), $separator),
				);
				
				$links['next_page'] = array
				(
					'name' => '&raquo;',
					'url'  => Mako::url($url, array_merge($params, array($this->key => ($this->currentPage + 1))), $separator),
				);
			}
			
			// Page links
			
			if($this->pages > $this->maxPageLinks)
			{
				$start = max(($this->currentPage) - ceil($this->maxPageLinks / 2), 0);

				$end = $start + $this->maxPageLinks;

				if($end > $this->pages)
				{
					$end = $this->pages;
				}

				if($start > ($end - $this->maxPageLinks))
				{
					$start = $end - $this->maxPageLinks;
				}
			}
			else
			{
				$start = 0;

				$end = $this->pages;
			}
			
			for($i = $start + 1; $i <= $end; $i++)
			{
				$links['pages'][] = array
				(
					'name'    => $i,
					'url'     => Mako::url($url, array_merge($params, array($this->key => $i)), $separator),
					'is_current' => ($i == $this->currentPage),
				);
			}
						
			return $links;
		}
	}
}

/** -------------------- End of file --------------------**/