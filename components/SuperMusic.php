<?php
namespace app\components;
 
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\Url;
use yii\helpers\Html;
use app\models\Song;

class SuperMusic extends Component {

    private function getPost($url, $params) {
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($params),
            ]
        ];
        $query = stream_context_create($options);
        return file_get_contents($url, false, $query);
    }

    private function getContent($phrase, $type, $fraza) {
        return $this->getPost(
            'http://www.supermusic.sk/najdi.php',
            [
                'hladane' => $phrase,
                'typhladania' => $type, 
                'fraza' => $fraza
            ]
        );
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
        $content = $this->getContent($phrase, 'piesen', $fraza);
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

    public function getSong($bandId, $songId) {
        $document = new \DOMDocument;
        $content = $this->getPost(
            'http://www.supermusic.sk/skupina.php',
            [
                'action' => 'piesen',
                'idskupiny' => $bandId,
                'idpiesne' => $songId,
            ]
        );
        @$document->loadHTML($content);
        $xpath = new \DOMXPath($document);
        $model = new Song();
        $model->body = $document->saveHtml(
            $xpath->query('//td[@class="piesen"]/font')->item(0)
        );
        $model->name = $xpath->query('//font[@class="test3"]')->item(0)->textContent;
        return $model;
    }
}
