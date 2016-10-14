<?php

namespace Btn\MediaBundle\Filter;

use Btn\BaseBundle\Filter\AbstractFilter;

class MediaFilter extends AbstractFilter
{
    private $groups;

    /**
     * @param array $groups
     */
    public function setGroups(array $groups)
    {
        $this->groups = $groups;
    }

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

        if (($group = $this->getValue('group'))) {
            $mimeTypes = $this->getMimeTypesForGroup($group);
            if ($mimeTypes) {
                $qb->andWhere('m.type IN (:types)')->setParameter(':types', $mimeTypes);
                $result = true;
            }
        }

        return $result;
    }

    private function getMimeTypesForGroup($groupName) {
        foreach ($this->groups as $group) {
            if ($group['name'] === $groupName) {
                return $group['mime_types'];
            }
        }
    }
}
