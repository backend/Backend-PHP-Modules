<?php
/**
 * File defining \Backend\Modules\Session
 *
 * PHP Version 5.3
 *
 * @category   Backend
 * @package    Modules
 * @subpackage SessionManagement
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @copyright  2011 - 2012 Jade IT (cc)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
namespace Backend\Modules;
/**
 * A simple Session class.
 *
 * @category   Backend
 * @package    Modules
 * @subpackage SessionManagement
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
abstract class Session
{
    /**
     * The value bag for the session.
     *
     * @var ArrayIterator
     */
    protected $valueBag;

    /**
     * Get a session value.
     *
     * @param string $name The name of the value to get.
     *
     * @return mixed The value.
     */
    public function get($name)
    {
        return array_key_exists($name, $this->valueBag) ? $this->valueBag[$name] : null;
    }

    /**
     * Set a session value.
     *
     * @param string $name  The name of the value to set.
     * @param mixed  $value The value to set.
     *
     * @return \Backend\Modules\Session
     */
    public function set($name, $value)
    {
        $this->valueBag[$name] = $value;
        return $this;
    }

    /**
     * Close the session.
     *
     * This will destroy the session and it's associated data.
     *
     * @return \Backend\Modules\Session
     */
    abstract public function close();

    public function setValueBag(\ArrayIterator $valueBag)
    {
        $this->valueBag = $valueBag;
    }

    public function getValueBag()
    {
        return $this->valueBag;
    }
}