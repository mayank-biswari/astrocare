<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Prediction;
use App\Models\PaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PredictionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'user']);
    }

    // ---------------------------------------------------------------
    // Yearly Predictions
    // ---------------------------------------------------------------

    public function test_yearly_predictions_requires_authentication(): void
    {
        $response = $this->post('/services/predictions/yearly', [
            'name' => 'Customer 1',
            'email' => 'customer1@example.com',
            'dob' => '2003-01-14',
            'time' => '05:29',
            'place' => 'Delhi',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_yearly_predictions_with_valid_data_creates_prediction_and_redirects_to_checkout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/services/predictions/yearly', [
            'name' => 'Customer 1',
            'email' => 'customer1@example.com',
            'dob' => '2003-01-14',
            'time' => '05:29',
            'place' => 'Delhi',
        ]);

        $response->assertRedirect(route('predictions.checkout'));

        $prediction = Prediction::where('user_id', $user->id)->first();
        $this->assertNotNull($prediction);
        $this->assertEquals('Customer 1', $prediction->name);
        $this->assertEquals('customer1@example.com', $prediction->email);
        $this->assertEquals('Delhi', $prediction->place);
        $this->assertEquals('yearly', $prediction->type);
        $this->assertEquals(999, (int) $prediction->amount);
        $this->assertEquals('pending', $prediction->status);
    }

    public function test_yearly_predictions_validates_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/services/predictions/yearly', []);

        $response->assertSessionHasErrors(['name', 'email', 'dob', 'place']);
    }

    public function test_yearly_predictions_validates_email_format(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/services/predictions/yearly', [
            'name' => 'Customer 1',
            'email' => 'not-an-email',
            'dob' => '2003-01-14',
            'place' => 'Delhi',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_yearly_predictions_validates_dob_is_date(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/services/predictions/yearly', [
            'name' => 'Customer 1',
            'email' => 'customer1@example.com',
            'dob' => 'not-a-date',
            'place' => 'Delhi',
        ]);

        $response->assertSessionHasErrors(['dob']);
    }

    public function test_yearly_predictions_stores_session_checkout_data(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/services/predictions/yearly', [
            'name' => 'Customer 1',
            'email' => 'customer1@example.com',
            'dob' => '2003-01-14',
            'time' => '05:29',
            'place' => 'Delhi',
        ]);

        $response->assertSessionHas('prediction_checkout');
        $checkoutData = session('prediction_checkout');
        $this->assertEquals('Customer 1', $checkoutData['name']);
        $this->assertEquals('Yearly', $checkoutData['type']);
        $this->assertEquals(999, $checkoutData['amount']);
    }

    // ---------------------------------------------------------------
    // Monthly Predictions
    // ---------------------------------------------------------------

    public function test_monthly_predictions_requires_authentication(): void
    {
        $response = $this->post('/services/predictions/monthly', [
            'name' => 'Customer 1',
            'email' => 'customer1@example.com',
            'dob' => '2003-01-14',
            'time' => '05:29',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_monthly_predictions_with_valid_data_creates_prediction_and_redirects_to_checkout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/services/predictions/monthly', [
            'name' => 'Customer 1',
            'email' => 'customer1@example.com',
            'dob' => '2003-01-14',
            'time' => '05:29',
        ]);

        $response->assertRedirect(route('predictions.checkout'));

        $prediction = Prediction::where('user_id', $user->id)->first();
        $this->assertNotNull($prediction);
        $this->assertEquals('Customer 1', $prediction->name);
        $this->assertEquals('customer1@example.com', $prediction->email);
        $this->assertEquals('monthly', $prediction->type);
        $this->assertEquals(299, (int) $prediction->amount);
        $this->assertEquals('pending', $prediction->status);
    }

    public function test_monthly_predictions_validates_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/services/predictions/monthly', []);

        $response->assertSessionHasErrors(['name', 'email', 'dob']);
    }

    public function test_monthly_predictions_stores_session_checkout_data(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/services/predictions/monthly', [
            'name' => 'Customer 1',
            'email' => 'customer1@example.com',
            'dob' => '2003-01-14',
            'time' => '05:29',
        ]);

        $response->assertSessionHas('prediction_checkout');
        $checkoutData = session('prediction_checkout');
        $this->assertEquals('Customer 1', $checkoutData['name']);
        $this->assertEquals('Monthly', $checkoutData['type']);
        $this->assertEquals(299, $checkoutData['amount']);
    }

    // ---------------------------------------------------------------
    // Prediction Checkout Page
    // ---------------------------------------------------------------

    public function test_prediction_checkout_requires_authentication(): void
    {
        $response = $this->get('/services/predictions/checkout');

        $response->assertRedirect('/login');
    }

    public function test_prediction_checkout_requires_session_data(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/services/predictions/checkout');

        $response->assertRedirect(route('predictions.index'));
    }

    public function test_prediction_checkout_displays_correctly(): void
    {
        $user = User::factory()->create(['phone' => '1234567890']);

        \App\Models\Currency::create([
            'code' => 'INR',
            'name' => 'Indian Rupee',
            'symbol' => '₹',
            'exchange_rate' => 1,
            'is_default' => true,
            'is_active' => true,
        ]);

        PaymentGateway::create([
            'name' => 'Cash on Delivery',
            'code' => 'cod',
            'description' => 'Pay on delivery',
            'supported_currencies' => ['INR'],
            'is_active' => true,
            'is_test_mode' => false,
            'sort_order' => 1,
        ]);

        $prediction = Prediction::create([
            'user_id' => $user->id,
            'name' => 'Customer 1',
            'email' => 'customer1@example.com',
            'phone' => '1234567890',
            'dob' => '2003-01-14',
            'type' => 'yearly',
            'amount' => 999,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($user)
            ->withSession([
                'prediction_checkout' => [
                    'prediction_id' => $prediction->id,
                    'name' => 'Customer 1',
                    'type' => 'Yearly',
                    'amount' => 999,
                ]
            ])
            ->get('/services/predictions/checkout');

        $response->assertStatus(200);
        $response->assertSee('Yearly');
        $response->assertSee('999');
    }

    // ---------------------------------------------------------------
    // Place Prediction Order
    // ---------------------------------------------------------------

    public function test_place_prediction_order_requires_authentication(): void
    {
        $response = $this->post('/services/predictions/order/place', [
            'name' => 'Customer 1',
            'email' => 'customer1@example.com',
            'phone' => '1234567890',
            'payment_gateway' => 'cod',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_place_prediction_order_creates_order_and_redirects(): void
    {
        $user = User::factory()->create(['phone' => '1234567890']);

        PaymentGateway::create([
            'name' => 'Cash on Delivery',
            'code' => 'cod',
            'description' => 'Pay on delivery',
            'supported_currencies' => ['INR'],
            'is_active' => true,
            'is_test_mode' => false,
            'sort_order' => 1,
        ]);

        $prediction = Prediction::create([
            'user_id' => $user->id,
            'name' => 'Customer 1',
            'email' => 'customer1@example.com',
            'phone' => '1234567890',
            'dob' => '2003-01-14',
            'type' => 'monthly',
            'amount' => 299,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($user)
            ->withSession([
                'prediction_checkout' => [
                    'prediction_id' => $prediction->id,
                    'name' => 'Customer 1',
                    'type' => 'Monthly',
                    'amount' => 299,
                ]
            ])
            ->post('/services/predictions/order/place', [
                'name' => 'Customer 1',
                'email' => 'customer1@example.com',
                'phone' => '1234567890',
                'payment_gateway' => 'cod',
            ]);

        $order = \App\Models\Order::where('user_id', $user->id)->first();
        $this->assertNotNull($order);

        $response->assertRedirect(route('dashboard.order.details', $order->id));
        $response->assertSessionHas('success');

        $this->assertEquals('App\\Models\\Prediction', $order->orderable_type);
        $this->assertEquals($prediction->id, $order->orderable_id);
        $this->assertEquals(299, (int) $order->total_amount);
        $this->assertEquals('cod', $order->payment_method);
    }

    // ---------------------------------------------------------------
    // Predictions Page
    // ---------------------------------------------------------------

    public function test_predictions_page_loads_successfully(): void
    {
        \App\Models\Currency::create([
            'code' => 'INR',
            'name' => 'Indian Rupee',
            'symbol' => '₹',
            'exchange_rate' => 1,
            'is_default' => true,
            'is_active' => true,
        ]);

        $response = $this->get('/services/predictions');

        $response->assertStatus(200);
        $response->assertSee('Astrological Predictions');
    }
}
