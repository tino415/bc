<?php

namespace tests\codeception\unit\models;

use Yii;
use yii\codeception\DbTestCase;
use Codeception\Specify;
use app\models\Tag;
use app\models\Document;
use app\models\MapDocumentTag;
use yii\helpers\ArrayHelper;
use tests\codeception\fixtures\DocumentFixture;

class DocumentTest extends DbTestCase {

    use Specify;

    public function fixtures() {
        return [
            'document' => DocumentFixture::className()
        ];
    }
    
    public function testTagGenerationSimple() {

        MapDocumentTag::deleteAll();
        Tag::deleteAll();

        $this->loadFixtures($this->getFixtures());
        

        $model = Document::findOne(1);
        print_r($model->getTagsFromAtts());
        exit();
        $tags = $model->getTagsFromAtts();

        $names = ArrayHelper::getColumn($tags, 'name');
        $counts = ArrayHelper::map($tags, 'name', 'count');
        $types = ArrayHelper::map($tags, 'name', 'type');
        
        $this->specify('Count of inserted tags', function() use($names) {
            expect('There are three different tags', count($names))->equals(3);
        });

        $this->specify('Tags ("interpret", "document", "type")',
        function() use($names) {
            expect('Contains document', in_array('document', $names))->true();
            expect('Contains interpret', in_array('interpret', $names))->true();
            expect('Contains type', in_array('type', $names))->true();
        });

        $this->specify('Tag counts ("interpret", "document", "type")',
        function() use($counts) {
            expect('Count document', $counts['document'])->equals(1);
            expect('Count interpret', $counts['interpret'])->equals(1);
            expect('Count type', $counts['type'])->equals(1);
        });

        $this->specify('Tags have different importance, depends on filed',
        function() use ($types) {
            expect('Document is document name', $types['document'])->equals(5);
            expect('Interpret is interpret name', $types['interpret'])->equals(4);
            expect('Type is document type', $types['type'])->equals(0);
        });
    }

    public function testTagGenereationDuplicity() {
        $model = Document::findOne(2);
        $tags = $model->getTagsFromAtts();

        $names = ArrayHelper::getColumn($tags, 'name');
        $counts = ArrayHelper::map($tags, 'name', 'count');
        $types = ArrayHelper::map($tags, 'name', 'type');

        $this->specify('Count of inserted tags', function() use($names) {
            expect('There are four different tags', count($names))->equals(4);
        });
        
        $this->specify('Tags ("interpret", "document", "type", "document document")',
        function() use($names) {
            expect('Contains document', in_array('document', $names))->true();
            expect('Contains interpret', in_array('interpret', $names))->true();
            expect('Contains type', in_array('type', $names))->true();
            expect('Contains "document document"',
                in_array('document document', $names))->true();
        });

        $this->specify('Test tags counts', function() use ($counts) {
            expect('Count document', $counts['document'])->equals(2);
            expect('Count interpret', $counts['interpret'])->equals(1);
            expect('Count type', $counts['type'])->equals(1);
            expect('Count "document document"',
                $counts['document document'])->equals(1);
        });

        $this->specify('Test types', function() use ($types) {
            expect('Document type', $types['document'])->equals(2);
            expect('Interpret type', $types['interpret'])->equals(4);
            expect('Type type', $types['type'])->equals(0);
            expect('"document document" type', $types['document document'])->equals(5);
        });
    }

    public function testTagGenerationDocumentNameAndInterpretNameTag() {
        $model = Document::findOne(3);
        $tags = $model->getTagsFromAtts();

        $names = ArrayHelper::getColumn($tags, 'name');
        $counts = ArrayHelper::map($tags, 'name', 'count');
        $types = ArrayHelper::map($tags, 'name', 'type');

        $this->specify('Count of inserted tags', function() use($names) {
            expect('Different tags', count($names))->equals(5);
        });

        $this->specify('Tags ("interpret", "document", "type", "document document")',
        function() use($names) {
            expect('Contains document', in_array('document', $names))->true();
            expect('Contains interpret', in_array('interpret', $names))->true();
            expect('Contains type', in_array('type', $names))->true();
            expect('Contains "document interpret"',
                in_array('document interpret', $names))->true();
            expect('Contains "interpret document"',
                in_array('interpret document', $names))->true();
        });

        $this->specify('Test tags counts', function() use ($counts) {
            expect('Count document', $counts['document'])->equals(2);
            expect('Count interpret', $counts['interpret'])->equals(2);
            expect('Count type', $counts['type'])->equals(1);
            expect('Count "document interpret"',
                $counts['document interpret'])->equals(1);
            expect('Count "interpret document"',
                $counts['interpret document'])->equals(1);
        });

        $this->specify('Test types', function() use ($types) {
            expect('Document type', $types['document'])->equals(3);
            expect('Interpret type', $types['interpret'])->equals(3);
            expect('Type type', $types['type'])->equals(0);
            expect('"document interpret" type',
                $types['document interpret'])->equals(4);
            expect('"interepret document" type',
                $types['interpret document'])->equals(5);
        });
    }

    public function testSavingTags() {
        $model = Document::findOne(1);
        $source = [
            [
                'name' => 'tag 1',
                'count' => 2,
                'type' => 3,
            ],
            [
                'name' => 'tag 2',
                'count' => 1,
                'type' => 4,
            ],
            [
                'name' => 'tag 3',
                'count' => 1,
                'type' => 2,
            ]
        ];
        $model->saveTags($source);
        $tags = Tag::find()->indexBy('name')->all();
        $maps = $model->getTags()->indexBy('name')->all();

        $this->specify('Check if tags are in database', function() use($tags) {
            expect('Contains tag 1', array_key_exists('tag 1', $tags))->true();
            expect('Contains tag 2', array_key_exists('tag 2', $tags))->true();
            expect('Contains tag 3', array_key_exists('tag 3', $tags))->true();
        });

        $this->specify('Check if document -> tag mapping exists', 
        function() use($maps) {
            expect('Check mapping of tag 1', array_key_exists('tag 1', $maps))->true();
            expect('Check mapping of tag 2', array_key_exists('tag 2', $maps))->true();
            expect('Check mapping of tag 3', array_key_exists('tag 3', $maps))->true();
        });

        Tag::deleteAll(['name' => ['tag 1', 'tag 2', 'tag 3']]);
    }

    public function testFullFunctionSimple() {
        MapDocumentTag::deleteAll(['document_id' => 1]);
        Tag::deleteAll(['name' => ['document', 'interpret', 'type']]);
        $document = Document::findOne(1);
        $document->createTagsFromAtts();

        $tags = $document->getTags()->indexBy('name')->all();
        $maps = $document->getMapDocumentTags()->indexBy('tag_id')->all();

        $counts = ArrayHelper::map($maps, 'tag_id', 'count');
        $types = ArrayHelper::map($maps, 'tag_id', 'type_id');

        $this->specify('Count of inserted tags', function() use($tags) {
            expect('There are three different tags', count($tags))->equals(3);
        });

        $this->specify('Tags ("interpret", "document", "type")',
        function() use($tags) {
            expect('Contains interpret', array_key_exists('interpret', $tags))->true();
            expect('Contains document', array_key_exists('document', $tags))->true();
            expect('Contains type', array_key_exists('type', $tags))->true();
        });

        $this->specify('Tag counts ("interpret", "document", "type")',
        function() use($counts, $tags) {
            expect('Count document', $counts[$tags['document']->id])->equals(1);
            expect('Count interpret', $counts[$tags['interpret']->id])->equals(1);
            expect('Count type', $counts[$tags['type']->id])->equals(1);
        });

        $this->specify('Tags have different importance, depends on filed',
        function() use ($types, $tags) {
            expect('Document is document name', $types[$tags['document']->id])->equals(5);
            expect('Interpret is interpret name', $types[$tags['interpret']->id])->equals(4);
            expect('Type is document type', $types[$tags['type']->id])->equals(0);
        });

        MapDocumentTag::deleteAll(['document_id' => 1]);
        Tag::deleteAll(['name' => ['document', 'interpret', 'type']]);
    }

    public function testTagAlreadyExists() {
        MapDocumentTag::deleteAll(['document_id' => 1]);
        Tag::deleteAll(['name' => ['document', 'interpret', 'type']]);

        $tag = new Tag;
        $tag->name = 'document';
        $tag->save();

        $document = Document::findOne(1);
        $document->createTagsFromAtts();

        $tags = $document->getTags()->indexBy('name')->all();
        $maps = $document->getMapDocumentTags()->indexBy('tag_id')->all();

        $counts = ArrayHelper::map($maps, 'tag_id', 'count');
        $types = ArrayHelper::map($maps, 'tag_id', 'type_id');

        $this->specify('Count of inserted tags', function() use($tags) {
            expect('There are three different tags', count($tags))->equals(3);
        });

        $this->specify('Tags ("interpret", "document", "type")',
        function() use($tags) {
            expect('Contains interpret', array_key_exists('interpret', $tags))->true();
            expect('Contains document', array_key_exists('document', $tags))->true();
            expect('Contains type', array_key_exists('type', $tags))->true();
        });

        $this->specify('Tag counts ("interpret", "document", "type")',
        function() use($counts, $tags) {
            expect('Count document', $counts[$tags['document']->id])->equals(1);
            expect('Count interpret', $counts[$tags['interpret']->id])->equals(1);
            expect('Count type', $counts[$tags['type']->id])->equals(1);
        });

        $this->specify('Tags have different importance, depends on filed',
        function() use ($types, $tags) {
            expect('Document is document name', $types[$tags['document']->id])->equals(5);
            expect('Interpret is interpret name', $types[$tags['interpret']->id])->equals(4);
            expect('Type is document type', $types[$tags['type']->id])->equals(0);
        });

        MapDocumentTag::deleteAll(['document_id' => 1]);
        Tag::deleteAll(['name' => ['document', 'interpret', 'type']]);
    }
}
