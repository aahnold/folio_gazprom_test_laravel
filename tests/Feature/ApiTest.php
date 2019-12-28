<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiTest extends TestCase
{
    static public $data = [];

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testTokenAcquisition()
    {
        $response = $this->json('POST', route('api.login'), [
            'login' => 'frontend app',
            'password' => $this->app->get('config')->get('api.api_password')
        ], [
            'Content-type' => 'application/json',
            'Accept' => 'application/json'
        ]);

        $response->assertStatus(200);
        $this->assertJson($response->content(), 'Unrecognized response.');
        $json = json_decode($response->content());
        $this->assertObjectHasAttribute('token', $json, 'Token is missing in the response.');
        $this->assertIsString($json->token, 'Unrecognized token.');
        $this->assertNotEmpty($json->token, 'Token is empty.');
        static::$data['token'] = $json->token;
    }

    /**
     * @depends testTokenAcquisition
     */

    public function testBillsGet(){
        $response = $this->get(
            route('api.bills', [$this->app->get('config')->get('api.auth_login')]),
            [
                'Authorization' => 'Bearer ' . static::$data['token'],
                'Accept' => 'application/json'
            ]
        );

        $response->assertStatus(200);
        $this->assertJson($response->content(), 'Unrecognized response.');
        $json = json_decode($response->content());
        $this->assertObjectHasAttribute('Bill', $json, 'Target data unavailable.');
        $this->assertIsArray($json->{"Bill"}, 'Unrecognized target data format.');
        $this->assertNotEmpty($json->{"Bill"}, 'Bills are empty.');
        $this->assertObjectHasAttribute('id', $json->{"Bill"}[0], 'Unrecognized bill format.');
        static::$data['id_bill'] = $json->{"Bill"}[0]->id;
    }

    /**
     * @depends testBillsGet
     */

    public function testCardsGet(){
        $response = $this->get(
            route('api.cards', ['id_bill' => static::$data['id_bill']]),
            [
                'Authorization' => 'Bearer ' . static::$data['token'],
                'Accept' => 'application/json'
            ]
        );

        $response->assertStatus(200);
        $this->assertJson($response->content(), 'Unrecognized response.');
        $json = json_decode($response->content());
        $this->assertObjectHasAttribute('Card', $json, 'Target data unavailable.');
        $this->assertIsArray($json->{"Card"}, 'Unrecognized target data format.');
        $this->assertNotEmpty($json->{"Card"}, 'Cards are empty.');
        $this->assertObjectHasAttribute('id', $json->{"Card"}[0], 'Unrecognized card format.');
        static::$data['id_card'] = $json->{"Card"}[0]->id;
    }

    public function testCardsGetWithPaginationAndFiltering(){
        $response = $this->get(
            route(
                'api.cards',
                [
                    'id_bill' => static::$data['id_bill'],
                    'page' => 2,
                    'search' => 'нужная'
                ]
            ),
            [
                'Authorization' => 'Bearer ' . static::$data['token'],
                'Accept' => 'application/json'
            ]
        );

        $response->assertStatus(200);
        $this->assertJson($response->content(), 'Unrecognized response.');
        $json = json_decode($response->content());
        $this->assertObjectHasAttribute('Card', $json, 'Target data unavailable.');
        $this->assertIsArray($json->{"Card"}, 'Unrecognized target data format.');
        $this->assertNotEmpty($json->{"Card"}, 'Cards are empty.');
        $this->assertObjectHasAttribute('id', $json->{"Card"}[0], 'Unrecognized card format.');
        $this->assertNotEquals(static::$data['id_card'], $json->{"Card"}[0]->id, 'Pagination doesn\'t work as expected.');
        $this->assertStringContainsString('нужная', $json->{"Card"}[0]->description, 'Search result is incorrect.');
    }

    /**
     * @depends testCardsGet
     */

    public function testCardDetailsGet(){
        $response = $this->get(
            route('api.card_detail', ['id_card' => static::$data['id_card']]),
            [
                'Authorization' => 'Bearer ' . static::$data['token'],
                'Accept' => 'application/json'
            ]
        );

        $response->assertStatus(200);
        $this->assertJson($response->content(), 'Unrecognized response.');
        $json = json_decode($response->content());
        $this->assertObjectHasAttribute('id', $json, 'Unrecognized target data format.');
        $this->assertEquals(static::$data['id_card'], $json->id, 'Api method returned erroneous result.');
    }

    public function testDictionaryGet(){
        $response = $this->get(
            route('api.dictionary', ['name' => 'BillStatus']),
            [
                'Authorization' => 'Bearer ' . static::$data['token'],
                'Accept' => 'application/json'
            ]
        );

        $response->assertStatus(200);
        $this->assertJson($response->content(), 'Unrecognized response.');
        $json = json_decode($response->content());
        $this->assertObjectHasAttribute('DictionaryItem', $json, 'Unrecognized target data format.');
        $this->assertIsArray($json->{"DictionaryItem"}, 'Unrecognized target data format.');
        $this->assertNotEmpty($json->{"DictionaryItem"}, 'Dictionary items are empty.');
        $this->assertObjectHasAttribute('id', $json->{"DictionaryItem"}[0], 'Unrecognized dictionary format.');

        $bill_status_value = $json->{"DictionaryItem"}[0]->value;

        $response = $this->get(
            route('api.dictionary', ['name' => 'CardType']),
            [
                'Authorization' => 'Bearer ' . static::$data['token'],
                'Accept' => 'application/json'
            ]
        );

        $response->assertStatus(200);
        $this->assertJson($response->content(), 'Unrecognized response.');
        $json = json_decode($response->content());
        $this->assertObjectHasAttribute('DictionaryItem', $json, 'Unrecognized target data format.');
        $this->assertIsArray($json->{"DictionaryItem"}, 'Unrecognized target data format.');
        $this->assertNotEmpty($json->{"DictionaryItem"}, 'Dictionary items are empty.');
        $this->assertObjectHasAttribute('id', $json->{"DictionaryItem"}[0], 'Unrecognized dictionary format.');

        $card_type_value = $json->{"DictionaryItem"}[0]->value;

        $this->assertNotEquals($bill_status_value, $card_type_value, 'Api method returned erroneous result.');
    }
}
