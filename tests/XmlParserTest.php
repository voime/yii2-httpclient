<?php

namespace yiiunit\extensions\httpclient;

use yii\httpclient\XmlParser;
use yii\httpclient\Response;

class XmlParserTest extends TestCase
{
    public function testParse()
    {
        $document = new Response();
        $xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<main>
    <name1>value1</name1>
    <name2>value2</name2>
</main>
XML;
        $document->setContent($xml);

        $data = [
            'name1' => 'value1',
            'name2' => 'value2',
        ];
        $parser = new XmlParser();
        $this->assertEquals($data, $parser->parse($document));
    }

    /**
     * @depends testParse
     */
    public function testParseCData()
    {
        $document = new Response();
        $xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<main>
    <name1><![CDATA[<tag>]]></name1>
    <name2><![CDATA[value2]]></name2>
</main>
XML;
        $document->setContent($xml);

        $data = [
            'name1' => '<tag>',
            'name2' => 'value2',
        ];
        $parser = new XmlParser();
        $this->assertEquals($data, $parser->parse($document));
    }

    /**
     * @depends testParse
     */
    public function testParseEncoding()
    {
        $response = new Response();
        $xml = <<<XML
<?xml version="1.0" encoding="windows-1251"?>
<main>
    <enname>test</enname>
    <rusname>тест</rusname>
</main>
XML;
        $response->setContent($xml);
        $response->addHeaders(['content-type' => 'text/xml; charset=windows-1251']);

        $parser = new XmlParser();
        $data = $parser->parse($response);
        $this->assertEquals('test', $data['enname']);
        $this->assertNotEquals('тест', $data['rusname']); // UTF characters should be broken during parsing by 'windows-1251'
    }
}