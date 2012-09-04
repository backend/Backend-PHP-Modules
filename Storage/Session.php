<?php
/**
 * File defining \Backend\Modules\Storage\Session
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
namespace Backend\Modules\Storage;
use Backend\Interfaces\SessionInterface;
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
abstract class Session implements SessionInterface
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
     * Remove a session value.
     *
     * @param string $name  The name of the value to remove.
     *
     * @return \Backend\Modules\Session
     */
    public function remove($name)
    {
        if (array_key_exists($name, $this->valueBag)) {
            unset($this->valueBag[$name]);
        }
        return $this;
    }

    /**
     * Magic function to get a Property.
     *
     * @param string $name The name of the property.
     *
     * @return mixed The Property if it exists.
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Magic function to check if a Property exists.
     *
     * @param string $name The name of the property to check.
     *
     * @return boolean If the Property exists or not.
     */
    public function __isset($name)
    {
        return array_key_exists($name, $this->valueBag);
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
     * Magic function to set a Property.
     *
     * @param string $name  The name of the value to set.
     * @param mixed  $value The value to set.
     *
     * @return \Backend\Modules\Session
     */
    public function __set($name, $value)
    {
        return $this->set($name, $value);
    }

    /**
     * Set the value bag for the session.
     *
     * @param \ArrayIterator $valueBag The value bag.
     * 
     * @return \Backend\Modules\Session
     */
    public function setValueBag(\ArrayIterator $valueBag)
    {
        $this->valueBag = $valueBag;
        return $this;
    }

    /**
     * Get the value bag for the session.
     * 
     * @return \ArrayIterator The session's value bag.
     */
    public function getValueBag()
    {
        return $this->valueBag;
    }

    /**
     * Iterator function. Return the current value.
     * 
     * @return mixed The current iterator value.
     */
    public function current()
    {
        return $this->valueBag->current();
    }

    /**
     * Iterator function. Return the key of the current value.
     * 
     * @return mixed The key of the current iterator value.
     */
    public function key()
    {
        return $this->valueBag->key();
    }

    /**
     * Iterator function. Return the next value and advance the iterator pointer.
     * 
     * @return mixed The next iterator value.
     */
    public function next()
    {
        return $this->valueBag->next();
    }

    /**
     * Iterator function. Check if the current positions is valid.
     * 
     * @return boolean If the current position is valid.
     */
    public function valid()
    {
        return $this->valueBag->valid();
    }

    /**
     * Iterator function. Rewind the iterator.
     * 
     * @return void
     */
    public function rewind()
    {
        return $this->valueBag->rewind();
    }
}
