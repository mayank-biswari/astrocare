<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\UserManagementFilterRequest;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserManagementFilterRequestTest extends TestCase
{
    /**
     * Helper to validate data against the request rules.
     */
    private function validate(array $data): \Illuminate\Validation\Validator
    {
        $request = new UserManagementFilterRequest();
        return Validator::make($data, $request->rules());
    }

    /**
     * Helper to simulate prepareForValidation by creating a request and
     * invoking the protected method via reflection.
     */
    private function prepareRequest(array $data): UserManagementFilterRequest
    {
        $request = UserManagementFilterRequest::create('/test', 'GET', $data);
        $request->setContainer(app());

        $method = new \ReflectionMethod(UserManagementFilterRequest::class, 'prepareForValidation');
        $method->invoke($request);

        return $request;
    }

    // ─── Search Validation ───────────────────────────────────────────────

    #[Test]
    public function valid_search_terms_pass_validation(): void
    {
        $validator = $this->validate(['search' => 'john']);
        $this->assertTrue($validator->passes());

        $validator = $this->validate(['search' => 'a']);
        $this->assertTrue($validator->passes());

        $validator = $this->validate(['search' => str_repeat('a', 100)]);
        $this->assertTrue($validator->passes());
    }

    #[Test]
    public function null_search_passes_validation(): void
    {
        $validator = $this->validate(['search' => null]);
        $this->assertTrue($validator->passes());
    }

    #[Test]
    public function search_exceeding_100_chars_fails_validation(): void
    {
        $validator = $this->validate(['search' => str_repeat('a', 101)]);
        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('search'));
    }

    #[Test]
    public function search_exceeding_100_chars_is_truncated_in_prepare_for_validation(): void
    {
        $longSearch = str_repeat('x', 150);
        $request = $this->prepareRequest(['search' => $longSearch]);

        $this->assertEquals(100, mb_strlen($request->input('search')));
        $this->assertEquals(str_repeat('x', 100), $request->input('search'));
    }

    #[Test]
    public function whitespace_only_search_is_sanitized_to_null(): void
    {
        $request = $this->prepareRequest(['search' => '   ']);
        $this->assertNull($request->input('search'));

        $request = $this->prepareRequest(['search' => "\t\n  "]);
        $this->assertNull($request->input('search'));
    }

    #[Test]
    public function search_with_leading_trailing_whitespace_is_trimmed(): void
    {
        $request = $this->prepareRequest(['search' => '  john  ']);
        $this->assertEquals('john', $request->input('search'));
    }

    // ─── Role Validation ─────────────────────────────────────────────────

    #[Test]
    public function valid_role_values_pass_validation(): void
    {
        foreach (['admin', 'expert', 'user'] as $role) {
            $validator = $this->validate(['role' => $role]);
            $this->assertTrue($validator->passes(), "Role '{$role}' should pass validation");
        }
    }

    #[Test]
    public function null_role_passes_validation(): void
    {
        $validator = $this->validate(['role' => null]);
        $this->assertTrue($validator->passes());
    }

    #[Test]
    public function invalid_role_values_fail_validation(): void
    {
        $invalidRoles = ['superadmin', 'moderator', 'Admin', 'EXPERT', 'manager'];

        foreach ($invalidRoles as $role) {
            $validator = $this->validate(['role' => $role]);
            $this->assertFalse($validator->passes(), "Role '{$role}' should fail validation");
            $this->assertTrue($validator->errors()->has('role'));
        }
    }

    // ─── Date Format Validation ──────────────────────────────────────────

    #[Test]
    public function valid_date_formats_pass_validation(): void
    {
        $today = date('Y-m-d');
        $pastDate = '2023-01-15';

        $validator = $this->validate(['date_from' => $pastDate]);
        $this->assertTrue($validator->passes());

        $validator = $this->validate(['date_to' => $today]);
        $this->assertTrue($validator->passes());

        $validator = $this->validate(['date_from' => $pastDate, 'date_to' => $today]);
        $this->assertTrue($validator->passes());
    }

    #[Test]
    public function null_dates_pass_validation(): void
    {
        $validator = $this->validate(['date_from' => null, 'date_to' => null]);
        $this->assertTrue($validator->passes());
    }

    #[Test]
    public function malformed_dates_fail_validation(): void
    {
        $malformedDates = [
            '15-01-2023',       // DD-MM-YYYY
            '01/15/2023',       // MM/DD/YYYY
            '2023/01/15',       // YYYY/MM/DD
            'not-a-date',       // random string
            '2023-13-01',       // invalid month
            '2023-02-30',       // invalid day
            '20230115',         // no separators
        ];

        foreach ($malformedDates as $date) {
            $validator = $this->validate(['date_from' => $date]);
            $this->assertFalse($validator->passes(), "Date '{$date}' should fail validation");
            $this->assertTrue($validator->errors()->has('date_from'));
        }
    }

    #[Test]
    public function future_dates_are_rejected(): void
    {
        $futureDate = date('Y-m-d', strtotime('+1 day'));

        $validator = $this->validate(['date_from' => $futureDate]);
        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('date_from'));

        $validator = $this->validate(['date_to' => $futureDate]);
        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('date_to'));
    }

    // ─── Cross-field Date Validation ─────────────────────────────────────

    #[Test]
    public function date_from_after_date_to_triggers_validation_error(): void
    {
        $data = [
            'date_from' => '2024-06-15',
            'date_to'   => '2024-06-10',
        ];

        $request = UserManagementFilterRequest::create('/test', 'GET', $data);
        $request->setContainer(app());

        $validator = Validator::make($data, $request->rules());
        $request->withValidator($validator);
        $validator->passes();

        $this->assertTrue($validator->errors()->has('date_from'));
        $this->assertStringContainsString(
            'start date must be before or equal to the end date',
            $validator->errors()->first('date_from')
        );
    }

    #[Test]
    public function date_from_equal_to_date_to_passes_validation(): void
    {
        $date = '2024-01-15';
        $data = [
            'date_from' => $date,
            'date_to'   => $date,
        ];

        $request = UserManagementFilterRequest::create('/test', 'GET', $data);
        $request->setContainer(app());

        $validator = Validator::make($data, $request->rules());
        $request->withValidator($validator);
        $validator->passes();

        $this->assertFalse($validator->errors()->has('date_from'));
    }

    // ─── Sort Validation ─────────────────────────────────────────────────

    #[Test]
    public function valid_sort_by_values_pass_validation(): void
    {
        foreach (['name', 'email', 'role', 'created_at'] as $sortBy) {
            $validator = $this->validate(['sort_by' => $sortBy]);
            $this->assertTrue($validator->passes(), "sort_by '{$sortBy}' should pass validation");
        }
    }

    #[Test]
    public function invalid_sort_by_values_fail_validation(): void
    {
        $invalidValues = ['id', 'password', 'updated_at', 'phone', 'Name', 'EMAIL'];

        foreach ($invalidValues as $value) {
            $validator = $this->validate(['sort_by' => $value]);
            $this->assertFalse($validator->passes(), "sort_by '{$value}' should fail validation");
            $this->assertTrue($validator->errors()->has('sort_by'));
        }
    }

    #[Test]
    public function valid_sort_dir_values_pass_validation(): void
    {
        foreach (['asc', 'desc'] as $dir) {
            $validator = $this->validate(['sort_dir' => $dir]);
            $this->assertTrue($validator->passes(), "sort_dir '{$dir}' should pass validation");
        }
    }

    #[Test]
    public function invalid_sort_dir_values_fail_validation(): void
    {
        $invalidValues = ['ASC', 'DESC', 'ascending', 'up', 'down'];

        foreach ($invalidValues as $value) {
            $validator = $this->validate(['sort_dir' => $value]);
            $this->assertFalse($validator->passes(), "sort_dir '{$value}' should fail validation");
            $this->assertTrue($validator->errors()->has('sort_dir'));
        }
    }

    #[Test]
    public function null_sort_values_pass_validation(): void
    {
        $validator = $this->validate(['sort_by' => null, 'sort_dir' => null]);
        $this->assertTrue($validator->passes());
    }

    // ─── Combined Validation ─────────────────────────────────────────────

    #[Test]
    public function all_valid_parameters_together_pass_validation(): void
    {
        $validator = $this->validate([
            'search'   => 'john',
            'role'     => 'admin',
            'date_from' => '2024-01-01',
            'date_to'   => '2024-06-30',
            'sort_by'  => 'name',
            'sort_dir' => 'asc',
        ]);

        $this->assertTrue($validator->passes());
    }

    #[Test]
    public function empty_request_passes_validation(): void
    {
        $validator = $this->validate([]);
        $this->assertTrue($validator->passes());
    }
}
