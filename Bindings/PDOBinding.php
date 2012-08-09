<?php
/**
 * File defining \Backend\Modules\Bindings\PDOBinding
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
/**
 * PDO Connection Binding
 *
 * @category   Backend
 * @package    Modules
 * @subpackage Bindings
 * @author     J Jurgens du Toit <jrgns@jrgns.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 * @todo This is a rudimentary implementation of the PDOBinding. It can be
 * improved a lot.
 */
class PDOBinding extends DatabaseBinding
{
    /**
     * The PDO connection for this binding
     *
     * @var PDO
     */
    protected $connection;

    /**
     * The name of the table this binding operates on.
     *
     * @var string
     */
    protected $table;

    /**
     * The constructor for the object.
     *
     * The settings array should contain at least the name of the table to use.
     *
     * @param array $connection The connection settings for the Binding
     */
    public function __construct(array $connection)
    {
        parent::__construct($connection);
        if (empty($connection['table'])) {
            throw new ConfigException(
                'Missing Table for Binding ' . get_class($this)
            );
        }
        $this->table = $connection['table'];
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
        if (empty($connection['driver'])) {
            throw new ConfigException(
                'Missing Driver for Connection ' . $this->_name
            );
        }
        $driver = $connection['driver'];
        unset($connection['driver']);
        if (array_key_exists('username', $connection)) {
            $username = $connection['username'];
            unset($connection['username']);
        } else {
            $username = '';
        }
        if (array_key_exists('password', $connection)) {
            $password = $connection['password'];
            unset($connection['password']);
        } else {
            $password = '';
        }

        //TODO It will be wise to extend the PDOBinding class into driver
        //specific classes at some point
        switch ($driver) {
        case 'sqlite':
            $dsn = $driver . ':' . $connection['path'];
            break;
        default:
            $dsn = $driver . ':' . urldecode(http_build_query($connection, '', ';'));
            break;
        }
        $this->connection = new \PDO($dsn, $username, $password);

        return $this;
    }

    /**
     * Execute a statement on the current connection
     *
     * @param PDOStatement $statement  The statement to execute
     * @param array        $parameters The parameters to use when executing the
     * statement
     *
     * @return PDOStatement The statement after executing it
     */
    protected function executeStatement($statement, array $parameters = array())
    {
        if ($statement && $statement->execute($parameters)) {
            return $statement;
        } else {
            $info = $statement->errorInfo();
            throw new \RuntimeException('PDO Error: ' . $info[2] . ' (' . $info[0] . ')');
        }
    }

    /**
     * Execute a query on the current connection
     *
     * @param string $query      The query to execute
     * @param array  $parameters The parameters to use when executing the query
     *
     * @return PDOStatement The statement of the query that was executed
     */
    public function executeQuery($query, array $parameters = array())
    {
        return $this->executeStatement(
            $this->connection->prepare($query), $parameters
        );
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
        $query = 'SELECT * FROM ' . $this->table;
        if (array_key_exists('order', $options)) {
            $query .= ' ORDER BY ' . $options['order'];
        }
        if (array_key_exists('limit', $options)) {
            $query .= ' LIMIT ' . $options['limit'];
        }
        return $this->executeQuery($query)->fetchAll(
            \PDO::FETCH_CLASS|\PDO::FETCH_PROPS_LATE, $this->className
        );
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
        $query  = 'INSERT INTO ' . $this->table;
        $params = array();
        $values = array();
        $names  = array();
        foreach ($data as $name => $value) {
            $params[':' . $name] = $value;
            $names[]  = $name;
            $values[] = ':' . $name;
        }
        $query .= ' (' . implode(', ', $names) . ') VALUES ('
            . implode(', ', $values) . ')';
        if ($this->executeQuery($query, $params)) {
            return $this->read($this->connection->lastInsertId());
        }
        return false;
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
        $query = 'SELECT * FROM ' . $this->table . ' WHERE `id` = :id';
        $stmt = $this->executeQuery($query, array(':id' => $identifier));
        if ($stmt) {
            $stmt->setFetchMode(
                \PDO::FETCH_CLASS|\PDO::FETCH_PROPS_LATE, $this->className
            );
            return $stmt->fetch();
        }
        return false;
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
        throw new ModuleException('Unimplemented');
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
        $data       = $model->getProperties();
        $identifier = $model->getId();
        $query  = 'UPDATE ' . $this->table . ' SET ';
        $params = array();
        $values = array();
        foreach ($data as $name => $value) {
            $params[':' . $name] = $value;
            $values[] = $name . ' = :' . $name;
        }
        $query .= implode(', ', $values) . ' WHERE id = :identifier';
        $params[':identifier'] = $identifier;
        if ($this->executeQuery($query, $params)) {
            return $this->read($identifier);
        }
        return false;
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
        $identifier = $model->getId();
        $query = 'DELETE FROM ' . $this->table . ' WHERE `id` = :id';
        $result = (bool)$this->executeQuery($query, array(':id' => $identifier));
        unset($model);
        return $result;
    }
}
