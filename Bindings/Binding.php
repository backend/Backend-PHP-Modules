<?php
/**
 * File defining \Backend\Modules\Bindings\Binding
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
use \Backend\Interfaces\BindingInterface;
use \Backend\Modules\Exception as ModuleException;
/**
 * Abstract class for Data Bindings
 *
 * Bindings act as a transport layer which can be used to perform CRUD actions
 * on a resource. It's typically used by Models to maintain their state on an
 * outside resource.
 *
 * They also act as Table Gateways
 * (http://www.martinfowler.com/eaaCatalog/tableDataGateway.html) to keep all
 * data source related functionality out of Models.
 *
 * @category   Backend
 * @package    Modules
 * @subpackage Bindings
 * @author     J Jurgens du Toit <jrgns@jrgns.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
abstract class Binding implements BindingInterface
{
    /**
     * The name of the class this binding operates on.
     *
     * @var string
     */
    protected $className;

    /**
     * The constructor for the object.
     *
     * The settings array should contain at least the name of the table to use.
     *
     * @param array $settings The settings for the Binding
     *
     * @throws \Backend\Modules\Exception
     */
    public function __construct(array $settings)
    {
        if (empty($settings['class'])) {
            throw new ModuleException('Missing `class` option for Binding');
        }
        $this->className = $settings['class'];
    }
}
