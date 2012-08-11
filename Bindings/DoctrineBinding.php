<?php
/**
 * File defining \Backend\Modules\Bindings\DoctrineBinding
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
use Backend\Core\Exceptions\ConfigException;
use Doctrine\ORM\EntityManager;
/**
 * Binding for Doctrine connections.
 *
 * This class assumes that you installed Doctrine using PEAR.
 *     pear channel-discover pear.doctrine-project.org
 *     pear channel-discover pear.symfony.com
 *     pear install --alldeps doctrine/DoctrineORM
 *
 * @category   Backend
 * @package    Modules
 * @subpackage Bindings
 * @author     J Jurgens du Toit <jrgns@jrgns.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
class DoctrineBinding extends DatabaseBinding
{
    /**
     * The Doctrine EntityManager.
     *
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * The Doctrine entity name.
     *
     * @var string
     */
    protected $entityName;

    /**
     * The constructor for the object.
     *
     * The settings array should contain at least the class of the entity to use.
     *
     * @param array $connection The connection settings for the Binding
     */
    public function __construct(array $connection)
    {
        parent::__construct($connection);
        $this->entityName = $connection['class'];
    }

    /**
     * Initialize the connection
     *
     * @param array $connection The connection information for the binding
     *
     * @return Object The current object
     */
    protected function init(array $connection)
    {
        //Setup Doctrine
        $isDevMode = (BACKEND_SITE_STATE != 'production');
        $config    = \Doctrine\ORM\Tools\Setup::createYAMLMetadataConfiguration(
            array(PROJECT_FOLDER . 'configs/doctrine'),
            $isDevMode
        );
        // obtaining the entity manager
        $this->em = EntityManager::create($connection, $config);
    }

    /**
     * Find multiple instances of the resource.
     *
     * Don't specify any criteria to retrieve a full list of instances.
     *
     * @param array $conditions An array of conditions on which to filter the list.
     * @param array $options    An array of options.
     *
     * @return array An array of representations of the resource.
     */
    public function find(array $conditions = array(), array $options = array())
    {
        return $this->em->getRepository($this->entityName)->findAll();
    }

    /**
     * Create an instance on the source, and return the instance.
     *
     * @param array $data The data to create a new resource.
     *
     * @return \Backend\Interfaces\ModelInterface The created model.
     * @throws \RuntimeException When the resource can't be created.
     */
    public function create(array $data)
    {
        $model = new $this->entityName();
        $model->populate($data);
        $this->em->persist($model);
        $this->em->flush();
        return $this->read($model->getId());
    }

    /**
     * Read and return the single, specified instance of the resource.
     *
     * @param mixed $identifier The unique identifier for the instance, or an
     * array containing criteria on which to search for the resource.
     *
     * @return \Backend\Interfaces\ModelInterface The identified model.
     * @throws \RuntimeException When the resource can't be found.
     */
    public function read($identifier)
    {
        if (is_numeric($identifier)) {
            return $this->em->find($this->entityName, $identifier);
        }
        throw new \RuntimeException('Unimplemented');
    }

    /**
     * Refresh the specified instance on the source.
     *
     * This function is the logical counterpart to update, and receives data from
     * the source.
     *
     * @param \Backend\Interfaces\ModelInterface &$model The model to refresh.
     * Passed by reference.
     *
     * @return boolean If the refresh was successful or not.
     * @throws \RuntimeException When the resource can't be refreshed.
     */
    public function refresh(\Backend\Interfaces\ModelInterface &$model)
    {
        throw new \RuntimeException('Unimplemented');
    }

    /**
     * Update the specified instance of the resource.
     *
     * This function is the logical counterpart to refresh, and sends data to
     * the source.
     *
     * @param \Backend\Interfaces\ModelInterface &$model The model to update.
     * Passed by reference.
     *
     * @return boolean If the update was successful or not.
     * @throws \RuntimeException When the resource can't be updated.
     */
    public function update(\Backend\Interfaces\ModelInterface &$model)
    {
        $this->em->persist($model);
        $this->em->flush();
        return $this->read($model->getId());
    }

    /**
     * Delete the specified instance of the resource
     *
     * @param \Backend\Interfaces\ModelInterface &$model The model to delete
     *
     * @return boolean If the deletion was succesful or not.
     * @throws \RuntimeException When the resource can't be deleted.
     */
    public function delete(\Backend\Interfaces\ModelInterface &$model)
    {
        $this->em->remove($model);
        return $this->em->flush();
    }

    /**
     * Magic function to pass on Doctrine function calls to the binding.
     *
     * @param string $method     The name of the method.
     * @param array  $parameters An array of parameters to pass to the method.
     *
     * @return mixed The result of the method call.
     */
    public function __call($method, $parameters)
    {
        if (is_callable(array($this->em, $method))) {
            return call_user_func_array(array($this->em, $method), $parameters);
        } else if (is_callable(array($this->em->getRepository($this->entityName), $method))) {
            return call_user_func_array(
                array($this->em->getRepository($this->entityName), $method), $parameters
            );
        } else {
            throw new \RuntimeException('Unknown method ' . get_class($this) . '::' . $method);
        }
    }
}
