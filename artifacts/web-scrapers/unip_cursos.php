<?php

use simplehtmldom\HtmlWeb;
require __DIR__ . '/../../../../../../vendor/autoload.php';

$cursos = getCursos('https://www.unip.br/cursos/graduacao/index.aspx');

$fp = fopen(__DIR__ . '/../scrape/unip-cursos.csv', 'w');
fputcsv($fp, ['title', 'link', 'body']);
foreach ($cursos as $curso) {
  fputcsv($fp, $curso);
}
fclose($fp);


function getCursos(string $url, array $result = []): array {
  $simplehtmldom = new HtmlWeb();
  $html = $simplehtmldom->load($url);
  foreach ($html->find('.mb-4 .cursos') as $card) {
    $title = $card->find('.card-title')[0]->text();
    $link = 'https://www.unip.br/cursos/graduacao/'.$card->find('.card-footer a')[0]->getAttribute('href');

    echo "$link\n";

    $page = $simplehtmldom->load($link);
    $body = $page->find('.container.mt-4 .container .tab-pane')[1]->innerText();

    $result[] = [
      'title' => $title,
      'link' => $link,
      'body' => $body,
    ];
  }
  return $result;
}
