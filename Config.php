<?php
/**
 * File defining Backend\Modules\Config .
 *
 * PHP Version 5.3
 *
 * @category   Backend
 * @package    Core
 * @subpackage Utilities
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @copyright  2011 - 2012 Jade IT (cc)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
namespace Backend\Modules;
use Backend\Core\Exceptions\BackendException;
/**
 * Class to handle application configs.
 *
 * @category   Backend
 * @package    Core
 * @subpackage Utilities
 * @author     J Jurgens du Toit <jrgns@jrgns.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
class Config
{
    /**
     * @var object Store for all the config values.
     */
    protected $values = null;

    /**
     * Parser to parse the config file.
     *
     * @var callable
     */
    protected $parser = null;

    /**
     * Construct the config class.
     *
     * @param mixed $config
     *
     * @return null
     * @todo Allow passing an array of filesnames to parse. This will let you parse
     * default as well as environment
     */
    public function __construct($config)
    {
        switch (true) {
        case is_string($config):
            $this->fromFile($config);
            break;
        case is_array($config):
            $this->_values = $config;
            break;
        }
    }

    public function getParser()
    {
        if (empty($this->parser)) {
            if (function_exists('yaml_parse')) {
                $this->parser = function($yamlString) {
                    return \yaml_parse($yamlString);
                };
            } else if (class_exists('\sfYamlParser')) {
                $this->parser = array(new \sfYamlParser(), 'parse');
            }
        }
        if (!is_callable($this->parser)) {
            throw new \Exception('Could not find Config Parser');
        }
        return $this->parser;
    }

    public function setParser($parser)
    {
        if (!is_callable($parser)) {
            throw new \Exception('Trying to set Uncallable Config Parser');
        }
        $this->parser = $parser;
    }

    protected function fromFile($filename)
    {
        $parser = $this->getParser();
        $this->_values = call_user_func($parser, file_get_contents($filename));
        return $this->_values !== null;
    }

    /**
     * Magic function that returns the config values on request
     *
     * @param string $propertyName The name of the property being accessed
     *
     * @return mixed The value of the property
     */
    public function __get($propertyName)
    {
        if (array_key_exists($propertyName, $this->_values)) {
            return $this->_values[$propertyName];
        }
        return null;
    }

    /**
    * Get the named config value.
    *
    * @param string $name The name of the config value. Omit to get the whole
    * config.
    * @param mixed  $default The default value to return should the value not
    * be found.
    *
    * @return mixed The config setting
    */
    public function get($name = false, $default = null)
    {
        $value = $this->__get($name);
        return $value === null ? $default : $value;
    }
}
