<?php

namespace App\Http\Middleware;

use App\Contracts\LogWriter;
use Closure;
use Illuminate\Http\Request;

class ApiLogger
{
    private $log_writer;

    public function __construct(LogWriter $log_writer)
    {
        $this->log_writer = $log_writer;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // process everything else first...
        $response = $next($request);

        // build the pieces we need for logging...
        $log_pieces = [];
        $log_pieces['TIMESTAMP'] = time();

        $log_pieces['REQUEST_METHOD'] = $request->method();
        $log_pieces['REQUEST_PATH'] = $request->path();
        $log_pieces['REQUEST_FULL_URL'] = $request->fullUrl();
        $log_pieces['REQUEST_IP'] = $request->ip();
        $log_pieces['REQUEST_INPUT'] = json_encode($request->all());

        $request_headers = collect($request->header())->transform(function($item) {
            return $item[0];
        })->toArray();
        $request_headers = array_filter($request_headers, function($key) {
            if (substr($key, 0, 3) == 'sec') {
                return false;
            }
            return true;
        }, ARRAY_FILTER_USE_KEY);
        $log_pieces['REQUEST_HEADERS'] = json_encode($request_headers);

        $log_pieces['RESPONSE_CODE'] = $response->status();
        $log_pieces['RESPONSE_BODY'] = $response->content();

        // write these logs...
        $this->log_writer->writeLogPieces($log_pieces);

        // finish up...
        return $response;
    }
}
