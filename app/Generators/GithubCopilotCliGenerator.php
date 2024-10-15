<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Generators;

use App\Contracts\GeneratorContract;
use Illuminate\Console\OutputStyle;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

final class GithubCopilotCliGenerator implements GeneratorContract
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var \Illuminate\Console\OutputStyle
     */
    private $outputStyle;

    /**
     * @psalm-suppress UndefinedMethod
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->outputStyle = tap(clone resolve(OutputStyle::class))->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
    }

    /**
     * @psalm-suppress UnusedClosureParam
     */
    public function generate(string $prompt): string
    {
        $output = <<<'EOF'

            Welcome to GitHub Copilot in the CLI!
            version 1.0.5 (2024-09-12)

            I'm powered by AI, so surprises and mistakes are possible. Make sure to verify any generated code or suggestions, and share feedback so that we can learn and improve. For more information, see https://gh.io/gh-copilot-transparency


              # Explanation:

              {
              "subject": "feat(Generators): update GithubCopilotCliGenerator to include binary command",
              "body": "- Change the command array in the `resolve` function call to include `['binary', 'copilot', 'explain', $prompt]` as the command\n- Update the `mustRun` function
              callback to handle output formatting\n- Add debug statements to output the generated `$output` variable and perform a `dd()` call\n- Return the generated `$output` variable"
              }



            EOF;
        // $output = resolve(
        //     Process::class,
        //     ['command' => [$this->config['binary'], 'copilot', 'explain', $prompt]] + $this->config['parameters']
        // )->mustRun(function (string $type, string $data): void {
        //     Process::OUT === $type ? $this->outputStyle->write($data) : $this->outputStyle->write("<fg=red>$data</>");
        // })->getOutput();

        return str($output)->match('/\{.*\}/s')->__toString();
    }
}
