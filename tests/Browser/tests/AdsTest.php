<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdsTest extends DuskTestCase
{
    /**
     * Create ad.
     * @group ads
     */
    public function testCtreateAd(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(3))
                ->visit('/ads/create')
                ->type('title', 'Test ad')
                ->type('description', 'Test description')
                ->type('salary', '100')
                ->press('Създай')
                ->assertSee('Обявата е създадена успешно!');
            $browser->driver->manage()->deleteAllCookies();
        });
    }

    /**
     * Edit ad.
     * @group ads
     */
    public function testEditAd(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(3))
                ->visit('/ads/3/edit')
                ->type('title', 'Test ad')
                ->type('description', 'Test description')
                ->type('salary', '200')
                ->press('Създай')
                ->assertSee('Обявата е редактирана успешно!')
                ->assertSee('200 лв.');
            $browser->driver->manage()->deleteAllCookies();
        });
    }

    /**
     * Activate ad.
     * @group ads
     */
    public function testActivateAd(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(3))
                ->visit('/ads/3')
                ->press('Активирай')
                ->assertSee('Активна');
            $browser->driver->manage()->deleteAllCookies();
        });
    }

    /**
     * Deactivate ad.
     * @group ads
     */
    public function testDeactivateAd(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(3))
                ->visit('/ads/3')
                ->press('Деактивирай')
                ->assertSee('Неактивна');
        });
    }

    /**
     * XSS attack.
     * @group ads
     */
    public function testXSSAttack(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(3))
                ->visit('/ads/create')
                ->type('title', '<script>alert("XSS attack")</script>')
                ->type('description', '<script>alert("XSS attack")</script>')
                ->type('salary', '100')
                ->press('Създай')
                ->assertSee('Обявата е създадена успешно!')
                ->assertSee('<script>alert("XSS attack")</script>');
            $browser->driver->manage()->deleteAllCookies();
        });
    }
}
