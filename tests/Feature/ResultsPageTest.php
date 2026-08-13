<?php

namespace Tests\Feature;

use Tests\TestCase;

class ResultsPageTest extends TestCase
{
    public function test_results_page_is_available(): void
    {
        $response = $this->get('/resultaten');

        $response->assertOk();
        $response->assertSee('Top 10 gemeenten');
        $response->assertSee('EV per 1.000 inwoners');
        $response->assertSee('Elektrische auto');
        $response->assertSee('Aandeel elektrische auto');
        $response->assertSee('EV per 1.000 inwoners in Nederland');
        $response->assertSee('Top 12 provincies');
        $response->assertSee('2015');
    }

    public function test_results_csv_download_is_available(): void
    {
        $response = $this->get('/resultaten/download/csv');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename=klimaatdata-resultaten.csv');
    }

    public function test_results_excel_download_is_available(): void
    {
        $response = $this->get('/resultaten/download/excel');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}
