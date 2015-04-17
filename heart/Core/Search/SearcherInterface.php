<?php namespace Qdiscuss\Core\Search;

interface SearcherInterface
{
    public function query();

    public function setDefaultSort($defaultSort);
}
