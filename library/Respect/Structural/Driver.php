<?php

namespace Respect\Structural;

use Respect\Data\Collections\Collection;

interface Driver
{
    public function getConnection();

    public function find($collection, array $query = array());

    public function insert($collection, $document);

    public function update($collection, $criteria, $document);

    public function remove($collection, $criteria);

    /**
     * @param Collection $collection
     * @return array
     */
    public function generateQuery(Collection $collection);
}