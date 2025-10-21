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
                    
                    // Extract contact data from response
                    return $data['contact'] ?? $data;
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
            Log::info('Sending customer data to Qoyod', [
                'data' => $customerData
            ]);
            
            // Format address data for Qoyod
            $formattedData = $this->formatCustomerDataForQoyod($customerData);
            
            Log::info('Formatted customer data for Qoyod', [
                'formatted_data' => $formattedData
            ]);
            
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'API-KEY' => $this->apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/customers", [
                    'contact' => $formattedData
                ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Customer created successfully in Qoyod', [
                    'customer_id' => $data['id'] ?? null,
                    'name' => $customerData['name'] ?? null,
                    'response_data' => $data
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
            // Format address data for Qoyod
            $formattedData = $this->formatCustomerDataForQoyod($customerData);
            
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'API-KEY' => $this->apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->put("{$this->baseUrl}/customers/{$customerId}", [
                    'contact' => $formattedData
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

            // Handle 404 as success (customer might already be deleted)
            if ($response->status() === 404) {
                Log::info('Customer not found in Qoyod (already deleted)', [
                    'customer_id' => $customerId
                ]);
                
                // Clear cache anyway
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
     * Format customer data for Qoyod API
     */
    private function formatCustomerDataForQoyod($customerData)
    {
        $formatted = [
            'name' => $customerData['name'] ?? null,
            'organization' => $customerData['organization'] ?? null,
            'email' => $customerData['email'] ?? null,
            'secondary_email' => $customerData['secondary_email'] ?? null,
            'phone_number' => $customerData['phone_number'] ?? null,
            'secondary_phone_number' => $customerData['secondary_phone_number'] ?? null,
            'website' => $customerData['website'] ?? null,
            'currency' => $customerData['currency'] ?? null,
            'tax_number' => $customerData['tax_number'] ?? null,
            'commercial_registration_number' => $customerData['commercial_registration_number'] ?? null,
            'tax_subject' => $customerData['tax_subject'] ?? false,
            'pos_customer' => $customerData['pos_customer'] ?? false,
            'government_entity_customer' => $customerData['government_entity_customer'] ?? false,
            'status' => $customerData['status'] ?? 'Active',
            'credit_limit' => $customerData['credit_limit'] ?? null,
            'pos' => $customerData['pos'] ?? false,
            'government_entity' => $customerData['government_entity'] ?? false,
            'allow_credit' => $customerData['allow_credit'] ?? false,
            'notes' => $customerData['notes'] ?? null,
        ];

        // Use the correct format from Qoyod support: billing_address_attributes and shipping_address_attributes
        if (!empty($customerData['billing_address']) || !empty($customerData['billing_city']) || !empty($customerData['billing_state']) || !empty($customerData['billing_postal_code']) || !empty($customerData['billing_country'])) {
            $formatted['billing_address_attributes'] = [
                'billing_address' => $customerData['billing_address'] ?? null,
                'billing_city' => $customerData['billing_city'] ?? null,
                'billing_state' => $customerData['billing_state'] ?? null,
                'billing_zip' => $customerData['billing_postal_code'] ?? null,
                'billing_country' => $customerData['billing_country'] ?? null,
            ];
        }

        if (!empty($customerData['shipping_address']) || !empty($customerData['shipping_city']) || !empty($customerData['shipping_state']) || !empty($customerData['shipping_postal_code']) || !empty($customerData['shipping_country'])) {
            $formatted['shipping_address_attributes'] = [
                'shipping_address' => $customerData['shipping_address'] ?? null,
                'shipping_city' => $customerData['shipping_city'] ?? null,
                'shipping_state' => $customerData['shipping_state'] ?? null,
                'shipping_zip' => $customerData['shipping_postal_code'] ?? null,
                'shipping_country' => $customerData['shipping_country'] ?? null,
            ];
        }

        // Use the correct format from Qoyod support: customer_details as indexed array
        // Only send numeric fields in customer_details, addresses go in address_attributes
        $customerDetails = [];
        
        // Add commercial registration number (numeric only, max 10 digits)
        if (!empty($customerData['commercial_registration_number'])) {
            $customerDetails[] = [
                'name' => 'cr_number',
                'value' => $customerData['commercial_registration_number']
            ];
        }
        
        // Note: tax_number should be sent as a direct field, not in customer_details
        // because customer_details only accepts up to 10 digits, but tax_number requires 15 digits
        
        if (!empty($customerDetails)) {
            $formatted['customer_details'] = $customerDetails;
        }

        return $formatted;
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

    /**
     * Get all invoices from Qoyod
     */
    public function getInvoices($page = 1, $perPage = 50)
    {
        try {
            $cacheKey = "qoyod_invoices_page_{$page}_per_{$perPage}";
            
            return Cache::remember($cacheKey, 60, function () use ($page, $perPage) {
                $response = Http::timeout($this->timeout)
                    ->withHeaders([
                        'API-KEY' => $this->apiKey,
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ])
                    ->get("{$this->baseUrl}/invoices", [
                        'page' => $page,
                        'per_page' => $perPage,
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    Log::info('Qoyod invoices fetched successfully', [
                        'page' => $page,
                        'per_page' => $perPage,
                        'response_structure' => array_keys($data),
                        'invoices_count' => count($data['invoices'] ?? []),
                        'data_count' => count($data['data'] ?? [])
                    ]);
                    return $data;
                }

                Log::error('Failed to fetch invoices from Qoyod', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return [
                    'invoices' => [],
                    'data' => [],
                    'meta' => [
                        'current_page' => $page,
                        'per_page' => $perPage,
                        'total' => 0
                    ]
                ];
            });
        } catch (\Exception $e) {
            Log::error('Exception while fetching invoices from Qoyod', [
                'error' => $e->getMessage()
            ]);

            return [
                'invoices' => [],
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
     * Get a specific invoice by ID from Qoyod
     */
    public function getInvoice($invoiceId)
    {
        try {
            $cacheKey = "qoyod_invoice_{$invoiceId}";
            
            return Cache::remember($cacheKey, 300, function () use ($invoiceId) {
                $response = Http::timeout($this->timeout)
                    ->withHeaders([
                        'API-KEY' => $this->apiKey,
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ])
                    ->get("{$this->baseUrl}/invoices/{$invoiceId}");

                if ($response->successful()) {
                    $data = $response->json();
                    Log::info('Qoyod invoice fetched successfully', [
                        'invoice_id' => $invoiceId,
                        'invoice_number' => $data['invoice']['reference'] ?? null
                    ]);
                    return $data;
                }

                Log::error('Failed to fetch invoice from Qoyod', [
                    'invoice_id' => $invoiceId,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return [
                    'success' => false,
                    'message' => 'Invoice not found',
                    'status' => $response->status()
                ];
            });
        } catch (\Exception $e) {
            Log::error('Exception while fetching invoice from Qoyod', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Exception occurred while fetching invoice',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate next invoice number based on existing invoices in Qoyod
     */
    public function generateNextInvoiceNumber()
    {
        try {
            // Get all invoices to find the highest number
            $invoices = $this->getInvoices(1, 1000); // Get more invoices to ensure we get all
            
            $highestNumber = 0;
            
            // Look through all invoices to find the highest INV number
            foreach ($invoices['invoices'] ?? $invoices['data'] ?? [] as $invoice) {
                $reference = $invoice['reference'] ?? '';
                
                // Match INVXXXX pattern
                if (preg_match('/^INV(\d{4})$/', $reference, $matches)) {
                    $number = (int)$matches[1];
                    $highestNumber = max($highestNumber, $number);
                }
            }
            
            // Generate next number
            $nextNumber = $highestNumber + 1;
            $invoiceNumber = 'INV' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            
            Log::info('Generated new invoice number', [
                'highest_existing' => $highestNumber,
                'new_number' => $invoiceNumber
            ]);
            
            return $invoiceNumber;
            
        } catch (\Exception $e) {
            Log::error('Error generating invoice number', [
                'error' => $e->getMessage()
            ]);
            
            // Fallback to timestamp-based number if API fails
            return 'INV' . str_pad(time() % 10000, 4, '0', STR_PAD_LEFT);
        }
    }

    /**
     * Create an invoice in Qoyod
     */
    public function createInvoice($invoiceData)
    {
        try {
            // Generate invoice number if not provided
            if (empty($invoiceData['invoice']['reference'])) {
                $invoiceData['invoice']['reference'] = $this->generateNextInvoiceNumber();
            }
            
            Log::info('Creating invoice in Qoyod', [
                'invoice_data' => $invoiceData,
                'generated_reference' => $invoiceData['invoice']['reference']
            ]);

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'API-KEY' => $this->apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/invoices", $invoiceData);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Invoice created successfully in Qoyod', [
                    'invoice_id' => $data['invoice']['id'] ?? null,
                    'reference' => $data['invoice']['reference'] ?? $invoiceData['invoice']['reference']
                ]);

                return $data;
            }

            Log::error('Failed to create invoice in Qoyod', [
                'status' => $response->status(),
                'response' => $response->body(),
                'data' => $invoiceData
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create invoice',
                'status' => $response->status(),
                'response' => $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('Exception while creating invoice in Qoyod', [
                'error' => $e->getMessage(),
                'data' => $invoiceData
            ]);

            return [
                'success' => false,
                'message' => 'Exception occurred while creating invoice',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get invoice PDF from Qoyod
     */
    public function getInvoicePdf($invoiceId)
    {
        try {
            Log::info('Fetching invoice PDF from Qoyod', ['invoice_id' => $invoiceId]);

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'API-KEY' => $this->apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->get("{$this->baseUrl}/invoices/{$invoiceId}/pdf");

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Invoice PDF fetched successfully from Qoyod', [
                    'invoice_id' => $invoiceId,
                    'pdf_url' => $data['pdf_file'] ?? null
                ]);

                return $data;
            }

            Log::error('Failed to fetch invoice PDF from Qoyod', [
                'invoice_id' => $invoiceId,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to get invoice PDF',
                'status' => $response->status()
            ];
        } catch (\Exception $e) {
            Log::error('Exception while fetching invoice PDF from Qoyod', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Exception occurred while fetching invoice PDF',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Delete an invoice from Qoyod
     */
    public function deleteInvoice($invoiceId)
    {
        try {
            Log::info('Deleting invoice from Qoyod', ['invoice_id' => $invoiceId]);

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'API-KEY' => $this->apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->delete("{$this->baseUrl}/invoices/{$invoiceId}");

            if ($response->successful()) {
                Log::info('Invoice deleted successfully from Qoyod', [
                    'invoice_id' => $invoiceId
                ]);

                return [
                    'success' => true,
                    'message' => 'Invoice deleted successfully'
                ];
            }

            Log::error('Failed to delete invoice from Qoyod', [
                'invoice_id' => $invoiceId,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to delete invoice',
                'status' => $response->status()
            ];
        } catch (\Exception $e) {
            Log::error('Exception while deleting invoice from Qoyod', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Exception occurred while deleting invoice',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Create a quote in Qoyod
     */
    public function createQuote($quoteData)
    {
        try {
            Log::info('Creating quote in Qoyod', ['quote_data' => $quoteData]);

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'API-KEY' => $this->apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/quotes", $quoteData);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Quote created successfully in Qoyod', [
                    'quote_id' => $data['quote']['id'] ?? null,
                    'quotation_number' => $quoteData['quote']['quotation_number'] ?? null
                ]);

                return $data;
            }

            Log::error('Failed to create quote in Qoyod', [
                'status' => $response->status(),
                'response' => $response->body(),
                'data' => $quoteData
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create quote',
                'status' => $response->status(),
                'error' => $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('Exception while creating quote in Qoyod', [
                'error' => $e->getMessage(),
                'data' => $quoteData
            ]);

            return [
                'success' => false,
                'message' => 'Exception occurred while creating quote',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get products from Qoyod for line items
     */
    public function getProducts($page = 1, $perPage = 100)
    {
        try {
            $cacheKey = "qoyod_products_page_{$page}_per_{$perPage}";
            
            return Cache::remember($cacheKey, 300, function () use ($page, $perPage) {
                $response = Http::timeout($this->timeout)
                    ->withHeaders([
                        'API-KEY' => $this->apiKey,
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ])
                    ->get("{$this->baseUrl}/products", [
                        'page' => $page,
                        'per_page' => $perPage,
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    Log::info('Qoyod products fetched successfully', [
                        'page' => $page,
                        'per_page' => $perPage,
                        'total' => count($data['products'] ?? [])
                    ]);
                    
                    // Transform products to include both Arabic and English names
                    $products = [];
                    foreach ($data['products'] ?? [] as $product) {
                        // Get local pricing data
                        $localPrice = $this->getLocalPricingForProduct($product);
                        
                        $products[] = [
                            'id' => $product['id'],
                            'name' => $product['name_en'] ?? $product['name_ar'] ?? 'Unknown Product',
                            'name_ar' => $product['name_ar'] ?? 'منتج غير معروف',
                            'name_en' => $product['name_en'] ?? 'Unknown Product',
                            'description' => $product['description'] ?? '',
                            'category_id' => $product['category_id'] ?? null,
                            'type' => $product['type'] ?? 'Product',
                            'unit_type' => $product['unit_type'] ?? null,
                            'unit' => $product['unit'] ?? '',
                            'price' => $localPrice !== null ? $localPrice : ($product['price'] ?? 0),
                            'original_qoyod_price' => $product['price'] ?? 0,
                            'is_local_pricing' => $localPrice !== null,
                            'created_at' => $product['created_at'] ?? null,
                            'updated_at' => $product['updated_at'] ?? null
                        ];
                    }
                    
                    return [
                        'products' => $products,
                        'meta' => [
                            'current_page' => $page,
                            'per_page' => $perPage,
                            'total' => count($products)
                        ]
                    ];
                }

                Log::error('Failed to fetch products from Qoyod', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return [
                    'products' => [],
                    'meta' => [
                        'current_page' => $page,
                        'per_page' => $perPage,
                        'total' => 0
                    ]
                ];
            });
        } catch (\Exception $e) {
            Log::error('Exception while fetching products from Qoyod', [
                'error' => $e->getMessage()
            ]);

            return [
                'products' => [],
                'meta' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => 0
                ]
            ];
        }
    }

    /**
     * Get locations/inventories from Qoyod using inventories endpoint
     */
    public function getLocations()
    {
        try {
            $cacheKey = "qoyod_inventories";
            
            return Cache::remember($cacheKey, 300, function () {
                $response = Http::timeout($this->timeout)
                    ->withHeaders([
                        'API-KEY' => $this->apiKey,
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ])
                    ->get("{$this->baseUrl}/inventories");

                if ($response->successful()) {
                    $data = $response->json();
                    Log::info('Qoyod inventories fetched successfully', [
                        'total' => count($data['inventories'] ?? [])
                    ]);
                    
                    // Transform the data to include both Arabic and English names
                    $locations = [];
                    foreach ($data['inventories'] ?? [] as $inventory) {
                        $locations[] = [
                            'id' => $inventory['id'],
                            'name' => $inventory['ar_name'] ?? $inventory['name'] ?? 'Unknown Location',
                            'name_en' => $inventory['name'] ?? $inventory['ar_name'] ?? 'Unknown Location',
                            'ar_name' => $inventory['ar_name'] ?? 'موقع غير معروف',
                            'address' => $inventory['address'] ?? null,
                            'account_id' => $inventory['account_id'] ?? null
                        ];
                    }
                    
                    return $locations;
                }

                Log::error('Failed to fetch inventories from Qoyod', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                // Return mock data if API fails
                return [
                    [
                        'id' => 1,
                        'name' => 'الادارة العامة',
                        'name_en' => 'General Administration',
                        'ar_name' => 'الادارة العامة',
                        'reference' => 'ADMIN'
                    ],
                    [
                        'id' => 2,
                        'name' => 'ادارة التشغيل', 
                        'name_en' => 'Operations Management',
                        'ar_name' => 'ادارة التشغيل',
                        'reference' => 'OPS'
                    ]
                ];
            });
        } catch (\Exception $e) {
            Log::error('Exception while fetching inventories from Qoyod', [
                'error' => $e->getMessage()
            ]);

            // Return mock data on exception
            return [
                [
                    'id' => 1,
                    'name' => 'الادارة العامة',
                    'name_en' => 'General Administration', 
                    'ar_name' => 'الادارة العامة',
                    'reference' => 'ADMIN'
                ],
                [
                    'id' => 2,
                    'name' => 'ادارة التشغيل',
                    'name_en' => 'Operations Management',
                    'ar_name' => 'ادارة التشغيل', 
                    'reference' => 'OPS'
                ]
            ];
        }
    }

    /**
     * Get warehouses from Qoyod
     */
    public function getWarehouses()
    {
        return $this->getLocations(); // Same as locations for now
    }

    /**
     * Get local pricing data for a product based on product name analysis
     */
    private function getLocalPricingForProduct($product)
    {
        try {
            // Load pricing data from JSON file
            $pricingData = json_decode(file_get_contents(base_path('pricing_data.json')), true);
            
            if (!$pricingData) {
                return null;
            }

            $productName = $product['name_ar'] ?? $product['name_en'] ?? '';
            
            Log::info('Checking local pricing for product', [
                'product_name' => $productName,
                'product_id' => $product['id'] ?? 'unknown'
            ]);
            
            // Parse product name to extract artifact type, service type, and weight range
            if (preg_match('/الأحجار الكريمة الملونة\s*\((.*?)\)/', $productName, $matches)) {
                $artifactType = 'Colored Gemstones';
                $weightRange = $matches[1];
                
                // Determine service type from Arabic name
                $serviceType = null;
                if (strpos($productName, 'تقرير كبير') !== false && strpos($productName, 'المنشأ') === false) {
                    $serviceType = 'Regular - ID Report';
                } elseif (strpos($productName, 'تقرير') !== false && strpos($productName, 'المنشأ') !== false) {
                    $serviceType = 'Regular - ID + Origin';
                } elseif (strpos($productName, 'بطاقة') !== false && strpos($productName, 'المنشأ') === false) {
                    $serviceType = 'Mini Card Report - ID Report';
                } elseif (strpos($productName, 'بطاقة') !== false && strpos($productName, 'المنشأ') !== false) {
                    $serviceType = 'Mini Card Report - ID + Origin';
                }
                
                // Parse weight range
                if (strpos($weightRange, '-') !== false) {
                    preg_match('/(\d+\.?\d*)\s*-\s*(\d+\.?\d*)/', $weightRange, $weightMatches);
                    if (count($weightMatches) >= 3) {
                        $minWeight = (float)$weightMatches[1];
                        $maxWeight = (float)$weightMatches[2];
                        
                        // Find matching pricing entry
                        Log::info('Looking for pricing match', [
                            'artifact_type' => $artifactType,
                            'service_type' => $serviceType,
                            'min_weight' => $maxWeight, // Note: swap order in Arabic product names
                            'max_weight' => $minWeight
                        ]);
                        
                        foreach ($pricingData as $pricing) {
                            if ($pricing['artifact_type'] === $artifactType && 
                                $pricing['service_type'] === $serviceType &&
                                $pricing['min_weight'] == $maxWeight && // Note: swap order in Arabic product names
                                $pricing['max_weight'] == $minWeight) {
                                Log::info('Found pricing match', [
                                    'price' => $pricing['price'],
                                    'pricing_id' => $pricing['id']
                                ]);
                                return (float)$pricing['price'];
                            }
                        }
                    }
                } elseif (preg_match('/(\d+\.?\d*)/', $weightRange, $singleWeight)) {
                    $weight = (float)$singleWeight[1];
                    
                    // Find matching pricing entry for single weight
                    foreach ($pricingData as $pricing) {
                        if ($pricing['artifact_type'] === $artifactType && 
                            $pricing['service_type'] === $serviceType) {
                            // Check if weight falls within range
                            if (($pricing['min_weight'] <= $weight) &&
                                ($pricing['max_weight'] === null || $pricing['max_weight'] >= $weight)) {
                                return (float)$pricing['price'];
                            }
                        }
                    }
                }
            }
            
            // Check for Other Colored Gemstones
            if (strpos($productName, 'الأحجار الكريمة الملونة الأخرى') !== false) {
                $artifactType = 'Other Colored Gemstones';
                preg_match('/\((.*?)\)/', $productName, $weightMatches);
                if (count($weightMatches) >= 2) {
                    $weightRange = $weightMatches[1];
                    
                    $serviceType = strpos($productName, 'تقرير كبير') !== false ? 'Regular - ID Report' : 'Mini Card Report - ID Report';
                    
                    if (strpos($weightRange, '-') !== false) {
                        preg_match('/(\d+\.?\d*)\s*-\s*(\d+\.?\d*)/', $weightRange, $rangeMatches);
                        if (count($rangeMatches) >= 3) {
                            $minWeight = (float)$rangeMatches[1];
                            $maxWeight = (float)$rangeMatches[2];
                            
                            foreach ($pricingData as $pricing) {
                                if ($pricing['artifact_type'] === $artifactType && 
                                    $pricing['service_type'] === $serviceType &&
                                    $pricing['min_weight'] == $maxWeight && // Note: swap order in Arabic product names
                                    $pricing['max_weight'] == $minWeight) {
                                    return (float)$pricing['price'];
                                }
                            }
                        }
                    }
                }
            }
            
            // Check for Colorless Diamonds
            if (strpos($productName, 'الألماس عديم اللون') !== false) {
                $artifactType = 'Colorless Diamonds';
                $serviceType = strpos($productName, 'تقرير كبير') !== false ? 'Regular - Diamond Grading Report' : 'Mini Card Report - Mini Report';
                
                preg_match('/\((.*?)\)/', $productName, $weightMatches);
                
                foreach ($pricingData as $pricing) {
                    if ($pricing['artifact_type'] === $artifactType && 
                        $pricing['service_type'] === $serviceType) {
                        
                        if (strpos($productName, '-') !== false) {
                            preg_match('/(\d+\.?\d*)\s*-\s*(\d+\.?\d*)/', $productName, $rangeMatches);
                            if (count($rangeMatches) >= 3) {
                                $minWeight = (float)$rangeMatches[1];
                                $maxWeight = (float)$rangeMatches[2];
                                
                                if ($pricing['min_weight'] == $minWeight && $pricing['max_weight'] == $maxWeight) {
                                    return (float)$pricing['price'];
                                }
                            }
                        }
                    }
                }
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Error getting local pricing for product', [
                'product' => $product['name_ar'] ?? $product['name_en'] ?? 'Unknown',
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get a quote by ID from Qoyod
     */
    public function getQuote($quoteId)
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'API-KEY' => $this->apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->get("{$this->baseUrl}/quotes/{$quoteId}");

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Qoyod quote fetched successfully', [
                    'quote_id' => $quoteId,
                    'status' => $data['quote']['status'] ?? 'Unknown'
                ]);
                
                return $data['quote'];
            }

            Log::error('Failed to fetch quote from Qoyod', [
                'quote_id' => $quoteId,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Exception while fetching quote from Qoyod', [
                'quote_id' => $quoteId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get all quotes from Qoyod
     */
    public function getQuotes($page = 1, $perPage = 50)
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'API-KEY' => $this->apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->get("{$this->baseUrl}/quotes", [
                    'page' => $page,
                    'per_page' => $perPage,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Qoyod quotes fetched successfully', [
                    'page' => $page,
                    'per_page' => $perPage,
                    'total' => count($data['quote'] ?? [])
                ]);
                
                return [
                    'quotes' => $data['quote'] ?? [],
                    'meta' => $data['meta'] ?? [
                        'current_page' => $page,
                        'per_page' => $perPage,
                        'total' => count($data['quote'] ?? [])
                    ]
                ];
            }

            Log::error('Failed to fetch quotes from Qoyod', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return [
                'quotes' => [],
                'meta' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => 0
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Exception while fetching quotes from Qoyod', [
                'error' => $e->getMessage()
            ]);

            return [
                'quotes' => [],
                'meta' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => 0
                ]
            ];
        }
    }

    /**
     * Download PDF for a quote from Qoyod
     */
    public function downloadQuotePDF($quoteId)
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'API-KEY' => $this->apiKey,
                    'Accept' => 'application/pdf',
                    'Content-Type' => 'application/json',
                ])
                ->get("{$this->baseUrl}/quotes/{$quoteId}/pdf");

            if ($response->successful()) {
                Log::info('Qoyod quote PDF downloaded successfully', [
                    'quote_id' => $quoteId,
                    'content_length' => strlen($response->body())
                ]);
                
                return [
                    'success' => true,
                    'pdf_content' => $response->body(),
                    'filename' => "quote-{$quoteId}.pdf",
                    'mime_type' => 'application/pdf'
                ];
            }

            Log::error('Failed to download quote PDF from Qoyod', [
                'quote_id' => $quoteId,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return [
                'success' => false,
                'error' => 'Failed to download PDF from Qoyod',
                'status' => $response->status()
            ];
        } catch (\Exception $e) {
            Log::error('Exception while downloading quote PDF from Qoyod', [
                'quote_id' => $quoteId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get quote export URL from Qoyod (alternative method)
     */
    public function getQuoteExportUrl($quoteId)
    {
        try {
            // Try to get the export URL for the quote
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'API-KEY' => $this->apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->get("{$this->baseUrl}/quotes/{$quoteId}/export");

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Qoyod quote export URL retrieved successfully', [
                    'quote_id' => $quoteId,
                    'export_data' => $data
                ]);
                
                return [
                    'success' => true,
                    'export_url' => $data['export_url'] ?? $data['download_url'] ?? null,
                    'data' => $data
                ];
            }

            Log::warning('No export URL found for quote', [
                'quote_id' => $quoteId,
                'status' => $response->status()
            ]);

            return [
                'success' => false,
                'error' => 'No export URL available',
                'status' => $response->status()
            ];
        } catch (\Exception $e) {
            Log::error('Exception while getting quote export URL from Qoyod', [
                'quote_id' => $quoteId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
