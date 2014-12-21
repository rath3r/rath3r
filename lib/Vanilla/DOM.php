<?php

/**
 * Vanilla_DOM Class
 *
 * @name     Vanilla_DOM
 * @category DOM
 * @package  Vanilla
 * @author   Dan Conaghan <dconaghan@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

/**
 * Debug Class
 *
 * @name     Vanilla_Debug
 * @category DOM
 * @package  Vanilla
 * @author   Dan Conaghan <dconaghan@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

class Vanilla_DOM
{
    /**
     * Document path
     * @var string
     */
    protected $_document;

    /**
     * Document errors, false if none, or array if any errors returned
     * @var false|array
     */
    protected $_document_errors = false;

    /**
     * Doctype variable
     * @var string
     */
    protected $_doc_type;

    /**
     * Document encoding
     * @var string
     */
    protected $_encoding;

    /**
     * Xpath namespaces
     * @var array
     */
    protected $_xpath_namespaces = array();

    /**
     * Constructor
     *
     * @param null|string $document Document
     * @param string      $encoding Encoding type
     * 
     * @magic
     * 
     * @return void
     */
    public function __construct($document = null, $encoding = null)
    {
        $this->setEncoding($encoding);
        $this->setDocument($document);
    }

    /**
     * Set document encoding
     *
     * @param string $encoding Encoding
     * 
     * @return Vanilla_XMLFile
     */
    public function setEncoding($encoding)
    {
        $this->_encoding = (null === $encoding) ? null : (string) $encoding;
        return $this;
    }

    /**
     * Get document encoding
     *
     * @return null|string
     */
    public function getEncoding()
    {
        return $this->_encoding;
    }

    /**
     * Set document to query
     *
     * @param string      $document Document Path
     * @param null|string $encoding Document encoding
     * 
     * @return Vanilla_DOMQuery
     */
    public function setDocument($document, $encoding = null)
    {
        if (0 === strlen($document)) {
            return $this;
        }
        // breaking XML declaration to make syntax highlighting work
        if ('<' . '?xml' == substr(trim($document), 0, 5)) {
            return $this->setDocumentXml($document, $encoding);
        }
        if (strstr($document, 'DTD XHTML')) {
            return $this->setDocumentXhtml($document, $encoding);
        }
        return $this->setDocumentHtml($document, $encoding);
    }

    /**
     * Register HTML document
     *
     * @param string      $document Document Path
     * @param null|string $encoding Document encoding
     * 
     * @return Vanilla_DOMQuery
     */
    public function setDocumentHtml($document, $encoding = null)
    {
        $this->_document = (string) $document;
        $this->_doc_type  = self::DOC_HTML;
        if (null !== $encoding) {
            $this->setEncoding($encoding);
        }
        return $this;
    }

    /**
     * Register XHTML document
     *
     * @param string      $document Document Path
     * @param null|string $encoding Document encoding
     * 
     * @return Vanilla_DOMQuery
     */
    public function setDocumentXhtml($document, $encoding = null)
    {
        $this->_document = (string) $document;
        $this->_doc_type  = self::DOC_XHTML;
        if (null !== $encoding) {
            $this->setEncoding($encoding);
        }
        return $this;
    }

    /**
     * Register XML document
     *
     * @param string      $document Document Path
     * @param null|string $encoding Document encoding
     * 
     * @return Vanilla_DOMQuery
     */
    public function setDocumentXml($document, $encoding = null)
    {
        $this->_document = (string) $document;
        $this->_doc_type  = self::DOC_XML;
        if (null !== $encoding) {
            $this->setEncoding($encoding);
        }
        return $this;
    }

    /**
     * Retrieve current document
     *
     * @return string
     */
    public function getDocument()
    {
        return $this->_document;
    }

    /**
     * Get document type
     *
     * @return string
     */
    public function getDocumentType()
    {
        return $this->_doc_type;
    }

    /**
     * Get any DOMDocument errors found
     *
     * @return false|array
     */
    public function getDocumentErrors()
    {
        return $this->_document_errors;
    }


    /**
     * Perform an XPath query
     *
     * @param string|array $xpath_query Xpath query
     * @param string       $query       CSS selector query
     * 
     * @return Vanilla_DOMQuery
     */
    public function queryXpath($xpath_query, $query = null)
    {
        if (null === ($document = $this->getDocument())) {
            throw new Exception('Cannot query; no document registered');
        }

        $encoding = $this->getEncoding();
        libxml_use_internal_errors(true);

        if (null === $encoding) {
            $dom_doc = new DOMDocument('1.0');
        } else {
            $dom_doc = new DOMDocument('1.0', $encoding);
        }

        $type   = $this->getDocumentType();
        switch ($type) {
            case self::DOC_XML:
                $success = $dom_doc->loadXML($document);
                break;
            case self::DOC_HTML:
            case self::DOC_XHTML:
            default:
                $success = $dom_doc->loadHTML($document);
                break;
        }
        $errors = libxml_get_errors();
        if (!empty($errors)) {
            $this->_document_errors = $errors;
            libxml_clear_errors();
        }
        libxml_use_internal_errors(false);

        if (!$success) {
            throw new Exception(sprintf('Error parsing document (type == %s)', $type));
        }

        $node_list   = $this->_getNodeList($dom_doc, $xpath_query);
        return new Vanilla_DOM_Result($query, $xpath_query, $dom_doc, $node_list);
    }

    /**
     * Register XPath namespaces
     *
     * @param array $xpath_namespaces Xpath Namespaces
     * 
     * @return  void
     */
    public function registerXpathNamespaces($xpath_namespaces)
    {
        $this->_xpath_namespaces = $xpath_namespaces;
    }

    /**
     * Prepare node list
     *
     * @param DOMDocument  $document    Document
     * @param string|array $xpath_query XPath Query
     * 
     * @return array
     */
    protected function _getNodeList($document, $xpath_query)
    {
        $xpath  = new DOMXPath($document);
        foreach ($this->_xpath_namespaces as $prefix => $namespace_uri) {
            $xpath->registerNamespace($prefix, $namespace_uri);
        }
        $xpath_query = (string) $xpath_query;
        if (preg_match_all('|\[contains\((@[a-z0-9_-]+),\s?\' |i', $xpath_query, $matches)) {
            foreach ($matches[1] as $attribute) {
                $query_string = '//*[' . $attribute . ']';
                $attribute_name = substr($attribute, 1);
                $nodes = $xpath->query($query_string);
                foreach ($nodes as $node) {
                    $attr = $node->attributes->getNamedItem($attribute_name);
                    $attr->value = ' ' . $attr->value . ' ';
                }
            }
        }
        return $xpath->query($xpath_query);
    }
}