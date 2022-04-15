<?php
/**
 * grpc 客户端
 */
use Swoole\Http2\Request;
use Swoole\Coroutine\Http2\Client;
use function Swoole\Coroutine\run;
use Grpc\Parser;
use Grpc\HelloRequest;

require __DIR__ .'/vendor/autoload.php';

run(function () {
    $domain = '127.0.0.1';
    $cli = new Client($domain, 9505, false);
    $cli->set([
        'timeout' => -1,
        'ssl_host_name' => $domain
    ]);
    $cli->connect();
    $req = new Request();
    $req->method = 'POST';
    $req->path = '/grpc.Test/Sayxx';
    $req->headers = [
        'content-type' => 'application/grpc',
        'te' => 'trailers',
        'user-agent' => 'grpc-swoole',
    ];
    $request = new HelloRequest();
    $req->data = Parser::serializeMessage($request);
    $cli->send($req);
    $response = $cli->recv();
    var_dump(Parser::parseToResultArray($response,['\Grpc\HelloResponse', 'decode'])[0]->getReply());
    var_dump(assert(json_decode($response->data)->error->code === 10002));
});