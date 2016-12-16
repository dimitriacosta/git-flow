<?php

use App\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AuthTest extends TestCase
{
    use DatabaseMigrations;

    private $name = 'John Doe';
    private $email = 'john@example.com';
    private $username = 'johndoe';
    private $password = 'secret';
    private $user;

    /** @test */
    public function create_a_new_user()
    {
        $this->visit('/register')
            ->type($this->name, 'name')
            ->type($this->email, 'email')
            ->type($this->username, 'username')
            ->type($this->password, 'password')
            ->type($this->password, 'password_confirmation')
            ->press('Register')
            ->seeCredentials([
                'name' => $this->name,
                'email' => $this->email,
                'username' => $this->username,
                'password' => $this->password,
            ])
            ->seeIsAuthenticated()
            ->seePageIs('/home');
    }

    /**
     * Create a test user.
     * 
     * @param  array  $attributes
     * @return App\User
     */
    private function user(array $attributes = [])
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        if (!empty($attributes['password'])) {
            $attributes['password'] = bcrypt($attributes['password']);
        }

        return $this->user = factory(User::class)->create($attributes);
    }

    /** @test */
    public function a_user_can_log_in()
    {
        $this->user([
            'username' => $this->username,
            'password' => $this->password,
        ]);

        $this->visit('/login')
            ->type($this->username, 'username')
            ->type($this->password, 'password')
            ->press('Login')
            ->seeCredentials([
                'username' => $this->username,
                'password' => $this->password,
            ])
            ->seeIsAuthenticated()
            ->seePageIs('/home');
    }

    /** @test */
    public function users_can_log_out()
    {
        $user = $this->user();

        $this->actingAs($user)
            ->visit('/home')
            ->press('Logout')
            ->dontSeeIsAuthenticated()
            ->seePageIs('/');
    }
}
