<?php
/**
 * File defining \Backend\Modules\Bindings\ServiceBinding
 *
 * PHP Version 5.3
 *
 * @category   Backend
 * @package    Modules
 * @subpackage Bindings
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @copyright  2011 - 2012 Jade IT (cc)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
namespace Backend\Modules\Bindings;
use \Backend\Modules\Exception as ModuleException;
/**
 * Service Connection Binding
 *
 * @category   Backend
 * @package    Modules
 * @subpackage Bindings
 * @author     J Jurgens du Toit <jrgns@jrgns.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
abstract class ServiceBinding extends Binding
{
    /**
     * The URL of the service.
     *
     * @var string
     */
    protected $url = null;

    /**
     * The CURL handle
     *
     * @todo refactor the curl functions into a seperate transporte class.
     *
     * @var resource
     */
    protected $chandle = null;

    /**
     * The constructor for the object.
     *
     * @param array $settings The settings for the Service Connection
     *
     * @throws \Backend\Modules\Exception When no URL is specified.
     */
    public function __construct(array $settings)
    {
        parent::__construct($settings);
        if (empty($settings['url'])) {
            throw new ModuleException(
                'No Service settings for ' . $settings['class']
            );
        }
        $this->url = $settings['url'];

        $this->chandle = curl_init();

        //WARNING: this would prevent curl from detecting a 'man in the middle' attack
        //TODO Follow http://ademar.name/blog/2006/04/curl-ssl-certificate-problem-v.html to fix this
        curl_setopt($this->chandle, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($this->chandle, CURLOPT_SSL_VERIFYPEER, 0);

        if (isset($settings['username']) && isset($settings['password'])) {
            curl_setopt($this->chandle, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt(
                $this->chandle, CURLOPT_USERPWD, $settings['username']
                . ':' . $settings['password']
            );
        }
        if (isset($settings['headers']) && is_array($settings['headers'])) {
            $headers = array();
            foreach ($settings['headers'] as $name => $value) {
                $headers[] = $name . ': ' . $value;
            }
            curl_setopt($this->chandle, CURLOPT_HTTPHEADER, $headers);
        }

        curl_setopt($this->chandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->chandle, CURLOPT_HEADER, true);
    }

    /**
     * Execute a GET on the service
     *
     * @param string $path The path to GET
     * @param array  $data Extra data to pass in the request
     *
     * @return boolean
     */
    public function get($path = null, array $data = array())
    {
        curl_setopt($this->chandle, CURLOPT_HTTPGET, 1);
        $path .= '?' . http_build_query($data);
        return $this->execute($path);
    }

    /**
     * Execute a POST on the service
     *
     * @param string $path The path to POST to
     * @param array  $data Extra POST data to add to the request
     *
     * @return boolean
     */
    public function post($path = null, array $data = array())
    {
        curl_setopt($this->chandle, CURLOPT_POST, 1);
        curl_setopt($this->chandle, CURLOPT_POSTFIELDS, $data);
        return $this->execute($path);
    }

    /**
     * Execute the request
     *
     * @param string $path The path to execute the request on.
     *
     * @throws \Backend\Modules\Exception When a curl error occurs.
     * @return mixed|boolean
     */
    public function execute($path = null)
    {
        if ($path && substr($path, 0, 1) != '/' && (substr($this->url, -1) != '/')) {
            $path = '/' . $path;
        }
        curl_setopt($this->chandle, CURLOPT_URL, $this->url . $path);
        //curl_setopt($this->chandle, CURLINFO_HEADER_OUT, true);
        $result = curl_exec($this->chandle);
        if ($result === false) {
            throw new ModuleException(
                'Curl Issue: ' . curl_error($this->chandle),
                curl_errno($this->chandle)
            );
        }
        $code = curl_getinfo($this->chandle, CURLINFO_HTTP_CODE);
        switch ($code) {
        case 200:
            $headerSize = curl_getinfo($this->chandle, CURLINFO_HEADER_SIZE);
            $header = substr($result, 0, $headerSize);
            $body   = substr($result, $headerSize);
            return $this->parse($header, $body);
            break;
        default:
            //var_dump(curl_getinfo($this->chandle));
            //die("<pre>$result");
            throw new \Exception('Error making request: HTTP Code ' . $code);
            break;
        }
        return false;
    }

    /**
     * Parse the response from the service.
     *
     * @param string $header The header part of the response.
     * @param string $body   The body part of the response.
     *
     * @throws \Backend\Modules\Exception When invalid JSON is returned.
     * @return mixed The decoded JSON object, or the body as is.
     */
    public function parse($header, $body)
    {
        if (stripos($header, 'Content-Type: application/json') !== false) {
            $body = json_decode($body);
            $error = json_last_error();
            if ($error) {
                throw new ModuleException('Error Decoding JSON: ' . $error);
            }
        }
        return $body;
    }

    /**
     * Set the URL for the service.
     *
     * @param string $url The URL for the service.
     *
     * @return \Backend\Base\Bindings\ServiceBinding
     */
    public function setUrl($url)
    {
        $this->url = $url;
        if (substr($this->url, -1) != '/') {
            $this->url .= '/';
        }
        return $this;
    }
}
