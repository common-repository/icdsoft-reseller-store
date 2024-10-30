<?php

namespace ICD\Hosting\Services;

/**
 * Class CatalogHelper
 *
 * @package ICD\Hosting\Services
 */
class CatalogHelper {

	/**
	 * @var array
	 */
	public $catalog = array();

	/**
	 * @var array
	 */
	public $products = array();

	/**
	 * @var array
	 */
	public $location_catalog = array();

	/**
	 * @var array
	 */
	public $filter = array(
		'location'      => array(), // centurilink, neterra, iadvantage
		'product_type'  => array( 'hosting', 'server', 'domain' ), // hosting, domain
		'product_group' => array( 'bonus:domain', 'extra:domain' ), // bonus:domain, extra:domain
		'purpose'       => array( 'order' ), // order, renewal
		'period'        => array(), // 1,2
	);

	/**
	 * CatalogHelper constructor.
	 * Initialize catalog and products.
	 *
	 * @param array $catalog
	 * @param array $products
	 */
	public function __construct( $catalog = array(), $products = array() ) {
		$this->catalog  = $catalog;
		$this->products = $products;
	}

	/**
	 * Filter catalog
	 *
	 * @param array $filter
	 *
	 * @return array
	 */
	public function filterCatalog( $filter = array() ) {
		if ( empty( $filter ) ) {
			$filter = $this->filter;
		}

		return $this->filterCatalogTree( $this->catalog, $filter );
	}

	/**
	 * Filter multilevel catalog tree
	 *
	 * @param $catalog
	 * @param array $filter
	 *
	 * @return array
	 */
	public function filterCatalogTree( $catalog, $filter = array() ) {
		$ret = array();
		foreach ( $catalog as $key => $item ) {
			$product      = $this->products[ $item['product_id'] ]['product'];
			$product_type = $this->products[ $item['product_id'] ]['type'];
			if ( $product_type == 'group' ) {
				if ( ! empty( $filter['product_group'] ) && ! in_array( $product, $filter['product_group'] ) ) {
					continue;
				}
			} else {
				if ( ! empty( $filter['product_type'] ) && ! in_array( $product_type, $filter['product_type'] ) ) {
					continue;
				}

				if ( ! empty( $item['prices'] ) ) {
					// Unset not needed prices based on purpose
					if ( ! empty( $filter['purpose'] ) ) {
						foreach ( $item['prices'] as $purpose => $prices ) {
							if ( ! in_array( $purpose, $filter['purpose'] ) ) {
								unset( $item['prices'][ $purpose ] );
							}
						}
					}

					// Unset not needed prices based on period
					if ( ! empty( $filter['period'] ) ) {
						foreach ( $item['prices'] as $purpose => $prices ) {
							foreach ( $prices as $period => $price ) {
								if ( ! in_array( $period, $filter['period'] ) ) {
									unset( $item['prices'][ $purpose ][ $period ] );
								}
							}
						}
					}
				}

				//items without prices
				if ( ! $item['prices'] ) {
					continue;
				}
			}

			$ret[ $key ] = $item;

			// traverse subtree
			if ( ! empty( $ret[ $key ]['children'] ) ) {
				$ret[ $key ]['children'] = $this->filterCatalogTree( $ret[ $key ]['children'], $filter );
			} // if subtree is empty remove node itself
			else if ( empty( $ret[ $key ]['children'] ) && $product_type == 'group' ) {
				unset( $ret[ $key ] );
			}
		}

		return $ret;
	}

	/**
	 * Builds a flat representation of the catalog by the following pattern
	 * location -> product -> prices -> periods -> price
	 * location -> product -> bonus:domain -> domain -> periods -> price
	 * we only go one level deep here
	 *
	 * @param $catalog
	 */
	public function catalogByLocation( $catalog = array() ) {
		if ( $this->location_catalog ) {
			return $this->location_catalog;
		}

		if ( ! $catalog ) {
			$catalog = $this->catalog;
		}

		foreach ( $catalog as $catalog_id => $node ) {
			if ( ! empty( $node['prices'] ) ) {
				$location = $this->products[ $node['product_id'] ]['datacenter'];
				if ( ! $location ) {
					continue;
				}

				$product                                                         = $this->products[ $node['product_id'] ]['product'];
				$type                                                            = $this->products[ $node['product_id'] ]['type'];
				$periodicity                                                     = $this->products[ $node['product_id'] ]['periodicity'];
				$this->location_catalog[ $location ][ $product ]['catalog_id']   = $catalog_id;
				$this->location_catalog[ $location ][ $product ]['product_id']   = $node['product_id'];
				$this->location_catalog[ $location ][ $product ]['product']      = $product;
				$this->location_catalog[ $location ][ $product ]['product_type'] = $type;
				$this->location_catalog[ $location ][ $product ]['periodicity']  = $periodicity;
				$this->location_catalog[ $location ][ $product ]['name']         = icd_hosting_tr( $node['name'] ); //tr('catalog.' . $product);
				foreach ( $node['prices'] as $purpose => $prices ) {
					foreach ( $prices as $pp => $pv ) {
						$prices[ $pp ] = array( 'price'        => $pv,
						                        'period_label' => icd_hosting_tr_choice( 'periods.' . $periodicity, $pp )
						);
					}
					$this->location_catalog[ $location ][ $product ][ $purpose ] = $prices;
				}
				$this->location_catalog[ $location ][ $product ] += $this->filterAddons( $location, array_keys( $node['prices'] ), $node['children'] );
			}

			//if (!empty($node['children']))
			//	$this->catalogByLocation($node['children']);
		}

		return $this->location_catalog;
	}

	/**
	 * Filter Add-on products
	 *
	 * @param $location
	 * @param $purposes
	 * @param $subtree
	 *
	 * @return array
	 */
	protected function filterAddons( $location, $purposes, $subtree ) {
		$ret = array();
		foreach ( $subtree as $catalog_id => $node ) {
			$product     = $this->products[ $node['product_id'] ]['product'];
			$type        = $this->products[ $node['product_id'] ]['type'];
			$datacenter  = $this->products[ $node['product_id'] ]['datacenter'];
			$periodicity = $this->products[ $node['product_id'] ]['periodicity'];

			if ( ! isset( $ret[ $product ] ) ) {
				$ret[ $product ] = array();
			}

			if ( ( ! $datacenter or $datacenter == $location ) and ! empty( $node['prices'] ) ) {
				foreach ( $node['prices'] as $purpose => $prices ) {
					if ( in_array( $purpose, $purposes ) ) {
						foreach ( $prices as $pp => $pv ) {
							$prices[ $pp ] = array( 'price'        => $pv,
							                        'period_label' => icd_hosting_tr_choice( 'periods.' . $periodicity, $pp )
							);
						}
						$ret[ $product ]['catalog_id']   = $catalog_id;
						$ret[ $product ]['product_id']   = $node['product_id'];
						$ret[ $product ]['product']      = $product;
						$ret[ $product ]['product_type'] = $type;
						$ret[ $product ]['name']         = icd_hosting_tr( $node['name'] );
						$ret[ $product ][ $purpose ]     = $prices;
					}
				}
			}

			if ( ! empty( $node['children'] ) ) {
				$ret[ $product ] += $this->filterAddons( $location, $purposes, $node['children'] );
			}
		}

		return $ret;
	}

	/**
	 * Get subcatalog from main catalog
	 *
	 * @param $catalog_id
	 *
	 * @return array
	 */
	public function subCatalog( $catalog_id ) {
		$parents = array( $catalog_id );
		$result  = array( 'catalog' => [], 'products' => [] );
		foreach ( $this->catalog as $id => $item ) {
			$product = $this->products[ $item['product_id'] ];

			if ( $product['type'] != 'group' and empty( $item['prices']['order'][1]['active'] ) ) {
				continue;
			}

			if ( in_array( $item['parent_id'], $parents ) ) {
				$result['catalog'][ $id ]                  = $item;
				$result['products'][ $item['product_id'] ] = $product;
				if ( $product['type'] == 'group' ) {
					$parents[] = $id;
				}
			}
		}

		$sub_catalog = [];
		foreach ( $result['catalog'] as $catalog_id => $item ) {
			if ( ! isset( $result['catalog'][ $item['parent_id'] ] ) ) {
				continue;
			}

			$product = $result['products'][ $item['product_id'] ];
			if ( $product['type'] != 'group' ) {
				$sub_catalog[ $item['parent_id'] ][ $catalog_id ] = $item['name'];
			}
		}

		return $sub_catalog;
	}

}
