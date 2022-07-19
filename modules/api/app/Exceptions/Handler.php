<?php

namespace App\Exceptions;

use App\DTO\ErrorDTO;
use App\Exceptions\Exception\ValidatorException;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Throwable $e
     * @return void
     *
     * @throws Exception|Throwable
     */
    public function report(Throwable $e)
    {
        if ($this->shouldntReport($e)) {
            return;
        }

        if (method_exists($e, 'report')) {
            if ($e->report() !== false) {
                return;
            }
        }

        try {
            $logger = app(LoggerInterface::class);
        } catch (Exception $ex) {
            throw $e; // throw the original exception
        }

        $format = '[%s] %d - %s';

        $errorMsg = $e->getMessage();
        $errorCode = $e->getCode();
        $appDebugEnabled = config('app.debug');

        // detect file where error happened

        $classAndFunction = $this->getClassAndFunctionFromTrace($e);

        // process log data output

        $logData = [];

        $msgParts = explode(AppException::LOG_DATA_SEP, $errorMsg);
        if (count($msgParts) > 1) {
            $errorMsg = $msgParts[0];
            $logData['data'] = json_decode($msgParts[1], true);
        }

        if ($appDebugEnabled)
            $logData['exception'] = $e;

        // open stdClass in data (if any)

        if (isset($logData['data']['stdClass']))
            $logData['data'] = $logData['data']['stdClass'];

        // output

        $logger->error(sprintf($format, $classAndFunction, $errorCode, $errorMsg), $logData);
    }

    private function getClassAndFunctionFromTrace(Throwable $e): string
    {
        $foundTrace = null;

        if (count($e->getTrace()) > 0)
            $foundTrace = $e->getTrace()[0];

        foreach ($e->getTrace() as $trace) {
            if (isset($trace['file']) && strpos($trace['file'], 'pipeline/Pipeline.php') !== false) {
                $foundTrace = $trace;
                break;
            }
        }

        $msg = 'Unknown Class';

        if ($foundTrace !== null)
            $msg = $foundTrace['class'] . $foundTrace['type'] . $foundTrace['function'];

        return $msg;
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     * @return JsonResponse
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e): JsonResponse
    {
        // return parent::render($request, $e);

        $status = HttpResponse::HTTP_INTERNAL_SERVER_ERROR;

        if ($e instanceof MethodNotAllowedHttpException) {
            $status = HttpResponse::HTTP_METHOD_NOT_ALLOWED;
            $e = new MethodNotAllowedHttpException([], 'HTTP_METHOD_NOT_ALLOWED', $e);
        } elseif ($e instanceof NotFoundHttpException) {
            $status = HttpResponse::HTTP_NOT_FOUND;
            $e = new NotFoundHttpException('HTTP_NOT_FOUND', $e);
        } elseif ($e instanceof ValidatorException) {
            $status = HttpResponse::HTTP_BAD_REQUEST;
            $e = new ValidatorException($e->getMessage(), $e->getCode(), $e);
        } elseif ($e instanceof AuthorizationException) {
            $status = HttpResponse::HTTP_FORBIDDEN;
            $e = new AuthorizationException('HTTP_FORBIDDEN', $status);
        }

        $errorMsg = $e->getMessage();
        $errorCode = $e->getCode();
        $appDebugEnabled = config('app.debug');

        // remove log data msg part (if any)

        $logDataMsgParts = explode(AppException::LOG_DATA_SEP, $errorMsg);
        if (count($logDataMsgParts) > 1)
            $errorMsg = $logDataMsgParts[0];

        if (is_string($e->getCode()))
            throw new AppException($errorMsg . ' | Code: ' . $e->getCode(), 0);

        // hide error message without code (usually it is a system error)

        if ($appDebugEnabled === false && $errorCode === 0)
            $errorMsg = 'Internal Error Has Occurred';
        else
            $errorMsg = '[DEBUG] ' . $errorMsg;

        $errorDto = new ErrorDTO($errorMsg, $e->getCode());

        return response()->json($errorDto, $status);
    }
}
