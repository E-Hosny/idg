<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class QoyodService
{
    private $baseUrl;
    private $apiKey;
    private $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.qoyod.base_url', 'https://api.qoyod.com/api/v1');
        $this->apiKey = config('services.qoyod.api_key');
        $this->timeout = config('services.qoyod.timeout', 30);
    }

    /**
     * Get all customers from Qoyod
     */
    public function getCustomers($page = 1, $perPage = 50)
    {
        try {
            $cacheKey = "qoyod_customers_page_{$page}_per_{$perPage}";
            
            return Cache::remember($cacheKey, 60, function () use ($page, $perPage) {
                $response = Http::timeout($this->timeout)
                    ->withHeaders([
                        'API-KEY' => $this->apiKey,
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ])
                    ->get("{$this->baseUrl}/customers");

                if ($response->successful()) {
                    $data = $response->json();
                    Log::info('Qoyod customers fetched successfully', [
                        'page' => $page,
                        'per_page' => $perPage,
                        'total' => count($data['customers'] ?? [])
                    ]);
                    
                    // Transform the response to match our expected format
                    return [
                        'data' => $data['customers'] ?? [],
                        'meta' => [
                            'current_page' => $page,
                            'per_page' => $perPage,
                            'total' => count($data['customers'] ?? [])
                        ]
                    ];
                }

                Log::error('Failed to fetch customers from Qoyod', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return [
                    'data' => [],
                    'meta' => [
                        'current_page' => $page,
                        'per_page' => $perPage,
                        'total' => 0
                    ]
                ];
            });
        } catch (\Exception $e) {
            Log::error('Exception while fetching customers from Qoyod', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'data' => [],
                'meta' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => 0
                ]
            ];
        }
    }

    /**
     * Get a specific customer by ID
     */
    public function getCustomer($customerId)
    {
        try {
            $cacheKey = "qoyod_customer_{$customerId}";
            
            return Cache::remember($cacheKey, 60, function () use ($customerId) {
                $response = Http::timeout($this->timeout)
                    ->withHeaders([
                        'API-KEY' => $this->apiKey,
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ])
                    ->get("{$this->baseUrl}/customers/{$customerId}");

                if ($response->successful()) {
                    $data = $response->json();
                    Log::info('Qoyod customer fetched successfully', [
                        'customer_id' => $customerId
                    ]);
                    return $data;
                }

                Log::error('Failed to fetch customer from Qoyod', [
                    'customer_id' => $customerId,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return null;
            });
        } catch (\Exception $e) {
            Log::error('Exception while fetching customer from Qoyod', [
                'customer_id' => $customerId,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Create a new customer in Qoyod
     */
    public function createCustomer($customerData)
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'API-KEY' => $this->apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/customers", [
                    'contact' => $customerData
                ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Customer created successfully in Qoyod', [
                    'customer_id' => $data['id'] ?? null,
                    'name' => $customerData['name'] ?? null
                ]);

                // Clear cache
                $this->clearCustomersCache();

                return $data;
            }

            Log::error('Failed to create customer in Qoyod', [
                'status' => $response->status(),
                'response' => $response->body(),
                'data' => $customerData
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Exception while creating customer in Qoyod', [
                'error' => $e->getMessage(),
                'data' => $customerData
            ]);

            return null;
        }
    }

    /**
     * Update a customer in Qoyod
     */
    public function updateCustomer($customerId, $customerData)
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'API-KEY' => $this->apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->put("{$this->baseUrl}/customers/{$customerId}", [
                    'contact' => $customerData
                ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Customer updated successfully in Qoyod', [
                    'customer_id' => $customerId
                ]);

                // Clear cache
                $this->clearCustomersCache();
                Cache::forget("qoyod_customer_{$customerId}");

                return $data;
            }

            Log::error('Failed to update customer in Qoyod', [
                'customer_id' => $customerId,
                'status' => $response->status(),
                'response' => $response->body(),
                'data' => $customerData
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Exception while updating customer in Qoyod', [
                'customer_id' => $customerId,
                'error' => $e->getMessage(),
                'data' => $customerData
            ]);

            return null;
        }
    }

    /**
     * Delete a customer from Qoyod
     */
    public function deleteCustomer($customerId)
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'API-KEY' => $this->apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->delete("{$this->baseUrl}/customers/{$customerId}");

            if ($response->successful()) {
                Log::info('Customer deleted successfully from Qoyod', [
                    'customer_id' => $customerId
                ]);

                // Clear cache
                $this->clearCustomersCache();
                Cache::forget("qoyod_customer_{$customerId}");

                return true;
            }

            Log::error('Failed to delete customer from Qoyod', [
                'customer_id' => $customerId,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Exception while deleting customer from Qoyod', [
                'customer_id' => $customerId,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Test the connection to Qoyod API
     */
    public function testConnection()
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'API-KEY' => $this->apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->get("{$this->baseUrl}/customers");

            if ($response->successful()) {
                Log::info('Qoyod API connection test successful');
                return [
                    'success' => true,
                    'message' => 'Connection successful',
                    'status' => $response->status()
                ];
            }

            Log::error('Qoyod API connection test failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return [
                'success' => false,
                'message' => 'Connection failed',
                'status' => $response->status(),
                'error' => $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('Exception during Qoyod API connection test', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Connection failed',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Clear customers cache
     */
    private function clearCustomersCache()
    {
        // Clear all cache for now (simple approach)
        Cache::flush();
    }

    /**
     * Get invoices for a specific customer
     */
    public function getCustomerInvoices($customerId, $page = 1, $perPage = 50)
    {
        try {
            $cacheKey = "qoyod_customer_{$customerId}_invoices_page_{$page}_per_{$perPage}";
            
            return Cache::remember($cacheKey, 60, function () use ($customerId, $page, $perPage) {
                $response = Http::timeout($this->timeout)
                    ->withHeaders([
                        'API-KEY' => $this->apiKey,
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ])
                    ->get("{$this->baseUrl}/invoices", [
                        'customer_id' => $customerId,
                        'page' => $page,
                        'per_page' => $perPage,
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    Log::info('Qoyod customer invoices fetched successfully', [
                        'customer_id' => $customerId,
                        'page' => $page,
                        'per_page' => $perPage,
                        'total' => $data['meta']['total'] ?? 0
                    ]);
                    return $data;
                }

                Log::error('Failed to fetch customer invoices from Qoyod', [
                    'customer_id' => $customerId,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return [
                    'data' => [],
                    'meta' => [
                        'current_page' => $page,
                        'per_page' => $perPage,
                        'total' => 0
                    ]
                ];
            });
        } catch (\Exception $e) {
            Log::error('Exception while fetching customer invoices from Qoyod', [
                'customer_id' => $customerId,
                'error' => $e->getMessage()
            ]);

            return [
                'data' => [],
                'meta' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => 0
                ]
            ];
        }
    }
}
