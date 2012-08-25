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

    public function setValueBag(\ArrayIterator $valueBag)
    {
        $this->valueBag = $valueBag;
    }

    public function getValueBag()
    {
        return $this->valueBag;
    }

    public function current()
    {
        return $this->valueBag->current();
    }

    public function key()
    {
        return $this->valueBag->key();
    }

    public function next()
    {
        return $this->valueBag->next();
    }

    public function valid()
    {
        return $this->valueBag->valid();
    }

    public function rewind()
    {
        return $this->valueBag->rewind();
    }
}
