<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\PaySlip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class PaySlipControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test sending payslip email.
     *
     * @return void
     */
    public function testSendPayslipEmail()
    {
        // Create a test employee
        $employee = Employee::factory()->create();

        // Create a test payslip
        $payslip = PaySlip::factory()->create([
            'employee_id' => $employee->id,
            // Add other necessary attributes
        ]);

        // Mock the Utility::settings() method to return payslip_sent = 1
        $this->mockUtilitySettings(true);

        // Call the send method
        $response = $this->post(route('payslip.send', [
            'id' => $payslip->employee_id,
            'month' => $payslip->salary_month,
        ]));

        // Assert that the response is successful
        $response->assertRedirect()->back()->with('success', __('Payslip successfully sent.'));

        // You can add more assertions here based on your specific requirements
    }

    /**
     * Test viewing payslip PDF.
     *
     * @return void
     */
    public function testViewPayslipPdf()
    {
        // Create a test payslip
        $payslip = PaySlip::factory()->create();

        // Encrypt the payslip ID
        $encryptedId = Crypt::encrypt($payslip->id);

        // Call the payslipPdf method
        $response = $this->get(route('payslip.payslipPdf', $encryptedId));

        // Assert that the response is successful
        $response->assertOk();

        // You can add more assertions here based on your specific requirements
    }

    /**
     * Mock the Utility::settings() method to return a specific value.
     *
     * @param  bool  $value
     * @return void
     */
    protected function mockUtilitySettings($value)
    {
        $this->mock(\Utility::class, function ($mock) use ($value) {
            $mock->shouldReceive('settings')->andReturn(['payslip_sent' => $value]);
        });
    }
}
