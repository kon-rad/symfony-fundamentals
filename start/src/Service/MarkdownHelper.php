<?php
namespace App\Service;

use Michelf\MarkdownInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Psr\Log\LoggerInterface;

class MarkdownHelper {

  public function __construct(AdapterInterface $cache, MarkdownInterface $markdown, LoggerInterface $logger, bool $isDebug) {
    $this->cache = $cache;
    $this->markdown = $markdown;
    $this->logger = $logger;
    $this->isDebug = $isDebug;
  }

  public function parse(string $source): string
  {
    if (stripos($source, 'bacon') !== false) {
      $this->logger->info('They are talking about bacon again!');
    }

    if ($this->isDebug) {
      return $this->markdown->transform($source);
    }

    $item = $this->cache->getItem('markdown_'.md5($source));
    if (!$item->isHit()) {
        $item->set($this->markdown->transform($source));
        $this->cache->save($item);
    }

    return $item->get();
  }
}