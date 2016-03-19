<?php
/**
 * Copyright (c) 2016 Gravity CMS.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
 * Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT
 * NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Gravity\CmsBundle\NodeList;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ListHandler
 *
 * @author Andy Thorne <contrabandvr@gmail.com>
 */
class ListHandler
{
    /**
     * @var string
     */
    private $entity;

    /**
     * @var string
     */
    private $title = '';

    /**
     * @var string
     */
    private $template = 'GravityCmsBundle:Node:list.html.twig';

    /**
     * @var int
     */
    private $pageNumber = 1;

    /**
     * @var int
     */
    private $pageSize = 20;

    /**
     * ListHandler constructor.
     * @param $entity
     */
    public function __construct($entity)
    {
        $this->entity = $entity;
    }


    public static function fromRequest(Request $request)
    {
        $listHandler = new static($request->attributes->get('_entity'));
        $listHandler->setTitle($request->attributes->get('_title'));
        $listHandler->setTemplate($request->attributes->get('_template', 'GravityCmsBundle:Node:list.html.twig'));
        $listHandler->setPageSize($request->attributes->get('_page_size', 20));
        $listHandler->setPageNumber($request->query->get('page', 1));

        return $listHandler;
    }

    /**
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param string $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return int
     */
    public function getPageNumber()
    {
        return $this->pageNumber;
    }

    /**
     * @param int $pageNumber
     */
    public function setPageNumber($pageNumber)
    {
        if (!is_numeric($pageNumber) || !($pageNumber > 1)) {
            $pageNumber = 1;
        }

        $this->pageNumber = $pageNumber;
    }

    /**
     * @return int
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * @param int $pageSize
     */
    public function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
    }

    /**
     * Build a query from the handler's parameters
     *
     * @param QueryBuilder $queryBuilder
     */
    public function buildQuery(QueryBuilder $queryBuilder)
    {
        $queryBuilder->select('e')
            ->from($this->entity, 'e')
            ->where('e.published = 1')
            ->andWhere('e.publishedFrom IS NULL OR e.publishedFrom <= :now')
            ->andWhere('e.publishedTo IS NULL OR e.publishedTo >= :now')
            ->setParameter('now', new \DateTime())
            ->setFirstResult(($this->pageNumber - 1) * $this->pageSize)
            ->setMaxResults($this->pageSize);
    }
}