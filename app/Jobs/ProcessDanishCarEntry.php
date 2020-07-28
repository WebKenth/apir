<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

use App\Vehicle;

class ProcessDanishCarEntry implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $payload;

    /**
     * Create a new job instance.
     * @param $payload
     * @return void
     */
    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    public $mapping = [
        'id' => 'KoeretoejIdent',
        'type_id' => 'KoeretoejArtNummer',
        'type_name' => 'KoeretoejArtNavn',
        'usage_number' => 'KoeretoejAnvendelseStruktur.KoeretoejAnvendelseNummer',
        'usage_name' => 'KoeretoejAnvendelseStruktur.KoeretoejAnvendelseNavn',
        'registration_number' => 'RegistreringNummerNummer',
        'registration_status' => 'KoeretoejRegistreringStatus',
        'registration_status_date' => 'KoeretoejRegistreringStatusDato',
        'vehicle_status' => 'KoeretoejOplysningGrundStruktur.KoeretoejOplysningStatus',
        'vehicle_status_date' => 'KoeretoejOplysningGrundStruktur.KoeretoejOplysningStatusDato',
        'vehicle_vin' => 'KoeretoejOplysningGrundStruktur.KoeretoejOplysningStelNummer',
        'vehicle_vin_placement' => 'KoeretoejOplysningGrundStruktur.KoeretoejOplysningStelNummerAnbringelse',
        'vehicle_weight' => 'KoeretoejOplysningGrundStruktur.KoeretoejOplysningTotalVaegt',
        'vehicle_seats' => 'KoeretoejOplysningGrundStruktur.KoeretoejOplysningSiddepladserMaksimum',
        'vehicle_doors' => 'KoeretoejOplysningGrundStruktur.KoeretoejOplysningAntalDoere',
        'vehicle_towing_capable' => 'KoeretoejOplysningGrundStruktur.KoeretoejOplysningTilkoblingMulighed',
        'vehicle_towing_weight_nobreaks' => 'KoeretoejOplysningGrundStruktur.KoeretoejOplysningTilkoblingsvaegtUdenBremser',
        'vehicle_towing_weight_withbreaks' => 'KoeretoejOplysningGrundStruktur.KoeretoejOplysningTilkoblingsvaegtMedBremser',
        'vehicle_model' => 'KoeretoejOplysningGrundStruktur.KoeretoejBetegnelseStruktur.Model.KoeretoejModelTypeNavn',
        'vehicle_brand' => 'KoeretoejOplysningGrundStruktur.KoeretoejBetegnelseStruktur.KoeretoejMaerkeTypeNavn',
        'vehicle_engine' => 'KoeretoejOplysningGrundStruktur.KoeretoejBetegnelseStruktur.Variant.KoeretoejVariantTypeNavn',
        'vehicle_fuel_type' => 'KoeretoejOplysningGrundStruktur.KoeretoejMotorStruktur.DrivkraftTypeStruktur.DrivkraftTypeNavn',
        'vehicle_color' => 'KoeretoejOplysningGrundStruktur.KoeretoejFarveStruktur.FarveTypeStruktur.FarveTypeNavn',
        'vehicle_type' => 'KoeretoejOplysningGrundStruktur.KarrosseriTypeStruktur.KarrosseriTypeNavn.DrivkraftTypeNavn',
        'vehicle_particle_filter' => 'KoeretoejOplysningGrundStruktur.KoeretoejMiljoeOplysningStruktur.KoeretoejMiljoeOplysningPartikelFilter',
        'inspection_date' => 'SynResultatStruktur.SynResultatSynStatusDato',
        'inspection_status' => 'SynResultatStruktur.SynResultatSynStatus',
    ];

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $plate = $this->payload['RegistreringNummerNummer'] ?? 'N/a';

        $this->log('Processing Entry ' . $plate);

        $payload = collect([$this->payload]);
        $data = [];
        foreach ($this->mapping as $key => $lookup) {
            $data[$key] = $payload->pluck($lookup)->first();
        }


        $vehicle = Vehicle::firstOrNew([
            'oid' => $data['id']
        ]);

        $vehicle->raw = $this->payload;
        $vehicle->raw_dot = $this->arrayToDotNotation($this->payload);
        $vehicle->oid = $data['id'];
        $vehicle->plate = $data['registration_number'];
        $vehicle->registration_status = $data['registration_status'];
        $registration_date = $data['registration_status_date'];
        $vehicle->registration_date = $registration_date ? Carbon::createFromTimeString($registration_date) : Carbon::createFromTimestamp(0);
        $vehicle->type = $data['type_name'];
        $vehicle->usage = $data['usage_name'];
        $vehicle->vin = $data['vehicle_vin'];
        $vehicle->model = $data['vehicle_model'];
        $vehicle->brand = $data['vehicle_brand'];
        $vehicle->engine = $data['vehicle_engine'];
        $vehicle->fuel_type = $data['vehicle_fuel_type'];
        $inspection_date = $data['inspection_date'];
        $vehicle->inspection_date = $inspection_date ? Carbon::createFromTimeString($inspection_date) : Carbon::createFromTimestamp(0);
        $vehicle->inspection_status = $data['inspection_status'];

        $vehicle->save();

        $this->log('Processed: ' . $plate);
    }

    public function arrayToDotNotation($array)
    {
        $ritit = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($array));
        $result = [];
        foreach ($ritit as $leafValue) {
            $keys = [];
            foreach (range(0, $ritit->getDepth()) as $depth) {
                $keys[] = $ritit->getSubIterator($depth)->key();
            }
            $result[join('.', $keys)] = $leafValue;
        }
        return $result;
    }

    public function log($message, $type = 'info', array $data = [])
    {
        $job = $this->job;
        $jobId = 0;
        if($job !== null)
        {
            $jobId = $job->getJobId();
        }
        switch ($type) {
            case 'info':
                Log::info('Job [' . $jobId . '] ' . $message, $data);
                break;
            case 'error':
                Log::error('Job [' . $jobId . '] ' . $message, $data);
                break;
            default:
                Log::debug('Job [' . $jobId . '] ' . $message, $data);
        }
    }

    public function failed(\Exception $e)
    {
        $this->log($e->getMessage(), 'error', [
            'code' => $e->getCode(),
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}
