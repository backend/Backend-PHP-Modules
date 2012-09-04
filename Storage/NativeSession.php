<?php
/**
 * File defining \Backend\Modules\Storage\NativeSession
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
class NativeSession extends Session
{
    /**
     * The class constructor.
     *
     * @param string $name The session name.
     * @param mixed  $id   The session identifier.
     */
    public function __construct($name = null)
    {
        if ($name === null) {
            // TODO Some default name?
        }
        $this->open($name);

        $this->valueBag = &$_SESSION;

        if (version_compare(phpversion(), '5.4.0', '>=')) {
            session_register_shutdown();
        } else {
            register_shutdown_function('session_write_close');
        }
    }

    /**
     * Open the session.
     * 
     * @param string $name The Session Name.
     * 
     * @return void
     * @throws RuntimeException If a session already exists.
     * @throws RuntimeException If headers have already been sent.
     * @throws RuntimeException If the session cannot be started.
     */
    public function open($name)
    {
        if ($this->isOpen()) {
            throw new \RuntimeException('A Session already exists. Cannot start a new Session');
        }
        if (ini_get('session.use_cookies') && headers_sent()) {
            throw new \RuntimeException('Headers already sent. Cannot start the Session');
        }
        if ($name !== null) {
            session_name($name);
        }
        if (session_start() === false) {
            throw new \RuntimeException('Could not start Session');
        }
    }

    /**
     * Get a session value.
     *
     * @param string $name The name of the value to get.
     *
     * @return mixed The value.
     * @throws \RuntimeException If the session isn't open.
     */
    public function get($name)
    {
        if ($this->isOpen() === false) {
            throw new \RuntimeException('No Session to retrieve value from');
        }
        return parent::get($name);
    }

    /**
     * Set a session value.
     *
     * @param string $name  The name of the value to set.
     * @param mixed  $value The value to set.
     *
     * @return \Backend\Modules\Session
     * @throws \RuntimeException If the session isn't open.
     */
    public function set($name, $value)
    {
        if ($this->isOpen() === false) {
            throw new \RuntimeException('No Session to set value to');
        }
        return parent::set($name, $value);
    }

    /**
     * Close the session.
     *
     * This will destroy the session and it's associated data.
     *
     * @return \Backend\Modules\Session
     * @throws \RuntimeException If the session isn't open.
     * @throws \RuntimeException If the session couldn't be closed.
     */
    public function close()
    {
        if ($this->isOpen() === false) {
            throw new \RuntimeException('No Session to close');
        }
        if (session_destroy() === false) {
            throw new \RuntimeException('Could not destroy Session');
        }
        return $this;
    }

    /**
     * Check if the session is writable.
     *
     * @return boolean If the session is open.
     */
    public function isOpen()
    {
        return session_id() !== '';
    }
}
