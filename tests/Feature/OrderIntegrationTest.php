<?php

namespace Tests\Feature;

use Tests\TestCase;

class OrderIntegrationTest extends TestCase
{
    public function testBasic(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
