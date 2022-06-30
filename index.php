<?php
require_once __DIR__ . '/phpQuery/phpQuery.php';


//получение страницы
class getHtmlAsStr
{
    private $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    final public function getHtml(): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$this->url");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }
}
$newPage = new getHtmlAsStr('https://zakupki.gov.ru/epz/order/notice/ea44/view/protocol/protocol-bid-list.html?regNumber=0329200062221006202&protocolId=35530565');
$output = $newPage->getHtml();

$document = phpQuery::newDocument($output);
$data = array();

// получение данных
class getTdInfo
{
    private $tdId;
    private $name;
    private $data;
    private $document;

    public function __construct(string $tdId,string $name,array $data,phpQueryObject $document)
    {
        $this->tdId = $tdId;
        $this->name = $name;
        $this->data = $data;
        $this->document = $document;
    }

    final public function getInfo(): array
    {
        $entry = $this->document->find("table tr td:nth-child($this->tdId)");
        foreach ($entry as $increment => $row ){
            $this->data[$increment]["$this->name"]  = trim(preg_replace('/\s\s+/', ' ', pq($row)->text()));
        }
        return $this->data;
    }
}

$newObject = new getTdInfo('2','№',$data,$document);
$data = $newObject->getInfo();

$newObject = new getTdInfo('3','Наименование',$data,$document);
$data = $newObject->getInfo();

$newObject = new getTdInfo('4','Признак допуска заявки',$data,$document);
$data = $newObject->getInfo();

$newObject = new getTdInfo('5','Порядковый номер',$data,$document);
$data = $newObject->getInfo();


echo "<pre>";
echo json_encode($data,JSON_UNESCAPED_UNICODE);



