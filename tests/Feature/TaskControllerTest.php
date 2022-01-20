<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Task;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;
    private $companyDataRequestRoute = 'api/v1/company';
    private $checkProgressRoute = 'api/v1/getTaskProgress';
    private $getCompanyDataRoute = 'api/v1/getTaskData';

    public function test_create_company_request_success()
    {
        $user = User::factory()->create();

        $response = $this->withToken($user->token)->postJson($this->companyDataRequestRoute, ['company_name' => '123123123', 'company_domain' => 'test.com']);

        $response->assertStatus(201)->assertJson([
            'data' => [
                'message' => 'Task has been successfully stored. Please use the link below to track its progress. When completed you will receive an email. Do not forget your token.',
                'progress_link' => '/api/v1/getTaskProgress?task_id='. Task::first()->id,
            ]
        ]);
    }

    public function test_create_company_request_success_no_company_name()
    {
        $user = User::factory()->create();

        $response = $this->withToken($user->token)->postJson($this->companyDataRequestRoute, ['company_domain' => 'test.com']);

        $response->assertStatus(201)->assertJson([
            'data' => [
                'message' => 'Task has been successfully stored. Please use the link below to track its progress. When completed you will receive an email. Do not forget your token.',
                'progress_link' => '/api/v1/getTaskProgress?task_id='. Task::first()->id,
            ]
        ]);
    }

    public function test_create_company_request_fail_no_domain()
    {
        $user = User::factory()->create();

        $response = $this->withToken($user->token)->postJson($this->companyDataRequestRoute, ['company_name' => '123123123']);

        $response->assertStatus(422)->assertJson([
            'message' => 'Validation error',
            'details' => [
                'company_domain'=> [
                    'The company domain field is required.'
                ]
            ]
        ]);
    }

    public function test_check_progress_success()
    {
        $user = User::factory()->create();

        $taskResponse = $this->withToken($user->token)->postJson($this->companyDataRequestRoute, ['company_domain' => 'test.com']);

        $progressResponse = $this->withToken($user->token)->postJson($this->checkProgressRoute, ['task_id' => Task::first()->id]);

        $progressResponse->assertStatus(200);
    }

    public function test_check_progress_fail_task_belongs_to_another()
    {
        $userOne = User::factory()->create();
        $userTwo = User::factory()->create();

        $taskResponseUserOne = $this->withToken($userOne->token)->postJson($this->companyDataRequestRoute, ['company_domain' => 'test.com']);
        $taskResponseUserTwo = $this->withToken($userTwo->token)->postJson($this->companyDataRequestRoute, ['company_domain' => 'test.com']);

        $progressResponse = $this->withToken($userOne->token)->postJson($this->checkProgressRoute, ['task_id' => Task::where('user_id', $userTwo->id)->first()->id]);

        $progressResponse->assertStatus(403);
    }

    public function test_check_progress_fail_task_invalid_id()
    {
        $user = User::factory()->create();

        $taskResponseUser = $this->withToken($user->token)->postJson($this->companyDataRequestRoute, ['company_domain' => 'test.com']);

        $progressResponse = $this->withToken($user->token)->postJson($this->checkProgressRoute, ['task_id' => 123]);

        $progressResponse->assertStatus(403);
    }

    public function test_get_company_data_success()
    {
        $user = User::factory()->create();

        $taskResponseUser = $this->withToken($user->token)->postJson($this->companyDataRequestRoute, ['company_domain' => 'test.com']);

        $companyDataResponse = $this->withToken($user->token)->postJson($this->getCompanyDataRoute, ['company_domain' => 'test.com']);

        $companyDataResponse->assertStatus(200)->assertJsonStructure(
            [
                'data' => [
                    'notified_at', 'company_data', 'error'
                ]
            ]
        );
    }

    public function test_company_data_belongs_to_another_fail()
    {
        $userOne = User::factory()->create();
        $userTwo = User::factory()->create();

        $taskResponseUserOne = $this->withToken($userOne->token)->postJson($this->companyDataRequestRoute, ['company_domain' => 'test.com']);
        $taskResponseUserTwo = $this->withToken($userTwo->token)->postJson($this->companyDataRequestRoute, ['company_domain' => 'test2.com']);

        $companyDataResponse = $this->withToken($userOne->token)->postJson($this->getCompanyDataRoute, ['company_domain' => 'test2.com']);

        $companyDataResponse->assertStatus(403);
    }

    public function test_get_company_data_fail_domain_missing()
    {
        $user = User::factory()->create();

        $taskResponseUser = $this->withToken($user->token)->postJson($this->companyDataRequestRoute, ['company_domain' => 'test.com']);

        $companyDataResponse = $this->withToken($user->token)->postJson($this->getCompanyDataRoute, []);

        $companyDataResponse->assertStatus(403);
    }
}
