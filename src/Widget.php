<?php namespace Facuz\Theme;

use Closure;
use Illuminate\View\Factory;
use Illuminate\Config\Repository;


abstract class Widget {

    /**
     * Theme instanced.
     *
     * @var Theme;
     */
    protected $theme;

    /**
     * Repository config.
     *
     * @var \Illuminate\Config\Repository
     */
    protected $config;

    /**
     * Environment view.
     *
     * @var \Illuminate\View\Factory
     */
    protected $view;

    /**
     * Widget file template.
     *
     * @var string
     */
    public $template;

    /**
     * Watching widget template.
     *
     * @var boolean
     */
    public $watch;

    /**
     * Default attributes.
     *
     * @var array
     */
    public $attributes = array();

    /**
     * Attributes including data.
     *
     * @var array
     */
    public $data = array();

    /**
     * Turn on/off widget.
     *
     * @var boolean
     */
    public $enable = true;

    private $path;
    private $message;

    /**
     * Create a new theme instance.
     *
     * @param  Theme                         $theme
     * @param  \Illuminate\Config\Repository $config
     * @param  \Illuminate\View\Factory      $view
     * @return \Facuz\Theme\Widget
     */
    public function __construct(Theme $theme, Repository $config, Factory $view)
    {
        // Theme name.
        $this->theme = $theme;

        // Laravel config
        $this->config = $config;

        $this->view = $view;

        if( $this->enable ) {
            $this->path = 'widget::' . $this->template;

            // If not found in theme widgets directory, try to watch in views/widgets again.
            if( $this->watch === true ) {
                $this->path = $this->theme->getThemeNamespace('widgets.'.$this->template);
            }

            if( ! $this->view->exists($this->path) ) {
                $this->enable = false;

                if( env('APP_DEBUG') ) {
                    $this->enable = true;
                }

                $this->message = "Widget view <strong>$this->path</strong> not found.";
            }
        }
    }

    /**
     * Abstract class init for a widget factory.
     *
     * @return void
     */
    //abstract public function init();

    /**
     * Abstract class run for a widget factory.
     *
     * @return void
     */
    abstract public function run();

    /**
     * Set attributes to object var.
     *
     * @param  arary  $attributes
     * @return void
     */
    public function setAttributes($attributes)
    {
        $this->attributes = array_merge($this->attributes, $attributes);
    }

    /**
     * Set attribute.
     *
     * @param string $key
     * @param mixed $value
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Get attributes.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Get attribute with a key.
     *
     * @param  string  $key
     * @param  string  $default
     * @return mixed
     */
    public function getAttribute($key, $default = null)
    {
        return array_get($this->attributes, $key, $default);
    }

    /**
     * Disble widget.
     *
     * @return void
     */
    protected function disable()
    {
        $this->enable = false;
    }

    /**
     * Start widget factory.
     *
     * @return void
     */
    public function beginWidget()
    {
        // Init widget when enable is true.
        if ($this->enable == true)
        {
            $this->init($this->theme);
        }
    }

    /**
     * End widget factory.
     *
     * @return void
     */
    public function endWidget()
    {
        $data = (array) $this->run();

        $this->data = array_merge($this->attributes, $data);
    }

    /**
     * Watch widget tpl in theme, also app/views/widgets/ too.
     *
     * @param  boolean $bool
     * @return Widget
     */
    public function watch($bool = true)
    {
        $this->watch = $bool;

        return $this;
    }

    /**
     * Render widget to HTML.
     *
     * @throws UnknownWidgetFileException
     * @return string
     */
    public function render()
    {
        if( empty($this->message) ) {
            return $this->view->make($this->path, $this->data)->render();
        }else {
            // throw new UnknownWidgetFileException("Widget view [$this->template] not found.");
            return sprintf(
                '<div class="alert alert-danger alert-widget" style="font-size: 13px; padding: 5px 15px; color: #842029; background-color: #f8d7da; border: 1px solid #f5c2c7;">%1$s</div>',
                $this->message
            );
        }
        // if($this->enable == false) return '';

        // if( isset( $this->data['widget_namespace'] ) && $this->data['widget_namespace'] == 'Modules\Widget\Widgets' ) {
        //     $path = 'widget::' . $this->template;
        // }

        // // If not found in theme widgets directory, try to watch in views/widgets again.
        // if( $this->watch === true ) {
        //     $path = $this->theme->getThemeNamespace('widgets.'.$this->template);
        // }

        // if( empty($path) ) {
        //     throw new UnknownWidgetFileException("Widget [$this->template] undefined variable &#36;path.");
        // }
        
        // // Error file not exists.
        // if( ! $this->view->exists($path) ) {
        //     throw new UnknownWidgetFileException("Widget view [$this->template] not found.");
        // }

        $widget = $this->view->make($path, $this->data)->render();



        return $widget;
    }

}