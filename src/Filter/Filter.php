<?php

namespace Motor\Core\Filter;

use Motor\Core\Filter\Renderers\SelectRenderer;
use Auth;

class Filter
{

    protected $parent;

    protected $filters = [];

    protected $sortableFields = [];

    protected $sorting = [ 'id', 'ASC' ];


    /**
     * Grid constructor.
     *
     * @param $model
     */
    public function __construct($parent)
    {
        $this->parent = $parent;
        if (is_object($parent)) {
            $this->parent = get_class($parent);
        }
    }


    /**
     * @param $name
     *
     * @return mixed|null
     */
    public function get($name)
    {
        if (isset( $this->filters[$name] )) {
            return $this->filters[$name];
        }

        return null;
    }


    public function add(Base $filter)
    {
        $filter->setBaseName($this->parent);
        $filter->updateValues();
        $this->filters[$filter->getName()] = $filter;

        return $filter;
    }

    public function addClientFilter()
    {
        if (Auth::user()->client_id > 0) {
            $this->add(new SelectRenderer('client_id'))->setOptions([Auth::user()->client_id => Auth::user()->client->name])->setDefaultValue(Auth::user()->client_id)->isVisible(false);
        } else {
            $clients = config('motor-backend.models.client')::lists('name', 'id');
            $this->add(new SelectRenderer('client_id'))->setOptions($clients);
        }
    }


    public function filters()
    {
        return $this->filters;
    }
}