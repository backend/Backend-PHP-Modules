<?php
/**
 * File defining \Backend\Modules\TwigRender
 *
 * PHP Version 5.3
 *
 * @category   Backend
 * @package    Base
 * @subpackage Utilities
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @copyright  2011 - 2012 Jade IT (cc)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
namespace Backend\Modules;
use \Backend\Modules\Render;
use \Backend\Interfaces\RenderInterface;
/**
 * Render Twig templates.
 *
 * @category   Backend
 * @package    Base
 * @subpackage Utilities
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
class TwigRender
    extends \Backend\Modules\Render
    implements \Backend\Interfaces\RenderInterface
{
    /**
     * @var Twig The twig used to render templates
     */
    protected $twig = null;

    /**
     * The constructor for the object
     *
     * The template locations for the Renderer is set in this method
     */
    public function __construct(array $options = array())
    {
        parent::__construct();
        array_unshift($this->templateLocations, SOURCE_FOLDER);
        if (array_key_exists('locations', $options)) {
            $this->templateLocations = array_merge($this->templateLocations, $options['locations']);
            unset($options['locations']);
        }
        $loader     = new \Twig_Loader_Filesystem($this->templateLocations);
        $this->twig = new \Twig_Environment($loader, $options);
    }

    /**
     * Magic function to pass on Twig function calls.
     *
     * @param string $method     The name of the method.
     * @param array  $parameters An array of parameters to pass to the method.
     *
     * @return mixed The result of the method call.
     */
    public function __call($method, $parameters)
    {
        if (method_exists($this->twig, $method)) {
            return call_user_func_array(array($this->twig, $method), $parameters);
        }
        throw new \ErrorException(
            'Call to undefined method ' . get_class($this) . '::' . $method
        );
    }

    /**
     * Render the specified template, using the given values
     *
     * @param string $template The template to render
     * @param array  $values   The values to use to render the template
     *
     * @return string The rendered template
     */
    public function file($template, array $values = array())
    {
        //Use templateFileName instead of templateFile.
        //Twig handles it's own locations
        $file = $this->templateFileName($template);

        $values = array_merge($this->getVariables(), $values);

        return $this->twig->render($file, $values);
    }

    /**
     * Get the file name for the specified template
     *
     * @param string $template The name of the template
     *
     * @return string The template file to render
     */
    protected function templateFileName($template)
    {
        if (substr($template, -5) != '.twig') {
            $template .= '.twig';
        }
        $template = str_replace('\\', DIRECTORY_SEPARATOR, $template);
        return $template;
    }
}
