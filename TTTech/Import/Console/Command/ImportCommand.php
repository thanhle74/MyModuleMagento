<?php
declare(strict_types=1);
namespace TTTech\Import\Console\Command;

use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use TTTech\Import\Model\ImportProduct;
use TTTech\Import\Helper\Data as HelperData;

class ImportCommand extends Command
{
    /**
     * @var ImportProduct
     */
    protected ImportProduct $importProduct;

    /**
     * @var State
     */
    protected State $state;

    /**
     * Summary of helperData
     * @var HelperData
     */
    protected HelperData $helperData;

    const COMMAND_NAME = 'tttech:import:products';

    /**
     * Summary of __construct
     * @param \TTTech\Import\Model\ImportProduct $importProduct
     * @param \Magento\Framework\App\State $state
     * @param \TTTech\Import\Helper\Data $helperData
     */
    public function __construct(
        ImportProduct $importProduct,
        State $state,
        HelperData $helperData,
    ) {
        $this->importProduct = $importProduct;
        $this->state = $state;
        $this->helperData = $helperData;
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)->setDescription('Imports products from external API');
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            if((int)$this->helperData->statusModule()) {
                $this->state->setAreaCode('adminhtml');
                $this->importProduct->import($output);
            }
        } catch (\Exception $e) {
            $output->writeln('Error: ' . $e->getMessage());
        }

        return Cli::RETURN_SUCCESS;
    }
}
