<?php

namespace Respect\Structural;

use Respect\Data\Collections\Collection;
use Respect\Data\Styles\Stylable;

interface Driver
{
    /**
     * @return mixed
     */
    public function getConnection();

    /**
     * @return Stylable
     */
    public function getStyle();

    /**
     * @param Stylable $style
     *
     * @return Driver
     */
    public function setStyle(Stylable $style);

    /**
     * @param \Iterator $cursor
     *
     * @return array
     */
    public function fetch(\Iterator $cursor);

    /**
     * @param $collection
     * @param array $query
     *
     * @return \Iterator
     */
    public function find($collection, array $query = []);

    /**
     * @param string $collection
     * @param object $document
     *
     * @return string Returns the inserted id for current document.
     */
    public function insert($collection, $document);

    /**
     * @param string $collection
     * @param array  $criteria
     * @param object $document
     *
     * @return void
     */
    public function update($collection, $criteria, $document);

    /**
     * @param string $collection
     * @param array  $criteria
     *
     * @return void
     */
    public function remove($collection, $criteria);

    /**
     * @param Collection $collection
     *
     * @return array
     */
    public function generateQuery(Collection $collection);

    /**
     * @param string|int $id
     * @return string
     */
    public function createObjectId($id = null);
}
