<?php

use simplehtmldom\HtmlWeb;
require __DIR__ . '/../../../../../../vendor/autoload.php';

$news = getNews('http://www.portal.facom.ufu.br', '/acontece/2021/08');

$fp = fopen(__DIR__ . '/../scrape/ufbr_noticias.csv', 'w');
fputcsv($fp, ['title', 'link', 'body']);
foreach ($news as $news_item) {
  fputcsv($fp, $news_item);
}
fclose($fp);

function getNews(string $newsUrl, string $pageSegment = '', array $result = []): array {
  $simplehtmldom = new HtmlWeb();
  $html = $simplehtmldom->load($newsUrl . $pageSegment);
  foreach ($html->find('.view-content .views-row') as $newsRow) {
    $title = $newsRow->find('.titulo a')[0] ?? NULL;

    $newsItemUrl = $newsUrl . $title->getAttribute('href');
    echo "$newsItemUrl\n";
    $newsItemPage = $simplehtmldom->load($newsItemUrl);
    $body = $newsItemPage->find('.field-name-body')[0] ?? NULL;

    $result[] = [
      'title' => $title ? $title->innerText() : '',
      'link' => $newsItemUrl,
      'body' => $body ? $body->innerText() : '',
    ];
  }
  if ($nextPage = $html->find('.next a')[0] ?? NULL) {
    $nextPageSegment = $nextPage->getAttribute('href');
    $result = getNews($newsUrl, $nextPageSegment, $result);
  }
  return $result;
}
