<?php
namespace ant\file\adapters;

use elFinder;
/**
 * Default elFinder connector
 *
 * @author Dmitry (dio) Levashov
 **/
class ElFinderConnector extends \elFinderConnector
{

    /**
     * Execute elFinder command and output result
     *
     * @return void
     * @throws Exception
     * @author Dmitry (dio) Levashov
     */
    public function run($callback = null)
    {
        $isPost = $this->reqMethod === 'POST';
        $src = $isPost ? array_merge($_GET, $_POST) : $_GET;
        $maxInputVars = (!$src || isset($src['targets'])) ? ini_get('max_input_vars') : null;
        if ((!$src || $maxInputVars) && $rawPostData = file_get_contents('php://input')) {
            // for max_input_vars and supports IE XDomainRequest()
            $parts = explode('&', $rawPostData);
            if (!$src || $maxInputVars < count($parts)) {
                $src = array();
                foreach ($parts as $part) {
                    list($key, $value) = array_pad(explode('=', $part), 2, '');
                    $key = rawurldecode($key);
                    if (preg_match('/^(.+?)\[([^\[\]]*)\]$/', $key, $m)) {
                        $key = $m[1];
                        $idx = $m[2];
                        if (!isset($src[$key])) {
                            $src[$key] = array();
                        }
                        if ($idx) {
                            $src[$key][$idx] = rawurldecode($value);
                        } else {
                            $src[$key][] = rawurldecode($value);
                        }
                    } else {
                        $src[$key] = rawurldecode($value);
                    }
                }
                $_POST = $this->input_filter($src);
                $_REQUEST = $this->input_filter(array_merge_recursive($src, $_REQUEST));
            }
        }

        if (isset($src['targets']) && $this->elFinder->maxTargets && count($src['targets']) > $this->elFinder->maxTargets) {
            $this->output(array('error' => $this->elFinder->error(elFinder::ERROR_MAX_TARGTES)));
        }

        $cmd = isset($src['cmd']) ? $src['cmd'] : '';
        $args = array();

        if (!function_exists('json_encode')) {
            $error = $this->elFinder->error(elFinder::ERROR_CONF, elFinder::ERROR_CONF_NO_JSON);
            $this->output(array('error' => '{"error":["' . implode('","', $error) . '"]}', 'raw' => true));
        }

        if (!$this->elFinder->loaded()) {
            $this->output(array('error' => $this->elFinder->error(elFinder::ERROR_CONF, elFinder::ERROR_CONF_NO_VOL), 'debug' => $this->elFinder->mountErrors));
        }

        // telepat_mode: on
        if (!$cmd && $isPost) {
            $this->output(array('error' => $this->elFinder->error(elFinder::ERROR_UPLOAD, elFinder::ERROR_UPLOAD_TOTAL_SIZE), 'header' => 'Content-Type: text/html'));
        }
        // telepat_mode: off

        if (!$this->elFinder->commandExists($cmd)) {
            $this->output(array('error' => $this->elFinder->error(elFinder::ERROR_UNKNOWN_CMD)));
        }

        // collect required arguments to exec command
        $hasFiles = false;
        foreach ($this->elFinder->commandArgsList($cmd) as $name => $req) {
            if ($name === 'FILES') {
                if (isset($_FILES)) {
                    $hasFiles = true;
                } elseif ($req) {
                    $this->output(array('error' => $this->elFinder->error(elFinder::ERROR_INV_PARAMS, $cmd)));
                }
            } else {
                $arg = isset($src[$name]) ? $src[$name] : '';

                if (!is_array($arg) && $req !== '') {
                    $arg = trim($arg);
                }
                if ($req && $arg === '') {
                    $this->output(array('error' => $this->elFinder->error(elFinder::ERROR_INV_PARAMS, $cmd)));
                }
                $args[$name] = $arg;
            }
        }

        $args['debug'] = isset($src['debug']) ? !!$src['debug'] : false;

        $args = $this->input_filter($args);
        if ($hasFiles) {
            $args['FILES'] = $_FILES;
        }

        try {
			$result = $this->elFinder->exec($cmd, $args);
			if (isset($callback) && is_callable($callback)) {
				call_user_func_array($callback, [$cmd, $args, $result, $this->elFinder]);
			}
            $this->output($result);
        } catch (elFinderAbortException $e) {
            // connection aborted
            // unlock session data for multiple access
            $this->elFinder->getSession()->close();
            // HTTP response code
            header('HTTP/1.0 204 No Content');
            // clear output buffer
            while (ob_get_level() && ob_end_clean()) {
            }
            exit();
        }
    }
}// END class 
