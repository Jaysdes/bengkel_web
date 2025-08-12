#!/usr/bin/env python3
"""
Backend API Testing for Laravel Bengkel Web Application
Testing the API endpoints used by the transaction form
"""

import requests
import sys
import json
from datetime import datetime

class BengkelAPITester:
    def __init__(self, base_url="http://localhost:1111"):
        self.base_url = base_url
        self.api_url = f"{base_url}/api"
        self.session = requests.Session()
        self.tests_run = 0
        self.tests_passed = 0
        self.authenticated = False

    def run_test(self, name, method, endpoint, expected_status=200, data=None, headers=None):
        """Run a single API test"""
        url = f"{self.api_url}/{endpoint}" if not endpoint.startswith('http') else endpoint
        test_headers = {'Content-Type': 'application/json'}
        if headers:
            test_headers.update(headers)

        self.tests_run += 1
        print(f"\n🔍 Testing {name}...")
        print(f"   URL: {url}")
        
        try:
            if method == 'GET':
                response = self.session.get(url, headers=test_headers, timeout=10)
            elif method == 'POST':
                response = self.session.post(url, json=data, headers=test_headers, timeout=10)
            elif method == 'PUT':
                response = self.session.put(url, json=data, headers=test_headers, timeout=10)
            elif method == 'DELETE':
                response = self.session.delete(url, headers=test_headers, timeout=10)

            print(f"   Status: {response.status_code}")
            
            success = response.status_code == expected_status
            if success:
                self.tests_passed += 1
                print(f"✅ Passed - Status: {response.status_code}")
                try:
                    response_data = response.json()
                    print(f"   Response: {json.dumps(response_data, indent=2)[:200]}...")
                except:
                    print(f"   Response: {response.text[:200]}...")
            else:
                print(f"❌ Failed - Expected {expected_status}, got {response.status_code}")
                print(f"   Response: {response.text[:200]}...")

            return success, response

        except requests.exceptions.RequestException as e:
            print(f"❌ Failed - Network Error: {str(e)}")
            return False, None
        except Exception as e:
            print(f"❌ Failed - Error: {str(e)}")
            return False, None

    def test_basic_connectivity(self):
        """Test basic connectivity to the application"""
        print("\n" + "="*50)
        print("TESTING BASIC CONNECTIVITY")
        print("="*50)
        
        # Test main page
        success, response = self.run_test(
            "Main Page Access",
            "GET",
            self.base_url,
            expected_status=200
        )
        
        return success

    def test_authentication(self):
        """Test authentication endpoints"""
        print("\n" + "="*50)
        print("TESTING AUTHENTICATION")
        print("="*50)
        
        # Test login page
        success, response = self.run_test(
            "Login Page Access",
            "GET",
            f"{self.base_url}/login",
            expected_status=200
        )
        
        # Try to login with common credentials
        login_data = {
            "username": "admin",
            "password": "admin"
        }
        
        success, response = self.run_test(
            "Login Attempt",
            "POST",
            f"{self.base_url}/login",
            expected_status=302,  # Redirect on successful login
            data=login_data
        )
        
        if success and response:
            # Store session cookies
            self.authenticated = True
            print("✅ Authentication successful - session stored")
        
        return success

    def test_api_endpoints(self):
        """Test the API endpoints used by the transaction form"""
        print("\n" + "="*50)
        print("TESTING API ENDPOINTS")
        print("="*50)
        
        # List of endpoints from the JavaScript code
        endpoints = [
            ("SPK List", "GET", "spk"),
            ("Customers List", "GET", "customers"),
            ("Mekanik List", "GET", "mekanik"),
            ("Jenis Jasa List", "GET", "jenis_jasa"),
            ("Sparepart List", "GET", "sparepart"),
        ]
        
        results = []
        for name, method, endpoint in endpoints:
            success, response = self.run_test(name, method, endpoint)
            results.append((name, success))
            
        return results

    def test_transaction_endpoints(self):
        """Test transaction-related endpoints"""
        print("\n" + "="*50)
        print("TESTING TRANSACTION ENDPOINTS")
        print("="*50)
        
        # Test transaction creation endpoint
        transaction_data = {
            "id_spk": 1,
            "id_customer": 1,
            "id_jenis": 1,
            "no_kendaraan": "B1234ABC",
            "telepon": "081234567890",
            "id_mekanik": 1,
            "harga_jasa": 100000,
            "harga_sparepart": 0,
            "total": 100000,
            "jenis_service": "1"
        }
        
        success, response = self.run_test(
            "Create Transaction",
            "POST",
            "transaksi",
            expected_status=201,
            data=transaction_data
        )
        
        # Test detail transaction endpoint
        detail_data = {
            "id_transaksi": 1,
            "id_sparepart": 1,
            "qty": 1,
            "total": 50000
        }
        
        success2, response2 = self.run_test(
            "Create Transaction Detail",
            "POST",
            "detail_transaksi",
            expected_status=201,
            data=detail_data
        )
        
        return success and success2

    def test_page_access(self):
        """Test access to the transaction page"""
        print("\n" + "="*50)
        print("TESTING PAGE ACCESS")
        print("="*50)
        
        success, response = self.run_test(
            "Transaction Page Access",
            "GET",
            f"{self.base_url}/transaksi",
            expected_status=200
        )
        
        if success and response:
            # Check if the page contains expected elements
            content = response.text
            expected_elements = [
                "Transaksi Bengkel",
                "id_spk",
                "id_customer",
                "id_mekanik",
                "sparepartSection",
                "printPreview",
                "exportTransaction"
            ]
            
            found_elements = []
            for element in expected_elements:
                if element in content:
                    found_elements.append(element)
                    print(f"✅ Found: {element}")
                else:
                    print(f"❌ Missing: {element}")
            
            print(f"\nFound {len(found_elements)}/{len(expected_elements)} expected elements")
            
        return success

def main():
    """Main test execution"""
    print("🚀 Starting Laravel Bengkel Web API Tests")
    print(f"⏰ Test started at: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Initialize tester
    tester = BengkelAPITester()
    
    # Run test suites
    print("\n📋 Test Plan:")
    print("1. Basic Connectivity")
    print("2. Authentication")
    print("3. Page Access")
    print("4. API Endpoints")
    print("5. Transaction Endpoints")
    
    # Execute tests
    connectivity_ok = tester.test_basic_connectivity()
    
    if connectivity_ok:
        auth_ok = tester.test_authentication()
        page_ok = tester.test_page_access()
        api_results = tester.test_api_endpoints()
        transaction_ok = tester.test_transaction_endpoints()
    else:
        print("❌ Basic connectivity failed - skipping other tests")
        auth_ok = page_ok = transaction_ok = False
        api_results = []
    
    # Print final results
    print("\n" + "="*50)
    print("FINAL TEST RESULTS")
    print("="*50)
    print(f"📊 Tests Run: {tester.tests_run}")
    print(f"✅ Tests Passed: {tester.tests_passed}")
    print(f"❌ Tests Failed: {tester.tests_run - tester.tests_passed}")
    print(f"📈 Success Rate: {(tester.tests_passed/tester.tests_run*100):.1f}%" if tester.tests_run > 0 else "0%")
    
    print(f"\n🔗 Connectivity: {'✅ OK' if connectivity_ok else '❌ FAILED'}")
    print(f"🔐 Authentication: {'✅ OK' if auth_ok else '❌ FAILED'}")
    print(f"📄 Page Access: {'✅ OK' if page_ok else '❌ FAILED'}")
    print(f"🔌 API Endpoints: {len([r for r in api_results if r[1]])}/{len(api_results)} working")
    print(f"💰 Transaction: {'✅ OK' if transaction_ok else '❌ FAILED'}")
    
    # Recommendations
    print("\n💡 RECOMMENDATIONS:")
    if not connectivity_ok:
        print("- Check if Laravel application is running on port 1111")
        print("- Verify web server configuration")
    elif not auth_ok:
        print("- Check authentication system and credentials")
        print("- Verify session handling")
    elif not page_ok:
        print("- Check route configuration for /transaksi")
        print("- Verify middleware and permissions")
    elif len([r for r in api_results if r[1]]) == 0:
        print("- API endpoints are not responding - check API routes")
        print("- Verify API URL configuration in environment")
    else:
        print("- Basic functionality appears to be working")
        print("- Ready for frontend testing")
    
    return 0 if tester.tests_passed == tester.tests_run else 1

if __name__ == "__main__":
    sys.exit(main())