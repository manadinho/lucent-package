<?php

namespace Manadinho\Lucent;

use Exception;
use GuzzleHttp\Client;
use Throwable;

/**
 * Class Handler
 * @package Manadinho\Lucent\Handler
 * 
 * @author Muhammad Imran Israr (mimranisrar6@gmail.com)
 */
class Handler
{
    private $stack_trace = null;

    private $request = null;

    private $user = null;

    private $app = null;

    /**
     * Register the given throwable (exception or error) and send it to the exception handler.
     *
     * @param \Throwable $e The throwable to be registered and sent.
     * @return void
     */
    public function register(Throwable $e): void
    {
        $this->user = config('lucent.with_user_details') ? json_encode(auth()->user()) : null;

        $this->prepareStackTrace($e);

        $this->prepareRequest();

        $this->prepareAppDetail();

        $this->sendException();

    }

    /**
     * Send the exception data to the Lucent service.
     *
     * @return bool|string Returns false if Lucent configuration is missing, otherwise returns the response or an error message.
     */
    private function sendException(): bool|string
    {
        if(!config('lucent.lucent_key') || !config('lucent.lucent_url')){
            return false;
        }

        try{
            $client = new Client();

            $response = $client->post(config('lucent.lucent_url').'/register-exception', [
                'headers' => [
                    'Authorization' => 'Bearer '.config('lucent.lucent_key'),
                ],
                'json' => [
                    'stack_trace' => $this->stack_trace,
                    'request_detail' => $this->request,
                    'user' => $this->user,
                    'app' => $this->app
                ]
                ]);
            return true;    
        } catch(Exception $e){
            return false;
        }
        
    }

    /**
     * Prepare application details if enabled in configuration.
     *
     * This function prepares application details if the configuration option
     * 'lucent.with_app_details' is set to true. The application details include
     * PHP version, application environment, Laravel version, and Laravel locale.
     *
     * @return void
     */
    private function prepareAppDetail(): void 
    {
        if (config('lucent.with_app_details')) {
            $this->app = json_encode([
                'php_version' => phpversion(),
                'app_environment' => app()->environment(),
                'laravel_version' => app()->version(),
                'laravel_locale' => app()->getLocale()
            ]);
        }
    }

    /**
     * Prepare the request data to be sent to Lucent.
     *
     * This function prepares the request data by converting the relevant parts of the
     * incoming HTTP request into a JSON representation. It includes the HTTP method,
     * the URL, the headers, and the request body.
     *
     * This function is called when sending data to Lucent if the configuration option
     * 'lucent.with_request_details' is set to true. Otherwise, it does nothing.
     *
     * @return void
     */
    private function prepareRequest(): void
    {
        if (config('lucent.with_request_details')) {
            $this->request = json_encode([
                'method' => request()->method(),
                'url' => request()->url(),
                'headers' => json_encode(request()->headers->all()),
                'body' => request()->all()
            ]);
        }   
    }

    /**
     * Prepares the stack trace for a given Throwable (exception).
     *
     * @param Throwable $e The exception for which the stack trace needs to be prepared.
     * @return void
     */
    private function prepareStackTrace(Throwable $e): void
    {
        [$traces, $code_snippet] = $this->getTracersAndSnippet($e);

        $this->stack_trace = json_encode([
            'exception_name' => get_class($e),
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'occurrence_times' => \Carbon\Carbon::now()->toString(),
            'severity' => 'Error',
            'trace' => json_encode($traces),
            'code_snippet' => json_encode($code_snippet)
        ]);

    }

    /**
     * Get the traces and corresponding code snippets from a Throwable object.
     *
     * @param Throwable $e The throwable object (exception) from which to get tracers and snippets.
     * @return array An array containing two subarrays: one for tracers and one for code snippets.
     */
    private function getTracersAndSnippet(Throwable $e): array
    {
        $traces = [];
        $codeSnippets = [];
        $file_path = str_replace(base_path(), "", $e->getFile());
        
        if(!preg_match('#[\/\\\\]vendor[\/\\\\]#', $file_path)) {
            $codeSnippet = $this->generateCodeSnippet($e->getLine(), $e->getFile());
            $trace = [
                        "file" => str_replace(base_path(), "", $e->getFile()),
                        "line" => $e->getLine()
                    ];
            $traces[] = json_encode($trace);

            $codeSnippets[] = json_encode($codeSnippet); 

            return [$traces, $codeSnippets];
        }

        foreach ($e->getTrace() as $trace) {

            $file_path = str_replace(base_path(), "", $trace['file']);

            if(!preg_match('#[\/\\\\]vendor[\/\\\\]#', $file_path)) {
                $codeSnippet = $this->generateCodeSnippet($trace['line'], $trace['file']);

                $trace['file'] = str_replace(base_path(), "", $trace['file']);

                $traces[] = json_encode($trace);
    
                $codeSnippets[] = json_encode($codeSnippet); 

                break;
            }
        }
      
        return [$traces, $codeSnippets];
    }

    /**
     * Generate the code snippet from a trace.
     *
     * @param string $traceLine Trace line.
     * @param string $traceFile Trace file.
     * @return array An array containing code snippet.
     */
    private function generateCodeSnippet(string $traceLine, string $traceFile): array
    {
        return (new Codesnippet())
        ->surroundingLine($traceLine)
        ->get($traceFile);
    }
}
