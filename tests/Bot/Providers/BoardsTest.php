<?php

namespace seregazhuk\tests\Bot\Providers;

use seregazhuk\PinterestBot\Api\Providers\Boards;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

/**
 * Class BoardsTest
 * @method Boards getProvider()
 */
class BoardsTest extends BaseProviderTest
{
    /** @test */
    public function it_fetches_boards_for_a_specified_user()
    {
        $provider = $this->getProvider();

        $provider->forUser('johnDoe');

        $request = [
            'username'      => 'johnDoe',
            'field_set_key' => 'detailed',
        ];
        $this->assertWasGetRequest(UrlBuilder::RESOURCE_GET_BOARDS, $request);
    }

    /** @test */
    public function it_fetches_boards_for_current_user()
    {
        $provider = $this->getProvider();
        $this->login();
        $this->setResponse(['username' => 'johnDoe']);

        $provider->forMe();


        // Request to receive user settings
        $this->assertWasGetRequest(UrlBuilder::RESOURCE_GET_USER_SETTINGS);

        // Makes request for the retrieved username from the profile
        $request = [
            'username'      => 'johnDoe',
            'field_set_key' => 'detailed',
        ];
        $this->assertWasGetRequest(UrlBuilder::RESOURCE_GET_BOARDS, $request);
    }

    /** @test */
    public function it_fetches_info_for_a_specified_board()
    {
        $provider = $this->getProvider();

        $provider->info('johnDoe', 'my-board-name');

        $request = [
            'slug'          => 'my-board-name',
            'username'      => 'johnDoe',
            'field_set_key' => 'detailed',
        ];
        $this->assertWasGetRequest(UrlBuilder::RESOURCE_GET_BOARD, $request);
    }

    /** @test */
    public function it_formats_a_board_name_with_spaces_when_fetching_its_info()
    {
        $provider = $this->getProvider();

        $provider->info('johnDoe', 'my board name');

        $request = [
            'slug'          => 'my-board-name',
            'username'      => 'johnDoe',
            'field_set_key' => 'detailed',
        ];
        $this->assertWasGetRequest(UrlBuilder::RESOURCE_GET_BOARD, $request);
    }

    /** @test */
    public function it_creates_a_public_board()
    {
        $provider = $this->getProvider();
        $provider->create('boardName', 'description');

        $request = [
            'name'        => 'boardName',
            'description' => 'description',
            'privacy'     => Boards::BOARD_PRIVACY_PUBLIC,
        ];
        $this->assertWasPostRequest(UrlBuilder::RESOURCE_CREATE_BOARD, $request);
    }

    /** @test */
    public function it_creates_a_private_board()
    {
        $provider = $this->getProvider();
        $provider->createPrivate('boardName', 'description');

        $request = [
            'name'        => 'boardName',
            'description' => 'description',
            'privacy'     => Boards::BOARD_PRIVACY_PRIVATE,
        ];
        $this->assertWasPostRequest(UrlBuilder::RESOURCE_CREATE_BOARD, $request);
    }

    /** @test */
    public function it_fetches_board_titles_suggestions_for_a_specified_pin()
    {
        $provider = $this->getProvider();
        $provider->titleSuggestionsFor('123');

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_TITLE_SUGGESTIONS, ['pin_id' => '123']);
    }

    /** @test */
    public function it_updates_a_board_info()
    {
        $provider = $this->getProvider();
        $provider->update('12345', ['name' => 'new']);

        $expectedRequest = [
            'board_id' => '12345',
            'category' => 'other',
            'name' => 'new',
        ];
        $this->assertWasPostRequest(UrlBuilder::RESOURCE_UPDATE_BOARD, $expectedRequest);
    }

    protected function getProviderClass()
    {
        return Boards::class;
    }
}