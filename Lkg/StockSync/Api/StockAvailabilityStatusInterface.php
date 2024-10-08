<?php
declare(strict_types=1);
namespace Lkg\StockSync\Api;

interface StockAvailabilityStatusInterface
{
    const SOFORT_LIEFERBAR = 'sofort lieferbar';
    const NOCH_NICHT_ERSCHIENEN = 'noch nicht erschienen';
    const VERGRIFFEN = 'vergriffen';
    const KURZFRISTIG_NICHT_AM_LAGER = 'kurzfristig nicht am Lager';
}
