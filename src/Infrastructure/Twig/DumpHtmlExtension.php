<?php

namespace App\Twig;

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\Yaml\Yaml;
use Twig\Extension\AbstractExtension;
use Twig_Function;
use const JSON_ERROR_NONE;
use function is_string;
use function json_decode;
use function json_last_error;

class DumpHtmlExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new Twig_Function('dump_html', [$this, 'dump'], ['is_safe' => ['html']]),
        ];
    }

    public function dump($variable): ?string
    {
        if (is_string($variable)) {
            $tmp = json_decode($variable, true);

            if (JSON_ERROR_NONE === json_last_error()) {
                $variable = $tmp;
            }
        }

        if (class_exists('Symfony\Component\VarDumper\Dumper\HtmlDumper')) {
            $cloner = new VarCloner();
            $dumper = new HtmlDumper();

            $dumper->dump($cloner->cloneVar($variable), $output = fopen('php://memory', 'r+b'));
            $dumpedData = stream_get_contents($output, -1, 0);
        } elseif (class_exists('Symfony\Component\Yaml\Yaml')) {
            $dumpedData = sprintf('<pre class="sf-dump">%s</pre>', Yaml::dump((array) $variable, 1024));
        } else {
            $dumpedData = sprintf('<pre class="sf-dump">%s</pre>', var_export($variable, true));
        }

        return $dumpedData;
    }
}
