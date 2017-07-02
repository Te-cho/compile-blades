<?php

namespace Techo\CompileBlades\Console;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

/**
 * Class CompileBlades
 * @package Techo\CompileBlades\Console\Commands
 */
class CompileBlades extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'compile:blades {blade-name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compile blades into 1 flat file';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $viewName = $this->argument('blade-name');

        $blade = $this->compile(view($viewName)->getPath());
        file_put_contents(view($viewName)->getPath(), $blade);

        $this->comment(PHP_EOL . Inspiring::quote() . PHP_EOL);
    }

    private function compile($viewPath)
    {
        $blade = file_get_contents($viewPath);
        $this->implodeLayout($blade);//A1 @inprogress
        $this->implodeIncludes($blade);//A2 @pending

        return $blade;
    }

    private function implodeLayout(&$blade)
    {
        $sections = $this->seperateSections($blade);//B1 @done
        $this->replaceLayout($blade);//B2 @done
        $this->replaceSections($blade, $sections);//B3 @inprogress


    }

    private function implodeIncludes(&$blade)
    {
        $i = 0;

        // get includes names
        preg_match_all("/@include.*?['|\"](.*?)['|\"]((,)(.*?))?[)]$/im", $blade, $pregOutput);
        while (!empty($pregOutput[0])) {
            // split array from include name
            $includes = $pregOutput[1];
            $arraysSent = $pregOutput[4];
            // split array valriables
            // define variables
            $includesWithVariables = [];
            foreach ($includes as $index => $include) {
                $arrayOfVariables = empty($arraysSent[$index]) ? '[]' : $arraysSent[$index];
                $arrayOfVariablesExtraction = '<?php extract(' . $arrayOfVariables . '); ?>';
                $includesWithVariables[$include] = $arrayOfVariablesExtraction;
            }
            // Include files and append variables
            foreach ($includesWithVariables as $subViewName => $arrayOfVariables) {
                $subView = $arrayOfVariables . "\r\n" . file_get_contents(view($subViewName)->getPath());
                $blade = preg_replace("/@include.*?['|\"]" . $subViewName . "['|\"]((,)(.*?))?[)]$/im", $subView, $blade);
            }
            preg_match_all("/@include.*?['|\"](.*?)['|\"]((,)(.*?))?[)]$/sim", $blade, $pregOutput);
            if (++$i > 2) {
                break;
            }
        }

        return $blade;
    }

    /**
     * Extracts the sections from the blade and cleans the blade from them
     *
     * @param $blade
     *
     * @return array
     * @done
     */
    private function seperateSections(&$blade)
    {
        preg_match_all("/@section.{2}(.*?)'.{1}(?s)(.*?)@stop/si", $blade, $pregOutput);
        $blade = preg_replace("/@section(?s).*?stop/si", "{{-- section was here --}}", $blade);
        $sections = [];
        foreach ($pregOutput[2] as $index => $section) {
            $sections[$pregOutput[1][$index]] = $section;
        }

        return $sections;
    }

    private function replaceLayout(&$blade)
    {

        //find the extended file
        preg_match_all('/@extends[(][\'](.*?)[\'][)]/si', $blade, $output);
        $layout = $output[1][0];
        //take out the extend keyword
        $blade = preg_replace('/@extends[(][\'](.*?)[\'][)]/si', "{{-- Extend layout was here --}}", $blade);
        //bring the layout
        $layout = file_get_contents(view($layout)->getPath());
        $blade = $blade . " " . $layout;

        return $layout;
    }

    private function replaceSections(&$blade, $sections)
    {
        preg_match_all('/@yield[(][\'](.*?)[\'][)]/si', $blade, $output);
        $sectionsName = $output[1];
        foreach ($sectionsName as $sectionName) {
            $sectionNameWithAlt = explode('\', \'', $sectionName);
            if (isset($sections[$sectionNameWithAlt[0]])) {
                $blade = preg_replace(
                    '/@yield[(][\']' . $sectionNameWithAlt[0] . '[\'].*?[)]$/m',
                    $sections[$sectionNameWithAlt[0]],
                    $blade
                );
            } else {
                $blade = preg_replace(
                    '/@yield[(][\']' . $sectionNameWithAlt[0] . '[\'].*?[)]$/m',
                    $sectionNameWithAlt[1] ?? '{{--yield didnt have alternative--}}',
                    $blade
                );
            }
        }

    }
}
