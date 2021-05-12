<?php

namespace Vitalybaev\GoogleMerchant;

use Sabre\Xml\Service as SabreXmlService;

class Feed
{
    const GOOGLE_MERCHANT_XML_NAMESPACE = 'http://base.google.com/ns/1.0';

    private $namespace;

    /**
     * Feed title.
     *
     * @var string
     */
    private $title;

    /**
     * Link to the store
     *
     * @var string
     */
    private $link;

    /**
     * Feed description
     *
     * @var string
     */
    private $description;

    /**
     * Feed items
     *
     * @var Product[]
     */
    private $items = [];

    /**
     * Rss version attribute
     *
     * @var string
     */
    private $rssVersion;

    /**
     * Feed constructor.
     *
     * @param string $title
     * @param string $link
     * @param string $description
     * @param string $rssVersion
     */
    public function __construct($title, $link, $description, $rssVersion = "")
    {
        $this->title = $title;
        $this->link = $link;
        $this->description = $description;
        $this->rssVersion = $rssVersion;
        $this->namespace = '{'.static::GOOGLE_MERCHANT_XML_NAMESPACE.'}';

    }

    /**
     * Adds product to feed.
     *
     * @param $product
     */
    public function addProduct($product)
    {
        $this->items[] = $product->getXmlStructure($this->namespace);
    }

    /**
     * Set
     * @param $rssVersion
     */
    public function setRssVersion($rssVersion)
    {
        $this->rssVersion = $rssVersion;
    }

    /**
     * Generate string representation of this feed.
     *
     * @return string
     */
    public function build()
    {
        $xmlService = new SabreXmlService();

        $xmlService->namespaceMap[static::GOOGLE_MERCHANT_XML_NAMESPACE] = 'g';

        $xmlStructure = array('channel' => array());

        if (!empty($this->title)) {
            $xmlStructure['channel'][] = [
                'title' => $this->title,
            ];
        }

        if (!empty($this->link)) {
            $xmlStructure['channel'][] = [
                'link' => $this->link,
            ];
        }

        if (!empty($this->description)) {
            $xmlStructure['channel'][] = [
                'description' => $this->description,
            ];
        }

        foreach ($this->items as $item) {
            $xmlStructure['channel'][] = $item;
        }

        return $xmlService->write('rss', new RssElement($xmlStructure, $this->rssVersion));
    }
}
