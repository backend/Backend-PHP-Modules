<?php
/**
 * File defining \Backend\Modules\Bindings\DatabaseBinding
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
/**
 * Database Connection Binding
 *
 * @category   Backend
 * @package    Modules
 * @subpackage Bindings
 * @author     J Jurgens du Toit <jrgns@jrgns.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
abstract class DatabaseBinding extends Binding
{
    /**
     * The constructor for the object.
     *
     * @param array $connection The connection settings for the Binding
     */
    public function __construct(array $connection)
    {
        parent::__construct($connection);
        $this->init($connection);
    }

    /**
     * Initialize the connection
     *
     * @param array $connection The connection information for the binding
     *
     * @return Object The current object
     */
    protected abstract function init(array $connection);
}
