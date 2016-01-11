<?php

namespace Btn\MediaBundle\Filter;

use Btn\BaseBundle\Filter\AbstractFilter;

class MediaFilter extends AbstractFilter
{
    public function applyFilters()
    {
        $result = false;
        $qb = $this->getQueryBuilder();

        if (($keyword = $this->getValue('keyword'))) {
            $qb->andWhere('m.name LIKE :keyword')->setParameter(':keyword', '%'.$keyword.'%');
            $result = true;
        }

        if (($category = $this->getValue('category'))) {
            $qb->andWhere('m.category = :category')->setParameter(':category', $category);
            $result = true;
        }

        return $result;
    }
}
