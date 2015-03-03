<?php
namespace app\components;
 
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\Url;
use yii\helpers\Html;

class SuperMusic extends Component
{
    private function getContent($phrase, $fraza) {
        $url = 'http://www.supermusic.sk/najdi.php';
        $data = [
            'hladane' => $phrase,
            'typhladania' => 'piesen',
            'fraza' => $fraza
        ];
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => "POST",
                'content' => http_build_query($data),
            ]
        ];
        $query = stream_context_create($options);
        return file_get_contents($url, false, $query);
    }

    private function getLinks($content, $document) {
        @$document->loadHTML($content);
        $xpath = new \DOMXPath($document);
        return $xpath->query("//td[@class=\"clanok\"]/a");
    }

    private function parseTags($tags) {
        $tags = preg_replace('/[()\-]/', '', $tags);
        return explode(' a ', $tags);
    }

    private function parseLink($link, $type) {
        $params = [];
        $get_string = parse_url($link->getAttribute('href'), PHP_URL_QUERY);
        parse_str($get_string, $params);
        return Html::a(
            $link->textContent,
            Url::toRoute(array_merge(["site/$type"], $params))
        );
    }

    public function searchSong($phrase, $fraza = 'off') {
        $document = new \DOMDocument;
        $content = $this->getContent($phrase, $fraza);
        $links = $this->getLinks($content, $document);
        $res = [];
        for ($i=0; $i<($links->length-1); $i += 2) {
            $res[] = [
                'song' => $this->parseLink($links->item($i), 'song'),
                'interpret' => $this->parseLink($links->item($i+1), 'interpret'),
                'tags' => $this->parseTags(
                    $document->saveHtml($links->item($i)->nextSibling)
                )
            ];
        }
        return $res;
    }
}
