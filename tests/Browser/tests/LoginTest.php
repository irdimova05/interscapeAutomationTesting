<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    /**
     * Successful login as completed active user.
     * @group login
     */
    public function testSuccessfulLogin(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'admin@test.com')
                ->type('password', 'admin')
                ->press('Вход')
                ->assertPathIs('/ads');
            $browser->driver->manage()->deleteAllCookies();
        });
    }

    /**
     * Successful login as completed inactive user.
     * @group login
     */
    public function testSuccessfulLoginAsInactiveUser(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'inactive@test.com')
                ->type('password', 'inactive')
                ->press('Вход')
                ->assertPathIs('/inactive-profile');
            $browser->driver->manage()->deleteAllCookies();
        });
    }

    /**
     * Succsessful login as incompleted user.
     * @group login
     */
    public function testLoginAsIncompletedUser(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'complete@test.com')
                ->type('password', 'complete')
                ->press('Вход')
                ->assertPathIs('/register-complete');
            $browser->driver->manage()->deleteAllCookies();
        });
    }

    /**
     * Unsuccessful login.
     * @group login
     */
    public function testUnsuccessfulLogin(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'admin@test.com')
                ->type('password', 'test')
                ->press('Вход')
                ->assertSee('Потребителските данни не съвпадат.');
            $browser->driver->manage()->deleteAllCookies();
        });
    }

    /**
     * Login without credentials.
     * @group login
     */
    public function testLoginWithoutCredentials(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->press('Вход')
                ->assertPathIs('/login');
            $browser->driver->manage()->deleteAllCookies();
        });
    }

    /**
     * Login without email.
     * @group login
     */
    public function testLoginWithoutEmail(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('password', 'test')
                ->press('Вход')
                ->assertPathIs('/login');
            $browser->driver->manage()->deleteAllCookies();
        });
    }

    /**
     * Login without password.
     * @group login
     */
    public function testLoginWithoutPassword(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'admin@test.com')
                ->press('Вход')
                ->assertPathIs('/login');
            $browser->driver->manage()->deleteAllCookies();
        });
    }

    /**
     * Test SQL injection.
     * @group login
     */
    public function testSQLInjection(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', "' OR '1'='1'")
                ->type('password', "' OR '1'='1'")
                ->press('Вход')
                ->assertPathIs('/login');
            $browser->driver->manage()->deleteAllCookies();
        });
    }

    /**
     * Test brute force attack.
     * @group login
     */
    public function testBruteForceAttack(): void
    {
        $usernameList = ['admin@admin.com', 'user1@fs.dsf', 'user2@jds.df', 'user3@dj.dj', 'user4@dhf.djsf', 'user5@ejde.ds', 'user6@fse.esf', 'user7@efd.esd', 'user8@ierf.ef', 'user9@udjf.dfj'];
        $passwordList = ['password', 'password123', '123456', '12345678', 'qwerty', 'abc123', 'monkey', '1234567', 'letmein', 'trustno1'];

        $this->browse(function (Browser $browser) use ($usernameList, $passwordList) {

            foreach ($usernameList as $username) {
                foreach ($passwordList as $password) {
                    $browser->visit('/login')
                        ->type('email', $username)
                        ->type('password', $password)
                        ->press('Вход')
                        ->pause(1000);


                    try {
                        $browser->assertSee('Превишихте лимита на опитите.');
                        break 2;
                    } catch (\Exception $e) {
                    }

                    $browser->assertPathIs('/login');
                }
            }
        });
    }
}
