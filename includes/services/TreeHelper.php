<?php

namespace ICD\Hosting\Services;

/**
 * Class to parse trees
 *
 * It can flatten and build trees
 */
class TreeHelper {
	/**
	 * Parent field
	 *
	 * @var string
	 */
	protected $parent_field = 'parent';

	/**
	 * Children field
	 *
	 * @var string
	 */
	protected $children_field = 'children';

	/**
	 * Add parent_id if missing when flatting a tree
	 */
	protected $add_parent_id = true;

	/**
	 * Set parent field
	 *
	 * @param $field
	 *
	 * @return $this
	 */
	public function setParentField( $field ) {
		$this->parent_field = $field;

		return $this;
	}

	/**
	 * Set children field
	 *
	 * @param $field
	 *
	 * @return $this
	 */
	public function setChildrenField( $field ) {
		$this->children_field = $field;

		return $this;
	}

	/**
	 * Set parent rood ID
	 *
	 * @param $id
	 *
	 * @return $this
	 */
	public function setParentIdRoot( $id ) {
		$this->parent_id_root = $id;

		return $this;
	}

	/**
	 * Flag for adding the parent rood ID
	 *
	 * @param $value
	 *
	 * @return $this
	 */
	public function setAddParentId( $value ) {
		$this->add_parent_id = $value;

		return $this;
	}

	/**
	 * Flat a tree
	 *
	 * @param $tree
	 * @param string $parent_id
	 * @param array $flat
	 *
	 * @return array
	 */
	public function flatten( $tree, $parent_id = '', &$flat = array() ) {
		foreach ( $tree as $id => $data ) {
			if ( ! empty( $data[ $this->children_field ] ) ) {
				$children = $data[ $this->children_field ];
				unset( $data[ $this->children_field ] );
				$flat[ $id ] = $data;
				$this->flatten( $children, $id, $flat );
			} else {
				$flat[ $id ] = $data;
			}

			// Add parent id if missing
			if ( $this->add_parent_id && ! isset( $flat[ $id ][ $this->parent_field ] ) ) {
				$flat[ $id ][ $this->parent_field ] = $parent_id;
			}
		}

		return $flat;
	}

	/**
	 * Build a tree
	 *
	 * @param $flat
	 * @param string $parent_id
	 *
	 * @return array
	 */
	public function build( $flat, $parent_id = '' ) {
		$branch = array();

		foreach ( $flat as $id => $element ) {
			// Cast to string to handle different data types when strict comparing bellow
			$id = (string) $id;

			if ( ! isset( $element[ $this->parent_field ] ) ) {
				$element[ $this->parent_field ] = '';
			}

			if ( $element[ $this->parent_field ] === $parent_id ) {
				$children = $this->build( $flat, $id );
				if ( $children ) {
					$element[ $this->children_field ] = $children;
				}
				$branch[ $id ] = $element;
			}
		}

		return $branch;
	}

	/**
	 * Delete node from tree
	 *
	 * @param $node_id
	 * @param $tree
	 *
	 * @return mixed
	 */
	public function deleteNode( $node_id, &$tree ) {
		foreach ( $tree as $id => &$data ) {
			// found it - delete
			if ( $id == $node_id ) {
				unset( $tree[ $id ] );

				return $tree;
			}
			// has children
			if ( ! empty( $data[ $this->children_field ] ) ) {
				// recurse into children
				$this->deleteNode( $node_id, $data[ $this->children_field ] );
			}
		}

		return $tree;
	}
}