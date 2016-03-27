<?php
namespace Luzilla;

use Mixpanel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input;
use Symfony\Component\Console\Output\OutputInterface;

class ExportCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('export')
            ->setDescription('Export data')
            ->addArgument(
                'method',
                Input\InputArgument::REQUIRED,
                'Data to export'
            )
            ->addOption(
               'api_key',
               'k',
               Input\InputOption::VALUE_REQUIRED,
               'API key'
            )
            ->addOption(
               'api_secret',
               's',
               Input\InputOption::VALUE_REQUIRED,
               'API secret'
            )
        ;
    }

    protected function execute(Input\InputInterface $input, OutputInterface $output)
    {
        $method = $input->getArgument('method');

        $apiKey = $input->getOption('api_key');
        $apiSecret = $input->getOption('api_secret');

        $mixpanel = new Mixpanel($apiKey, $apiSecret);
        $responseObj = $mixpanel->request(
          [$method],
          ['expire' => $this->createExpire()]
        );

        $response = $this->object2array($responseObj);

        if (array_key_exists('error', $response)) {
            $error = sprintf(
                'There\'s an error: %s, see: %s',
                $response['error'],
                'https://mixpanel.com/docs/api-documentation/data-export-api#error-table'
            );

            throw new \RuntimeException($error);
        }

        if ($response['total'] > $response['results']) {
            throw new \RuntimeException('Time to implement pagination.');
        }

        $results = $response['results'];

        if (0 === count($results)) {
            $output->writeln('Nothing to export.');
            return 0;
        }

        $fileName = sprintf('./var/%s_%s.json', date('Y-m-d_H-i'), $method);
        file_put_contents($fileName, json_encode($responseObj));
    }

    /**
     * Overrides 'expire' Mixpanel::request() because that seems buggy.
     *
     * @param int
     *
     * @return int
     */
    private function createExpire($expireIn = 120)
    {
        $utc = new \DateTime(null, new \DateTimeZone('UTC'));
        $utc->add(\DateInterval::createFromDateString($expireIn . ' seconds'));
        return $utc->getTimestamp();
    }

    private function object2array(\stdClass $object)
    {
        return json_decode(json_encode($object), true);
    }
}
