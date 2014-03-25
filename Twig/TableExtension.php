<?php

namespace Ekyna\Bundle\TableBundle\Twig;

use Ekyna\Component\Table\Util\ColumnSort;
use Ekyna\Component\Table\TableView;
use Ekyna\Component\Table\View\Cell;
use Ekyna\Component\Table\View\Column;
use Ekyna\Component\Table\View\AvailableFilter;
use Ekyna\Component\Table\View\ActiveFilter;
use Pagerfanta\PagerfantaInterface;
use Pagerfanta\View\ViewFactoryInterface;

class TableExtension extends \Twig_Extension
{
    /**
     * @var \Twig_Environment
     */
    private $environment;

    /**
     * @var ViewFactoryInterface
     */
    private $viewFactory;

    /**
     * @var \Twig_Template
     */
    private $template;

    /**
     * @var array
     */
    protected $defaultOptions;

    /**
     * Constructor
     * 
     * @param \Twig_Environment    $environment
     * @param ViewFactoryInterface $viewFactory
     * @param array                $options
     */
    public function __construct(\Twig_Environment $environment, ViewFactoryInterface $viewFactory, array $defaultOptions = array())
    {
        $this->environment = $environment;
        $this->viewFactory = $viewFactory;
        $this->defaultOptions = array_merge(array(
            'class' => null,
            'template' => 'EkynaTableBundle::ekyna_table.html.twig',
        ), $defaultOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'ekyna_table_render' => new \Twig_Function_Method($this, 'render', array('is_safe' => array('html'))),
            'ekyna_table_cell' => new \Twig_Function_Method($this, 'renderCell', array('is_safe' => array('html'))),
            'ekyna_table_pager' => new \Twig_Function_Method($this, 'renderPager', array('is_safe' => array('html'))),
            'ekyna_table_sort_path' => new \Twig_Function_Method($this, 'generateSortPath', array('is_safe' => array('html'))),
            'ekyna_table_filter_add_path' => new \Twig_Function_Method($this, 'generateFilterAddPath', array('is_safe' => array('html'))),
            'ekyna_table_filter_remove_path' => new \Twig_Function_Method($this, 'generateFilterRemovePath', array('is_safe' => array('html'))),
        );
    }

    /**
     * Renders a table
     *
     * @param TableView $table
     * @param array     $options
     *
     * @return string
     */
    public function render(TableView $table, array $options = array())
    {
        $options = array_merge($this->defaultOptions, $options);

        $template = $options['template'];
        if ($template instanceof \Twig_Template) {
            $this->template = $template;
        }else{
            $this->template = $this->environment->loadTemplate($template);
        }

        return $this->template->renderBlock('table', array('table' => $table, 'options' => $options));
    }

    /**
     * Renders a cell
     *
     * @param array $vars
     * @param array $options
     *
     * @return string
     */
    public function renderCell(Cell $cell)
    {
        $block = $cell->vars['type'].'_cell';
        if(!$this->template->hasBlock($block)) {
            $block = 'text_cell';
        }
        return trim($this->template->renderBlock($block, array('vars' => $cell->vars)));
    }

    /**
     * Renders pager
     *
     * @param PagerfantaInterface $pagerfanta
     * @param string              $viewName
     * @param array               $options
     *
     * @return string
     */
    public function renderPager(PagerfantaInterface $pagerfanta, $viewName = 'twitter_bootstrap3', array $options = array())
    {
        $options = array_merge(array(
            'pageParameter' => '[page]',
            'proximity'     => 3,
            'next_message'  => '&raquo;',
            'prev_message'  => '&laquo;',
            'default_view'  => 'default'
        ), $options);

        $routeGenerator = function($page) {
            return '?page='.$page;
        };
        
        return $this->viewFactory->get($viewName)->render($pagerfanta, $routeGenerator, $options);;
    }

    /**
     * Generates a column sort path
     * 
     * @param ColumnInterface $column
     * 
     * @return string
     */
    public function generateSortPath(Column $column)
    {
        if(true === $column->getVar('sortable')) {
            $sort = $column->getVar('sort_dir');
            $path = '?' . $column->getVar('full_name') .'_sort=';
            return $path .= $sort === ColumnSort::ASC ? ColumnSort::DESC : ColumnSort::ASC;
        }
        return '';
    }

    /**
     * Generates a filter add path
     * 
     * @param AvailableFilter $filter
     * 
     * @return string
     */
    public function generateFilterAddPath(AvailableFilter $filter)
    {
        return '?add_filter=' . $filter->getVar('full_name');
    }

    /**
     * Generates a filter remove path
     * 
     * @param ActiveFilter $filter
     * 
     * @return string
     */
    public function generateFilterRemovePath(ActiveFilter $filter)
    {
        return '?remove_filter=' . $filter->getVar('id');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
    	'ekyna_table';
    }
}