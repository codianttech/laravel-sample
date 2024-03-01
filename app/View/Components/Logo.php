<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;

class Logo extends Component
{
    public $logoClass;

    public $logoUrl;

    public $params;

    public $src;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($logoClass, $params = '', $src = '')
    {
        $this->logoClass = $logoClass;
        $this->logoUrl = $this->getImage();
        $this->params = $params;
        $this->src = $src;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|Closure|string
     */
    public function render()
    {
        return view('components.logo');
    }

    /**
     * Method getImage
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getImage()
    {
        $settingLogo = getSetting('app.logo');
        $type = 'bucket';
        if (0 == $settingLogo) {
            $type = 'assets';
            switch ($this->logoClass) {
                case 'logo-dark':
                case 'logo-img-lg':
                case 'logo-img':
                    $settingLogo = 'logo-dark.png';
                    break;

                case 'logo-small':
                    $settingLogo = 'logo-small.png';
                    break;

                default:
                    $settingLogo = $this->src;
                    break;
            }
        }
        if ('url' == $this->logoClass) {
            return url($this->src);
        }

        return getAssetImage($settingLogo, $type);
    }
}
