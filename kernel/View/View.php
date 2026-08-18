<?php

declare(strict_types=1);

namespace Kernel\View;

use Smarty\Exception;
use Smarty\Smarty;

final class View
{
    private Smarty $smarty;

    public function __construct()
    {
        $projectRoot = app()->basePath;
        $compileDirectory = $projectRoot . '/cache/smarty';

        if (!is_dir($compileDirectory)) {
            mkdir($compileDirectory, 0775, true);
        }

        $this->smarty = new Smarty();
        $this->smarty->setTemplateDir($projectRoot . '/templates');
        $this->smarty->setCompileDir($compileDirectory);
    }

    /**
     * @throws Exception
     */
    public function render(string $template, array $data = []): string
    {
        $this->smarty->assign($data);

        return $this->smarty->fetch($template);
    }
}
