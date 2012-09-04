<?php
/**
 * File defining \Backend\Modules\Storage\NativeFlash
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
 * A simple Flash Session class.
 *
 * This wraps the Native Session class. The only difference in behaviour is that
 * a value will be removed from the session once it is fetched, and all values are
 * kept in an array stored in the \Backend\Modules\Storage\NativeFlash::BAG_NAME
 * session value.
 *
 * @category   Backend
 * @package    Modules
 * @subpackage SessionManagement
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
class NativeFlash extends NativeSession
{
    /**
     * Prefix to use for naming flash variables.
     */
    const BAG_NAME = 'flash';

    /**
     * The class constructor.
     *
     * @param string $name The session name.
     * @param mixed  $id   The session identifier.
     * @throws \RuntimeException If the session hasn't been started yet.
     */
    public function __construct($name = null)
    {
        if ($this->isOpen() === false) {
            throw new \RuntimeException('No Session to store flash values in');
        }
        $_SESSION[self::BAG_NAME] = array();
        $this->valueBag = &$_SESSION[self::BAG_NAME];
    }

    /**
     * Get a flash session value.
     *
     * The value will be removed after retreiving it, unless you specifically ask
     * to keep it.
     *
     * @param string  $name The name of the value to get.
     * @param boolean $keep If the value should be removed or not.
     *
     * @return mixed The value.
     */
    public function get($name, $keep = false)
    {
        $result = parent::get($name);
        if ($keep === false) {
            self::remove($name);
        }
        return $result;
    }
}
