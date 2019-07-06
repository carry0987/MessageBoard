<?php
class SitemapGenerator
{
    public static $document = null;
    private static $options = array();
    const DIR_SEP = DIRECTORY_SEPARATOR;

    public function __construct($option = array(), $addSitemap = false)
    {
        if (isset($option)) {
            self::$options = $option;
            if (!self::$document) {
                self::$document = new DOMDocument(self::$options['version'], self::$options['charset']);
                self::$document->formatOutput = true;
                self::$document->preserveWhiteSpace = false;
                if ($addSitemap === false) {
                    //Generate the urlset once
                    $this->addurlset();
                }
            }
        } else {
            return 'Could not find option';
        }
    }

    private function trimPath($path)
    {
        return str_replace(array('/', '\\', '//', '\\\\'), self::DIR_SEP, $path);
    }

    //Generate the root node - urlset
    private function addurlset()
    {
        $urlset = $this->createElement('urlset');
        $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $this->appendChild($urlset);
    }

    //Add item to xml
    public function addSitemapNode($result = array())
    {
        if (!empty($result) && is_array($result)) {
            $get_urlset = self::$document->getElementsByTagName('urlset');
            $urlset = $get_urlset[0];
            foreach ($result as $var) {
                $var['loc'] = htmlentities($var['loc']);
                $var['lastmod'] = $this->trimLastmod($var['lastmod'], self::$options['timezone']);
                $item = $this->createElement('url');
                $urlset->appendChild($item);
                $this->createItem($item, $var);
            }
        }
    }

    //Update sitemap
    public function updateSitemap($result = array())
    {
        $updateResult = false;
        if ($this->checkSitemap(self::$options['xml_file']) === true) {
            //Open and load a XML file
            $this->loadSitemap(self::$options['xml_file']);
            // Apply some modification
            foreach ($result as $sitemapData) {
                if ($sitemapData !== false) {
                    $this->addSitemapNode($sitemapData);
                    $updateResult = true;
                }
            }
            if ($updateResult === true) {
                $this->generateXML();
            }
        } else {
            $updateResult = false;
        }
        return $updateResult;
    }

    public function generateXML()
    {
        $file_path = $this->trimPath(self::$options['xml_file']);
        $this->saveFile($file_path);
    }

    private function trimLastmod($value, $tz = false)
    {
        try {
            $tz = new DateTimeZone($tz);
        } catch (Exception $e) {
            $tz = false;
        }
        if (is_int($value)) {
            $timezone = ($tz !== false) ? $tz : new DateTimeZone('Europe/London');
            $dateTime = new DateTime('', $timezone);
            $dateTime->setTimestamp($value);
            $result = $dateTime->format('c');
        } else {
            $result = date('c', strtotime($value));
        }
        return $result;
    }

    //Create element
    private function createElement($element)
    {
        return self::$document->createElement($element);
    }

    //Append child node
    private function appendChild($child)
    {
        return self::$document->appendChild($child);
    }

    //Add item
    private function createItem($item, $data, $attribute = array())
    {
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                //Create an element, the element name cannot begin with a number
                is_numeric($key{0}) && exit($key.' Error: First char cannot be a number');
                $temp = self::$document->createElement($key);
                $item->appendChild($temp);
                //Add element value
                $text = self::$document->createTextNode($val);
                $temp->appendChild($text);
                if (isset($attribute[$key])) {
                    foreach ($attribute[$key] as $akey => $row) {
                        //Create attribute node
                        $temps = self::$document->createAttribute($akey);
                        $temp->appendChild($temps);
                        //Create attribute value node
                        $aval = self::$document->createTextNode($row);
                        $temps->appendChild($aval);
                    }
                } 
            }
        }
    }

    //Return xml string
    private function saveXML()
    {
        return self::$document->saveXML();
    }

    //Save xml file to path
    private function saveFile($fpath)
    {
        //Write file
        $writeXML = file_put_contents($fpath, $this->saveXML());
        if ($writeXML === true) {
            return $this->saveXML();
        } else {
            return 'Could not write into file';
        }
    }

    //Load xml file
    public function loadSitemap($fpath)
    {
        $fpath = $this->trimPath($fpath);
        if (!file_exists($fpath)) {
            exit($fpath.' is a invalid file');
        }
        //Returns TRUE on success, or FALSE on failure
        return self::$document->load($fpath);
    }

    //Check xml file exist
    public function checkSitemap()
    {
        $fpath = $this->trimPath(self::$options['xml_file']);
        if (file_exists($fpath)) {
            return true;
        }
    }
}
