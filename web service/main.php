<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

$app->get('/{newsType}',function(Request $request, Response $response)
{
	$newsType=$request->getAttribute('newsType');
	$url1="https://newsapi.org/v1/articles?source=".$newsType."&sortBy=top&apiKey=91328d57dc4e4241bd993753219e66a0";
    //$url1="https://newsapi.org/v1/articles?source=bbc-news&sortBy=top&apiKey=91328d57dc4e4241bd993753219e66a0";
    $callAPI=new CallAPI();
    $data=$callAPI->CallNewsAPI($url1);
	$newsData=$callAPI->parseData($data);
	//$newsData = "Hello";
	$translateData=$callAPI->translateAPI($newsData);
	echo $translateData;	
});  

?>