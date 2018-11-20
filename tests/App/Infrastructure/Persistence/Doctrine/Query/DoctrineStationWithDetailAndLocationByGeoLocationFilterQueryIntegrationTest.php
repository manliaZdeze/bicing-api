<?php

declare(strict_types=1);

namespace tests\App\Infrastructure\Persistence\Doctrine\Query;

use App\Application\UseCase\Filter\ByGeoLocationFilter;
use App\Infrastructure\Persistence\Doctrine\Query\DoctrineStationWithDetailAndLocationByGeoLocationFilterQuery;
use Ramsey\Uuid\Uuid;
use tests\Support\Builder\LocationBuilder;
use tests\Support\Builder\StationBuilder;
use tests\Support\Builder\StationDetailBuilder;
use tests\Support\Builder\StationDetailTypeBuilder;
use tests\Support\TestCase\DatabaseTestCase;

class DoctrineStationWithDetailAndLocationByGeoLocationFilterQueryIntegrationTest extends DatabaseTestCase
{
    /** @var DoctrineStationWithDetailAndLocationByGeoLocationFilterQuery */
    private $query;

    /** @test */
    public function it_can_find_all_station_by_geo_location_filter(): void
    {
        $stationId1 = Uuid::uuid4();
        $stationId2 = Uuid::uuid4();
        $stationId3 = Uuid::uuid4();

        $statusBike = StationDetailTypeBuilder::create()->withTypeBike()->build();
        $statusElectricBike = StationDetailTypeBuilder::create()->withTypeElectricBike()->build();

        $this->buildPersisted(
            StationBuilder::create()
                ->withStationId($stationId1)
                ->withStationDetail(StationDetailBuilder::create()
                    ->withName('50 - AV. PARAL.LEL, 54')
                    ->withType($statusBike)
                    ->build())
                ->withLocation(LocationBuilder::create()
                    ->withAddress('Av. Paral.lel')
                    ->withZipCode('08001')
                    ->withLatitude(41.375)
                    ->withLongitude(2.17035)
                    ->build()),
            StationBuilder::create()
                ->withStationId($stationId2)
                ->withStationDetail(StationDetailBuilder::create()
                    ->withName('114 - PL. JEAN GENET, 1')
                    ->withType($statusElectricBike)
                    ->build())
                ->withLocation(LocationBuilder::create()
                    ->withAddress('Pl. Jean Genet')
                    ->withZipCode('08001')
                    ->withLatitude(41.376801)
                    ->withLongitude(2.173039)
                    ->build()),
            StationBuilder::create()
                ->withStationId($stationId3)
                ->withStationDetail(StationDetailBuilder::create()
                    ->withName('352 - C/RADI, 10/GRAN VIA DE LES CORTS CATALANES')
                    ->withType($statusBike)
                    ->build())
                ->withLocation(LocationBuilder::create()
                    ->withAddress('Radi')
                    ->withZipCode('08098')
                    ->withLatitude(41.36335)
                    ->withLongitude(2.134144)
                    ->build())
        );

        $this->assertEquals([
            [
                'station_id' => $stationId1,
                'name' => '50 - AV. PARAL.LEL, 54',
                'type' => $statusBike,
                'address' => 'Av. Paral.lel',
                'address_number' => null,
                'zip_code' => '08001',
                'latitude' => 41.375,
                'longitude' => 2.17035,
            ],
            [
                'station_id' => $stationId2,
                'name' => '114 - PL. JEAN GENET, 1',
                'type' => $statusElectricBike,
                'address' => 'Pl. Jean Genet',
                'address_number' => null,
                'zip_code' => '08001',
                'latitude' => 41.376801,
                'longitude' => 2.173039,
            ],
        ], $this->query->findAll(ByGeoLocationFilter::fromRawValues(41.373, 2.17031, 800.00)));
    }

    /** {@inheritdoc} */
    protected function setUp()
    {
        parent::setUp();

        $this->query = $this->getContainer()->get('test.app.query.station_with_detail_and_location_by_geo_location_query');
    }

    /** {@inheritdoc} */
    protected function tearDown()
    {
        $this->query = null;

        parent::tearDown();
    }
}
