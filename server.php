<?php
/**
 * grpc 服务端
 * 
 */
use Grpc\Parser;
use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Grpc\HelloResponse;

require __DIR__ .'/vendor/autoload.php';

$http = new Server('0.0.0.0', 9505);
$http->set([
    'open_http2_protocol' => true,
]);
$http->on('workerStart', function (Server $server) {
    echo "workerStart \n";
});
$http->on('request', function (Request $request,Response $response) {
    // request_uri 和 proto 文件中 rpc 对应关系: /{package}.{service}/{rpc}
    $path = $request->server['request_uri'];

    if ($path == '/grpc.HelloService/SayHello') {
        // decode, 获取 rpc 中的请求
        $request_message = Parser::deserializeMessage([Grpc\HelloRequest::class, null], $request->rawContent());

        // encode, 返回 rpc 中的应答
        $response_message = new HelloResponse();
        $response_message->setReply('Hello ' . $request_message->getGreeting() . date('r'));
        $response->header('content-type', 'application/grpc');
        $response->header('trailer', 'grpc-status, grpc-message');
        $trailer = [
            "grpc-status" => "0",
            "grpc-message" => ""
        ];
        foreach ($trailer as $trailer_name => $trailer_value) {
            $response->trailer($trailer_name, $trailer_value);
        }
        $response->end(Grpc\Parser::serializeMessage($response_message));
    } else if ($path == '/grpc.Test/Sayxx') {
        // decode, 获取 rpc 中的请求
        $request_message = Parser::deserializeMessage([Grpc\HelloRequest::class, null], $request->rawContent());

        // encode, 返回 rpc 中的应答
        $response_message = new HelloResponse();
        $response_message->setReply('Hello xxx ' . $request_message->getGreeting() . date('r'));
        $response->header('content-type', 'application/grpc');
        $response->header('trailer', 'grpc-status, grpc-message');
        $trailer = [
            "grpc-status" => "0",
            "grpc-message" => ""
        ];
        foreach ($trailer as $trailer_name => $trailer_value) {
            $response->trailer($trailer_name, $trailer_value);
        }
        $response->end(Grpc\Parser::serializeMessage($response_message));
    }
});
$http->start();