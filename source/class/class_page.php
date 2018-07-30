<?php
class Pagination
{
    private $properties = array();
    public $defaults = array(
        'page' => 1,
        'perPage' => 10,
    );

    public function __construct($array, $curPage = null, $perPage = null)
    {
        $this->array = $array;
        $this->curPage = ($curPage == null ? $this->defaults['page'] : $curPage);
        $this->perPage = ($perPage == null ? $this->defaults['perPage'] : $perPage);
    }

    public function __set($name, $value) 
    { 
        $this->properties[$name] = $value;
    } 

    public function __get($name)
    {
        if (array_key_exists($name, $this->properties)) {
        return $this->properties[$name];
    }
        return false;
    }

    public function setShowFirstAndLast($showFirstAndLast)
    {
        $this->_showFirstAndLast = $showFirstAndLast;
    }

    public function setMainSeperator($mainSeperator)
    {
        $this->mainSeperator = $mainSeperator;
    }

    public function getTotalPages()
    {
        if (empty($this->curPage) !== false) {
            $this->page = $this->curPage;
        } else {
            $this->page = 1;
        }
        $this->pages = ceil($this->length / $this->perPage);
        return $this->pages;
    }

    public function getResults()
    {
        if (empty($this->curPage) !== false) {
            $this->page = $this->curPage;
        } else {
            $this->page = 1;
        }
        $this->length = count($this->array);
        $this->pages = ceil($this->length / $this->perPage);
        $this->start = ceil(($this->page - 1) * $this->perPage);
        return array_slice($this->array, $this->start, $this->perPage);
    }

    public function getLinks()
    {
        $links = array();
        for ($j = 1; $j < ($this->pages + 1); $j++) {
            if ($this->page == $j) {
                $links[] = ['class' => 'active', 'page' => "$j"];
            } else {
                $links[] = ['class' => 'pages_tag', 'page' => "$j"];
            }
        }
        return $links;
    }

    public function showPrev()
    {
        $plinks = '';
        if (($this->pages) > 1) {
            if ($this->page != 1) {
                $plinks = ($this->page - 1);
            }
        return $plinks;
        }
    }

    public function showNext()
    {
        $slinks = '';
        if ($this->page < $this->pages) {
            $slinks = ($this->page + 1);
        }
        return $slinks;
    }
}
