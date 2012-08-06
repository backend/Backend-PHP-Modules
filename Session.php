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
class Session
{
    /**
     * The session ID.
     *
     * @var mixed
     */
    protected $id;

    /**
     * The class constructor.
     *
     * @param string $name The session name.
     * @param mixed  $id   The session identifier.
     */
    public function __construct($name = null, $id = null)
    {
        if ($name !== null) {
            session_name($name);
        }
        session_start();
        if ($id !== null ) {
            $this->id = $id;
            session_id($this->id);
        } else {
            $this->id = session_id();
        }
    }

    /**
     * The class destructor.
     */
    public function __destruct()
    {
        session_write_close();
    }

    /**
     * Get a session value.
     *
     * @param string $name The name of the value to get.
     *
     * @return mixed The value.
     */
    public function get($name)
    {
        $result = array_key_exists($name, $_SESSION) ? $_SESSION[$name] : null;
        return $result;
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
        $_SESSION[$name] = $value;
        return $this;
    }

    /**
     * Close the session.
     *
     * This will destroy the session and it's associated data.
     *
     * @return \Backend\Modules\Session
     */
    public function close()
    {
        session_destroy();
        return $this;
    }
}