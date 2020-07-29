<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ProcessDanishCarEntry;
use Illuminate\Support\Facades\Log;

class ConsumeDanishXML extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consume:danish:xml {filepath} {node}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processes an XML file and creates jobs for Processing Danish Car Entries';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filepath = $this->argument('filepath');
        $path = base_path() . '/' . $filepath;
        $nodeName = $this->argument('node');

        $bar = $this->output->createProgressBar();

        $bar->start();

        $reader = new \XMLReader;
        $reader->open($path);

        $doc = new \DOMDocument;

        while ($reader->read() && $reader->name !== $nodeName);

        while ($reader->name === $nodeName) {
            $dom = $doc->importNode($reader->expand(), true);
            $bar->advance();
            $data = $this->processDom($dom);

            $this->dispatchJob($data);
            usleep(1000);

            $reader->next($dom->localName);
        }
        $bar->finish();
    }

    public function dispatchJob($data, $level = 0)
    {
        try {
            ProcessDanishCarEntry::dispatch($data);
        } catch (\Throwable $t) {
            dump($t->getMessage());
            Log::error('ConsumeDanishXML :: ' . $t->getMessage());
            sleep(5);
            if ($level === 20) {
                dd($t->getMessage());
            }
            $this->dispatchJob($data, $level + 1);
        }
    }

    public function processDom(\DOMNode $node)
    {
        $data = [];
        /** @var \DomNode $childNode */
        foreach ($node->childNodes as $childNode) {
            if ($childNode->nodeName === '#text') {
                continue;
            }
            $childData = $this->processDom($childNode);
            if ($childData === null || $childData === []) {
                $data[$childNode->localName] = $childNode->nodeValue;
            } else {
                $data[$childNode->localName] = $childData;
            }
        }
        return $data;
    }
}
